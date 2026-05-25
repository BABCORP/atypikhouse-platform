<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use App\Helpers\Upload;
use RuntimeException;

final class OwnerController extends Controller
{
    public function dashboard(): void
    {
        $user = Auth::requireRole('owner');
        $properties = (new Property())->ownerProperties((int) $user['id']);
        $bookings = (new Booking())->ownerBookings((int) $user['id']);
        $this->view('dashboard/owner-dashboard', ['title' => 'Espace propriétaire', 'properties' => $properties, 'bookings' => $bookings]);
    }

    public function properties(): void
    {
        $user = Auth::requireRole('owner');
        $this->view('dashboard/properties', ['title' => 'Mes logements', 'properties' => (new Property())->ownerProperties((int) $user['id'])]);
    }

    public function createProperty(): void
    {
        Auth::requireRole('owner');
        $this->view('dashboard/property-form', ['title' => 'Ajouter un logement', 'property' => null]);
    }

    public function storeProperty(): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        $this->validateProperty();
        try {
            $image = Upload::propertyImage($_FILES['property_image'] ?? []);
        } catch (RuntimeException $exception) {
            flash('error', $exception->getMessage());
            $this->redirect('/proprietaire/logements/ajouter');
        }
        $id = (new Property())->create((int) $user['id'], $_POST, $image);
        audit((int) $user['id'], 'property_creation', 'property', $id);
        flash('success', 'Logement créé en brouillon.');
        $this->redirect('/proprietaire/logements');
    }

    public function editProperty(int $id): void
    {
        $user = Auth::requireRole('owner');
        $property = (new Property())->findOwned($id, (int) $user['id']);
        if (!$property) {
            http_response_code(404);
            exit('Logement introuvable.');
        }
        $this->view('dashboard/property-form', ['title' => 'Modifier un logement', 'property' => $property]);
    }

    public function updateProperty(int $id): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        $this->validateProperty();
        try {
            $image = Upload::propertyImage($_FILES['property_image'] ?? []);
        } catch (RuntimeException $exception) {
            flash('error', $exception->getMessage());
            $this->redirect('/proprietaire/logements/' . $id . '/modifier');
        }
        (new Property())->update($id, (int) $user['id'], $_POST, $image);
        audit((int) $user['id'], 'property_update', 'property', $id);
        flash('success', 'Logement mis à jour.');
        $this->redirect('/proprietaire/logements');
    }

    public function submitProperty(int $id): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        (new Property())->submit($id, (int) $user['id']);
        audit((int) $user['id'], 'property_submit', 'property', $id);
        flash('success', 'Logement soumis à validation.');
        $this->redirect('/proprietaire/logements');
    }

    public function deleteProperty(int $id): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        (new Property())->deleteDraft($id, (int) $user['id']);
        flash('success', 'Brouillon supprimé.');
        $this->redirect('/proprietaire/logements');
    }

    public function availability(): void
    {
        $user = Auth::requireRole('owner');
        $this->view('dashboard/availability', ['title' => 'Disponibilités', 'properties' => (new Property())->ownerProperties((int) $user['id'])]);
    }

    public function storeAvailability(): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        $property = (new Property())->findOwned((int) input('property_id'), (int) $user['id']);
        if (!$property) {
            http_response_code(403);
            exit('Accès refusé.');
        }
        if (!valid_date((string) input('date')) || input('date') < date('Y-m-d')) {
            flash('error', 'Choisissez une date valide et future.');
            $this->redirect('/proprietaire/disponibilites');
        }
        (new Property())->setAvailability((int) $property['id'], (string) input('date'), input('is_available') === '1', input('price_override') !== '' ? (float) input('price_override') : null);
        flash('success', 'Disponibilité enregistrée.');
        $this->redirect('/proprietaire/disponibilites');
    }

    public function reservations(): void
    {
        $user = Auth::requireRole('owner');
        $this->view('dashboard/bookings', ['title' => 'Réservations reçues', 'bookings' => (new Booking())->ownerBookings((int) $user['id']), 'scope' => 'owner']);
    }

    public function profile(): void
    {
        $user = Auth::requireRole('owner');
        $this->view('dashboard/profile', ['title' => 'Profil propriétaire', 'user' => $user, 'ownerProfile' => (new User())->ownerProfile((int) $user['id'])]);
    }

    public function updateProfile(): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        $model = new User();
        $model->updateProfile((int) $user['id'], $_POST);
        if (array_key_exists('company_name', $_POST)) {
            $model->updateOwnerProfile((int) $user['id'], $_POST);
        }
        flash('success', 'Profil mis à jour.');
        $this->redirect('/proprietaire/profil');
    }

    private function validateProperty(): void
    {
        foreach (['title', 'type', 'city', 'region', 'capacity', 'price_per_night', 'short_description', 'long_description'] as $field) {
            if (trim((string) input($field, '')) === '') {
                flash('error', 'Merci de remplir tous les champs obligatoires du logement.');
                remember_old($_POST);
                redirect($_SERVER['HTTP_REFERER'] ?? '/proprietaire/logements');
            }
        }
    }
}
