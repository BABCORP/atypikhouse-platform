<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Review;

final class BookingController extends Controller
{
    public function start(int $propertyId): void
    {
        $user = Auth::requireRole('tenant');
        verify_csrf();
        $property = (new Property())->find($propertyId);
        if (!$property || $property['status'] !== 'published') {
            flash('error', 'Ce logement n’est pas disponible à la réservation.');
            $this->redirect('/hebergements');
        }
        if (!valid_date((string) input('start_date')) || !valid_date((string) input('end_date')) || nights_between((string) input('start_date'), (string) input('end_date')) < 1) {
            flash('error', 'Les dates de réservation sont invalides.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        if ((int) input('guests_count') < 1 || (int) input('guests_count') > (int) $property['capacity']) {
            flash('error', 'Le nombre de voyageurs dépasse la capacité du logement.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        $bookingModel = new Booking();
        if (!$bookingModel->isAvailable((int) $property['id'], (string) input('start_date'), (string) input('end_date'))) {
            flash('error', 'Ces dates ne sont pas disponibles.');
            $this->redirect('/hebergements/' . $property['slug']);
        }
        $bookingId = $bookingModel->create($property, (int) $user['id'], $_POST);
        audit((int) $user['id'], 'booking_creation', 'booking', $bookingId);
        $this->redirect('/paiement/' . $bookingId);
    }

    public function payment(int $bookingId): void
    {
        $user = Auth::requireRole('tenant');
        $booking = (new Booking())->findForTenant($bookingId, (int) $user['id']);
        if (!$booking) {
            http_response_code(404);
            exit('Réservation introuvable.');
        }
        $this->view('dashboard/payment', ['title' => 'Paiement fictif', 'booking' => $booking]);
    }

    public function simulatePayment(int $bookingId): void
    {
        $user = Auth::requireRole('tenant');
        verify_csrf();
        $booking = (new Booking())->findForTenant($bookingId, (int) $user['id']);
        if (!$booking) {
            http_response_code(404);
            exit('Réservation introuvable.');
        }
        $success = input('scenario') === 'success';
        $success = (new Booking())->simulatePayment($bookingId, $success);
        audit((int) $user['id'], $success ? 'booking_payment_test_success' : 'booking_payment_test_failed', 'booking', $bookingId);
        flash($success ? 'success' : 'error', $success ? 'Paiement fictif validé. Votre réservation est confirmée.' : 'Paiement fictif refusé. Aucun paiement réel n’a été effectué.');
        $this->redirect('/locataire/reservations/' . $bookingId);
    }

    public function review(int $bookingId): void
    {
        $user = Auth::requireRole('tenant');
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
}
