<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Favorite;
use App\Models\Property;

final class FavoriteController extends Controller
{
    public function toggle(int $propertyId): void
    {
        verify_csrf();
        $redirectTo = $this->redirectTarget();
        $user = Auth::user();
        if (!$user) {
            flash('error', 'Connectez-vous pour ajouter ce logement à vos favoris.');
            $this->redirect('/connexion');
        }

        $property = (new Property())->find($propertyId);
        if (!$property || $property['status'] !== 'published') {
            flash('error', 'Ce logement ne peut pas être ajouté aux favoris.');
            $this->redirect($redirectTo);
        }

        $added = (new Favorite())->toggle((int) $user['id'], (int) $property['id']);
        audit((int) $user['id'], $added ? 'favorite_added' : 'favorite_removed', 'property', (int) $property['id']);
        flash('success', $added ? 'Logement ajouté à vos favoris.' : 'Logement retiré de vos favoris.');
        $this->redirect($redirectTo);
    }

    private function redirectTarget(): string
    {
        $redirect = trim((string) ($_POST['redirect'] ?? '/hebergements'));

        if ($redirect === '' || !str_starts_with($redirect, '/') || str_starts_with($redirect, '//')) {
            return '/hebergements';
        }

        if (preg_match('#^/favoris/\d+/toggle(?:$|[/?#])#', $redirect) === 1) {
            return '/hebergements';
        }

        return $redirect;
    }
}
