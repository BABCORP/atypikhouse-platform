<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Review;
use App\Services\MailService;

final class BookingController extends Controller
{
    public function start(int $propertyId): void
    {
        verify_csrf();
        $property = (new Property())->find($propertyId);
        if (!$property || $property['status'] !== 'published') {
            flash('error', 'Ce logement n’est pas disponible à la réservation.');
            $this->redirect('/hebergements');
        }
        $user = Auth::user();
        if (!$user) {
            flash('error', 'Connectez-vous avec un compte locataire pour réserver ce logement.');
            $this->redirect('/connexion');
        }
        if ($user['role'] === 'owner') {
            flash('error', 'Les comptes propriétaires ne peuvent pas effectuer de réservation. Utilisez un compte locataire pour ce parcours.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        if ($user['role'] === 'admin') {
            flash('error', 'Les comptes administrateurs ne peuvent pas effectuer de réservation. Utilisez un compte locataire pour ce parcours.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        if ($user['role'] !== 'tenant') {
            flash('error', 'Connectez-vous avec un compte locataire pour réserver ce logement.');
            $this->redirect('/connexion');
        }
        if ($user['status'] !== 'active') {
            flash('error', 'Votre compte locataire doit être validé par l’administrateur avant de réserver.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        if (!valid_date((string) input('start_date')) || !valid_date((string) input('end_date')) || nights_between((string) input('start_date'), (string) input('end_date')) < 1) {
            flash('error', 'Veuillez sélectionner des dates valides.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        if ((string) input('start_date') < date('Y-m-d')) {
            flash('error', 'Veuillez sélectionner des dates valides.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        if ((int) input('guests_count') < 1 || (int) input('guests_count') > (int) $property['capacity']) {
            flash('error', 'Le nombre de voyageurs dépasse la capacité du logement.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        $bookingModel = new Booking();
        if (!$bookingModel->isAvailable((int) $property['id'], (string) input('start_date'), (string) input('end_date'))) {
            flash('error', 'Ce logement n’est pas disponible sur les dates sélectionnées.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        $bookingId = $bookingModel->create($property, (int) $user['id'], $_POST);
        audit((int) $user['id'], 'booking_created_pending_admin', 'booking', $bookingId);
        $booking = $bookingModel->findForAdmin($bookingId) ?? [
            'tenant_email' => $user['email'],
            'tenant_first_name' => $user['first_name'],
            'title' => $property['title'],
            'start_date' => input('start_date'),
            'end_date' => input('end_date'),
            'guests_count' => input('guests_count'),
        ];
        $emailSent = (new MailService())->sendBookingPendingNotification((string) $user['email'], (string) $property['title'], $booking);
        audit((int) $user['id'], $emailSent ? 'email_booking_created_sent' : 'email_booking_created_failed', 'booking', $bookingId);
        flash('success', 'Votre demande de réservation a été transmise. Elle sera confirmée après validation par l’administrateur, puis paiement fictif.');
        $this->redirect('/locataire/reservations/' . $bookingId);
    }

    public function payment(int $bookingId): void
    {
        $user = $this->requireTenantForBooking();
        $booking = (new Booking())->findForTenant($bookingId, (int) $user['id']);
        if (!$booking) {
            http_response_code(404);
            exit('Réservation introuvable.');
        }
        if ($booking['status'] === 'pending_admin') {
            flash('error', 'Cette réservation attend la validation de l’administrateur avant paiement fictif.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        if ($booking['status'] !== 'pending_payment' || $booking['payment_status'] === 'test_paid') {
            flash('error', 'Cette réservation ne peut pas être payée à ce stade.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        $this->view('dashboard/payment', ['title' => 'Paiement fictif', 'booking' => $booking]);
    }

    public function simulatePayment(int $bookingId): void
    {
        $user = $this->requireTenantForBooking();
        verify_csrf();
        $booking = (new Booking())->findForTenant($bookingId, (int) $user['id']);
        if (!$booking) {
            http_response_code(404);
            exit('Réservation introuvable.');
        }
        if ($booking['status'] === 'pending_admin') {
            flash('error', 'Cette réservation attend la validation de l’administrateur avant paiement fictif.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        if ($booking['status'] !== 'pending_payment' || $booking['payment_status'] === 'test_paid') {
            flash('error', 'Cette réservation ne peut pas être payée à ce stade.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        $success = input('scenario') === 'success';
        $success = (new Booking())->simulatePayment($bookingId, $success);
        audit((int) $user['id'], $success ? 'fake_payment_succeeded' : 'fake_payment_failed', 'booking', $bookingId);
        if ($success) {
            audit((int) $user['id'], 'booking_confirmed_after_payment', 'booking', $bookingId);
            $bookingAfterPayment = (new Booking())->findForAdmin($bookingId);
            if ($bookingAfterPayment) {
                $emailSent = (new MailService())->sendFakePaymentConfirmedNotification($bookingAfterPayment);
                audit((int) $user['id'], $emailSent ? 'email_booking_payment_confirmed_sent' : 'email_booking_payment_confirmed_failed', 'booking', $bookingId);
            }
        }
        flash($success ? 'success' : 'error', $success ? 'Paiement fictif validé. Votre réservation est maintenant confirmée.' : 'Le paiement fictif a échoué. Vous pouvez réessayer.');
        $this->redirect('/locataire/reservations/' . $bookingId);
    }

    public function review(int $bookingId): void
    {
        $user = $this->requireTenantForBooking();
        verify_csrf();
        $booking = (new Booking())->findForTenant($bookingId, (int) $user['id']);
        if (!$booking || !(new Review())->canReview($bookingId, (int) $user['id'])) {
            flash('error', 'Vous ne pouvez pas déposer d’avis pour cette réservation.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        $rating = (int) input('rating');
        $comment = trim((string) input('comment'));
        if ($rating < 1 || $rating > 5 || $comment === '') {
            flash('error', 'Merci de fournir une note de 1 à 5 et un commentaire.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        (new Review())->create($booking, $rating, $comment);
        audit((int) $user['id'], 'review_submit', 'review');
        flash('success', 'Votre avis est en attente de modération.');
        $this->redirect('/locataire/avis');
    }

    private function requireTenantForBooking(): array
    {
        $user = Auth::user();
        if (!$user) {
            flash('error', 'Connectez-vous avec un compte locataire pour réserver ce logement.');
            $this->redirect('/connexion');
        }
        if ($user['role'] === 'owner') {
            flash('error', 'Les comptes propriétaires ne peuvent pas effectuer de réservation. Utilisez un compte locataire pour ce parcours.');
            $this->redirect('/hebergements');
        }
        if ($user['role'] === 'admin') {
            flash('error', 'Les comptes administrateurs ne peuvent pas effectuer de réservation. Utilisez un compte locataire pour ce parcours.');
            $this->redirect('/hebergements');
        }
        if ($user['role'] !== 'tenant') {
            flash('error', 'Connectez-vous avec un compte locataire pour réserver ce logement.');
            $this->redirect('/connexion');
        }
        if ($user['status'] !== 'active') {
            flash('error', 'Votre compte locataire doit être validé par l’administrateur avant d’utiliser ce parcours.');
            $this->redirect('/hebergements');
        }

        return $user;
    }
}
