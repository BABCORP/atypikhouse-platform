<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\PropertyChangeRequest;
use App\Models\User;
use App\Helpers\Upload;
use App\Services\MailService;
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
        $user = Auth::requireRole('owner');
        $this->ensureApprovedOwner($user);
        $this->view('dashboard/property-form', ['title' => 'Ajouter un logement', 'property' => null]);
    }

    public function storeProperty(): void
    {
        $user = Auth::requireRole('owner');
        $this->ensureApprovedOwner($user);
        verify_csrf();
        $this->validateProperty('/proprietaire/logements/ajouter');
        $_POST['amenities'] = $this->normalizedAmenities();
        try {
            $image = Upload::propertyImage($_FILES['property_main_image'] ?? ($_FILES['property_image'] ?? []));
            $secondaryImages = Upload::propertyImages($_FILES['property_images'] ?? []);
        } catch (RuntimeException $exception) {
            flash('error', $exception->getMessage());
            $this->redirect('/proprietaire/logements/ajouter');
        }
        $id = (new Property())->create((int) $user['id'], $_POST, $image, $secondaryImages);
        audit((int) $user['id'], 'property_submitted', 'property', $id);
        $emailSent = (new MailService())->sendPropertySubmittedNotification(
            (string) $user['email'],
            trim((string) input('title')),
            (string) $user['first_name'],
            [
                'city' => trim((string) input('city')),
                'price_per_night' => (float) input('price_per_night'),
            ]
        );
        audit((int) $user['id'], $emailSent ? 'email_property_submitted_sent' : 'email_property_submitted_failed', 'property', $id);
        flash('success', 'Votre logement a été soumis à validation. Il sera publié après vérification par l’administrateur.');
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
        if ($property['status'] === 'deleted') {
            flash('error', 'Un logement supprimé ne peut plus être modifié.');
            $this->redirect('/proprietaire/logements');
        }
        $model = new Property();
        $pendingChange = (new PropertyChangeRequest())->pendingForProperty($id);
        $this->view('dashboard/property-form', [
            'title' => 'Modifier un logement',
            'property' => $property,
            'images' => $model->images($id),
            'amenities' => $model->amenities($id),
            'pendingChange' => $pendingChange,
        ]);
    }

    public function updateProperty(int $id): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        $property = (new Property())->findOwned($id, (int) $user['id']);
        if (!$property || $property['status'] === 'deleted') {
            http_response_code(404);
            exit('Logement introuvable.');
        }
        $this->validateProperty('/proprietaire/logements/' . $id . '/modifier');
        $_POST['amenities'] = $this->normalizedAmenities();
        try {
            $image = Upload::propertyImage($_FILES['property_main_image'] ?? ($_FILES['property_image'] ?? []));
            $secondaryImages = Upload::propertyImages($_FILES['property_images'] ?? []);
        } catch (RuntimeException $exception) {
            flash('error', $exception->getMessage());
            $this->redirect('/proprietaire/logements/' . $id . '/modifier');
        }
        if (in_array($property['status'], ['published', 'paused'], true)) {
            $requestId = (new PropertyChangeRequest())->createOrReplace($id, (int) $user['id'], $this->proposedPropertyData($_POST, $image, $secondaryImages));
            audit((int) $user['id'], 'property_change_requested', 'property_change_request', $requestId);
            $emailSent = (new MailService())->sendPropertyChangeSubmittedNotification(
                (string) $user['email'],
                (string) $user['first_name'],
                (string) $property['title']
            );
            audit((int) $user['id'], $emailSent ? 'email_property_change_submitted_sent' : 'email_property_change_submitted_failed', 'property_change_request', $requestId);
            flash('success', 'Vos modifications ont été soumises à validation. Elles seront visibles après approbation par l’administrateur.');
            $this->redirect('/proprietaire/logements');
        }
        (new Property())->update($id, (int) $user['id'], $_POST, $image, $secondaryImages);
        audit((int) $user['id'], 'property_update', 'property', $id);
        if ($image || $secondaryImages) {
            audit((int) $user['id'], 'property_image_add', 'property', $id);
        }
        flash('success', 'Logement mis à jour.');
        $this->redirect('/proprietaire/logements');
    }

    public function updateImageAlt(int $propertyId, int $imageId): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        $property = (new Property())->findOwned($propertyId, (int) $user['id']);
        if ($property && in_array($property['status'], ['published', 'paused'], true)) {
            flash('error', 'Pour un logement publié, les changements d’images doivent être soumis via le formulaire de modification.');
            $this->redirect('/proprietaire/logements/' . $propertyId . '/modifier');
        }
        if (!(new Property())->updateImageAlt($propertyId, $imageId, (int) $user['id'], (string) input('alt_text', ''))) {
            http_response_code(403);
            exit('Accès refusé.');
        }
        audit((int) $user['id'], 'property_image_alt_update', 'property_image', $imageId);
        flash('success', 'Texte alternatif mis à jour.');
        $this->redirect('/proprietaire/logements/' . $propertyId . '/modifier');
    }

    public function setMainImage(int $propertyId, int $imageId): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        $property = (new Property())->findOwned($propertyId, (int) $user['id']);
        if ($property && in_array($property['status'], ['published', 'paused'], true)) {
            flash('error', 'Pour un logement publié, les changements d’images doivent être soumis via le formulaire de modification.');
            $this->redirect('/proprietaire/logements/' . $propertyId . '/modifier');
        }
        if (!(new Property())->setMainImage($propertyId, $imageId, (int) $user['id'])) {
            http_response_code(403);
            exit('Accès refusé.');
        }
        audit((int) $user['id'], 'property_image_main_update', 'property_image', $imageId);
        flash('success', 'Image principale mise à jour.');
        $this->redirect('/proprietaire/logements/' . $propertyId . '/modifier');
    }

    public function deleteImage(int $propertyId, int $imageId): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        $property = (new Property())->findOwned($propertyId, (int) $user['id']);
        if ($property && in_array($property['status'], ['published', 'paused'], true)) {
            flash('error', 'Pour un logement publié, les changements d’images doivent être soumis via le formulaire de modification.');
            $this->redirect('/proprietaire/logements/' . $propertyId . '/modifier');
        }
        if (!(new Property())->deleteImage($propertyId, $imageId, (int) $user['id'])) {
            http_response_code(403);
            exit('Accès refusé.');
        }
        audit((int) $user['id'], 'property_image_delete', 'property_image', $imageId);
        flash('success', 'Image supprimée. Un visuel temporaire est conservé si nécessaire.');
        $this->redirect('/proprietaire/logements/' . $propertyId . '/modifier');
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
        $model = new Property();
        $properties = $model->ownerProperties((int) $user['id']);
        $selectedId = (int) (input('property_id') ?: ($properties[0]['id'] ?? 0));
        $selected = $selectedId ? $model->findOwned($selectedId, (int) $user['id']) : null;
        $availabilities = $selected ? $model->availabilities((int) $selected['id']) : [];
        $bookedDates = $selected ? (new Booking())->bookedDatesForOwnerProperty((int) $selected['id'], (int) $user['id']) : [];
        $this->view('dashboard/availability', [
            'title' => 'Disponibilités',
            'properties' => $properties,
            'selectedPropertyId' => $selected ? (int) $selected['id'] : null,
            'availabilities' => $availabilities,
            'calendarData' => [
                'selectedPropertyId' => $selected ? (int) $selected['id'] : null,
                'availabilities' => array_map(static fn (array $row): array => [
                    'date' => $row['date'],
                    'is_available' => (int) $row['is_available'],
                    'price_override' => $row['price_override'] !== null ? (float) $row['price_override'] : null,
                ], $availabilities),
                'booked_dates' => $bookedDates,
            ],
        ]);
    }

    public function storeAvailability(): void
    {
        $user = Auth::requireRole('owner');
        verify_csrf();
        $start = (string) input('start_date', input('date', ''));
        $end = (string) input('end_date', $start);
        $property = (new Property())->findOwned((int) input('property_id'), (int) $user['id']);
        if (!$property) {
            http_response_code(403);
            exit('Accès refusé.');
        }
        if (!valid_date($start) || !valid_date($end) || $start < date('Y-m-d') || $end < $start) {
            flash('error', 'Choisissez une plage de dates valide et future.');
            $this->redirect('/proprietaire/disponibilites');
        }
        if (nights_between($start, (new \DateTimeImmutable($end))->modify('+1 day')->format('Y-m-d')) > 90) {
            flash('error', 'La plage ne peut pas dépasser 90 jours.');
            $this->redirect('/proprietaire/disponibilites');
        }
        $count = (new Property())->setAvailabilityRange((int) $property['id'], $start, $end, input('is_available') === '1', input('price_override') !== '' ? (float) input('price_override') : null);
        audit((int) $user['id'], 'availability_range_update', 'property', (int) $property['id']);
        flash('success', $count . ' date(s) de disponibilité enregistrée(s).');
        $this->redirect('/proprietaire/disponibilites');
    }

    public function reservations(): void
    {
        $user = Auth::requireRole('owner');
        $filters = [
            'property_id' => trim((string) input('property_id', '')),
            'status' => trim((string) input('status', '')),
            'start_date' => trim((string) input('start_date', '')),
            'end_date' => trim((string) input('end_date', '')),
        ];
        $this->view('dashboard/bookings', [
            'title' => 'Réservations reçues',
            'bookings' => (new Booking())->ownerBookings((int) $user['id'], $filters),
            'properties' => (new Property())->ownerProperties((int) $user['id']),
            'filters' => $filters,
            'scope' => 'owner',
        ]);
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

    private function validateProperty(string $fallback): void
    {
        foreach (['title', 'type', 'city', 'region', 'capacity', 'price_per_night', 'short_description', 'long_description'] as $field) {
            if (trim((string) input($field, '')) === '') {
                flash('error', 'Merci de remplir tous les champs obligatoires du logement.');
                remember_old($_POST);
                redirect($fallback);
            }
        }
        if (!in_array(input('type'), ['treehouse', 'yurt', 'floating_cabin', 'tiny_house', 'dome', 'other'], true)) {
            flash('error', 'Type de logement invalide.');
            remember_old($_POST);
            redirect($fallback);
        }
        if ((int) input('capacity', 0) < 1 || (float) input('price_per_night', 0) <= 0) {
            flash('error', 'La capacité et le prix par nuit doivent être supérieurs à zéro.');
            remember_old($_POST);
            redirect($fallback);
        }
        if (mb_strlen(trim((string) input('other_amenities', ''))) > 500) {
            flash('error', 'Les équipements complémentaires ne doivent pas dépasser 500 caractères.');
            remember_old($_POST);
            redirect($fallback);
        }
    }

    private function normalizedAmenities(): array
    {
        $amenities = array_filter(array_map('trim', (array) input('amenities', [])));
        $other = trim((string) input('other_amenities', ''));
        if ($other !== '') {
            $customItems = preg_split('/[,\n;]+/', $other) ?: [];
            foreach ($customItems as $item) {
                $name = trim(strip_tags((string) $item));
                if ($name !== '') {
                    $amenities[] = mb_substr($name, 0, 120);
                }
            }
        }

        return array_values(array_unique($amenities));
    }

    private function proposedPropertyData(array $data, ?string $mainImage, array $secondaryImages): array
    {
        return [
            'title' => trim((string) $data['title']),
            'type' => (string) $data['type'],
            'short_description' => trim((string) $data['short_description']),
            'long_description' => trim((string) $data['long_description']),
            'address' => trim((string) ($data['address'] ?? '')),
            'city' => trim((string) $data['city']),
            'postal_code' => trim((string) ($data['postal_code'] ?? '')),
            'region' => trim((string) $data['region']),
            'country' => trim((string) ($data['country'] ?? 'France')),
            'capacity' => (int) $data['capacity'],
            'bedrooms' => (int) ($data['bedrooms'] ?? 1),
            'beds' => (int) ($data['beds'] ?? 1),
            'bathrooms' => (int) ($data['bathrooms'] ?? 1),
            'price_per_night' => (float) $data['price_per_night'],
            'cleaning_fee' => (float) ($data['cleaning_fee'] ?? 0),
            'eco_score' => (int) ($data['eco_score'] ?? 3),
            'amenities' => array_values((array) ($data['amenities'] ?? [])),
            'main_image' => $mainImage ? [
                'path' => $mainImage,
                'alt_text' => trim((string) ($data['image_alt'] ?? '')),
            ] : null,
            'secondary_images' => array_map(static fn (string $path): array => [
                'path' => $path,
                'alt_text' => 'Photo complémentaire de ' . trim((string) $data['title']),
            ], $secondaryImages),
        ];
    }

    private function ensureApprovedOwner(array $user): void
    {
        $profile = (new User())->ownerProfile((int) $user['id']);
        if ($user['status'] !== 'active' || !$profile || $profile['verification_status'] !== 'approved') {
            flash('error', 'Votre compte propriétaire doit être validé par l’administrateur avant de proposer un logement.');
            $this->redirect('/proprietaire/logements');
        }
    }
}
