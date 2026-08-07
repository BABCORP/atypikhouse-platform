<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Review;
use App\Services\MailService;
use App\Services\StripeService;

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
            flash('error', 'Votre compte locataire doit être actif pour réserver ce logement.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        if (!valid_date((string) input('start_date')) || !valid_date((string) input('end_date')) || nights_between((string) input('start_date'), (string) input('end_date')) < 1) {
            flash('error', 'La date de départ doit être postérieure à la date d’arrivée.');
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
        audit((int) $user['id'], 'booking_created_pending_payment', 'booking', $bookingId);
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
        flash('success', 'Votre réservation fictive a été créée. Vous pouvez poursuivre vers le paiement de démonstration.');
        $this->redirect('/paiement/' . $bookingId);
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
            (new Booking())->validateForPayment($bookingId);
            $booking = (new Booking())->findForTenant($bookingId, (int) $user['id']);
            if (!$booking) {
                http_response_code(404);
                exit('Réservation introuvable.');
            }
        }
        if ($booking['status'] !== 'pending_payment' || $booking['payment_status'] === 'test_paid') {
            flash('error', 'Cette réservation ne peut pas être payée à ce stade.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        if ((float) $booking['total_price'] <= 0) {
            flash('error', 'Le montant de cette réservation est invalide. Merci de contacter l’équipe AtypikHouse.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        audit((int) $user['id'], 'fake_payment_started', 'booking', $bookingId);
        $this->view('dashboard/payment', [
            'title' => 'Paiement fictif',
            'booking' => $booking,
            'stripeReady' => (new StripeService())->isReady(),
        ]);
    }

    public function startStripePayment(int $bookingId): void
    {
        $user = $this->requireTenantForBooking();
        verify_csrf();
        $booking = (new Booking())->findForTenant($bookingId, (int) $user['id']);
        if (!$booking || $booking['status'] !== 'pending_payment' || $booking['payment_status'] === 'test_paid') {
            flash('error', 'Cette réservation ne peut pas être payée à ce stade.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }

        $stripe = new StripeService();
        $session = $stripe->createCheckoutSession($booking);
        if (($session['success'] ?? false) !== true || empty($session['url'])) {
            audit((int) $user['id'], 'stripe_checkout_failed', 'booking', $bookingId);
            flash('error', 'Stripe test n’a pas pu être démarré. Vous pouvez utiliser la simulation de paiement.');
            $this->redirect('/paiement/' . $bookingId);
        }

        audit((int) $user['id'], 'stripe_checkout_started', 'booking', $bookingId);
        header('Location: ' . $session['url']);
        exit;
    }

    public function stripeSuccess(int $bookingId): void
    {
        $user = $this->requireTenantForBooking();
        $booking = (new Booking())->findForTenant($bookingId, (int) $user['id']);
        if (!$booking) {
            flash('error', 'Réservation introuvable.');
            $this->redirect('/locataire/reservations');
        }

        $sessionId = trim((string) input('session_id', ''));
        $session = (new StripeService())->retrieveCheckoutSession($sessionId);
        $validSession = ($session['success'] ?? false) === true
            && (string) ($session['client_reference_id'] ?? '') === (string) $bookingId
            && (string) ($session['payment_status'] ?? '') === 'paid'
            && (string) ($session['status'] ?? '') === 'complete';

        if (!$validSession || !(new Booking())->confirmStripeTestPayment($bookingId, $sessionId)) {
            audit((int) $user['id'], 'stripe_checkout_validation_failed', 'booking', $bookingId);
            flash('error', 'Le paiement Stripe test n’a pas pu être confirmé. Vous pouvez réessayer.');
            $this->redirect('/paiement/' . $bookingId);
        }

        audit((int) $user['id'], 'stripe_test_payment_succeeded', 'booking', $bookingId);
        $bookingAfterPayment = (new Booking())->findForAdmin($bookingId);
        if ($bookingAfterPayment) {
            (new MailService())->sendFakePaymentConfirmedNotification($bookingAfterPayment);
        }
        flash('success', 'Paiement Stripe test validé. Votre réservation est maintenant confirmée.');
        $this->redirect('/locataire/reservations/' . $bookingId);
    }

    public function stripeCancel(int $bookingId): void
    {
        $this->requireTenantForBooking();
        flash('error', 'Paiement Stripe test annulé. Aucun montant réel n’a été débité.');
        $this->redirect('/paiement/' . $bookingId);
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
            (new Booking())->validateForPayment($bookingId);
            $booking = (new Booking())->findForTenant($bookingId, (int) $user['id']);
            if (!$booking) {
                http_response_code(404);
                exit('Réservation introuvable.');
            }
        }
        if ($booking['status'] !== 'pending_payment' || $booking['payment_status'] === 'test_paid') {
            flash('error', 'Cette réservation ne peut pas être payée à ce stade.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        if ((float) $booking['total_price'] <= 0) {
            flash('error', 'Le montant de cette réservation est invalide. Merci de contacter l’équipe AtypikHouse.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        $success = input('scenario') !== 'failure';
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
        if (!$booking) {
            flash('error', 'Réservation introuvable.');
            $this->redirect('/locataire/reservations');
        }
        if (!(new Review())->canReview($bookingId, (int) $user['id'])) {
            flash('error', 'Vous ne pouvez pas déposer d’avis pour cette réservation.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        $rating = (int) input('rating');
        $comment = trim((string) input('comment'));
        if ($rating < 1 || $rating > 5 || $comment === '' || strlen($comment) > 1200) {
            flash('error', 'Merci de fournir une note de 1 à 5 et un commentaire de 1 200 caractères maximum.');
            $this->redirect('/locataire/reservations/' . $bookingId);
        }
        (new Review())->create($booking, $rating, $comment);
        audit((int) $user['id'], 'review_submit', 'review');
        flash('success', 'Votre avis a été publié.');
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
            flash('error', 'Votre compte locataire doit être actif pour utiliser ce parcours.');
            $this->redirect('/hebergements');
        }

        return $user;
    }
}
