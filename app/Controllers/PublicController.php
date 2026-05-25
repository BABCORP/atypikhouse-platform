<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Property;
use App\Models\Review;

final class PublicController extends Controller
{
    public function home(): void
    {
        $this->view('public/home', [
            'title' => 'AtypikHouse - Hébergements insolites et responsables',
            'metaDescription' => 'Réservez un séjour insolite fictif en cabane, yourte, tiny house ou dôme avec AtypikHouse.',
            'properties' => (new Property())->featured(),
        ]);
    }

    public function concept(): void
    {
        $this->view('public/static', [
            'title' => 'Le concept AtypikHouse',
            'heading' => 'Une plateforme fictive pour voyager autrement',
            'body' => 'AtypikHouse imagine une marketplace responsable dédiée aux hébergements insolites en France : cabanes, yourtes, tiny houses, dômes et séjours en pleine nature. Le projet valorise la simplicité, la confiance et la projection émotionnelle, tout en donnant aux propriétaires un espace pour gérer leurs logements.',
        ]);
    }

    public function host(): void
    {
        $this->view('public/static', [
            'title' => 'Devenir hôte AtypikHouse',
            'heading' => 'Partagez votre hébergement insolite',
            'body' => 'Créez un compte propriétaire, ajoutez vos logements, gérez vos disponibilités et suivez vos réservations fictives depuis un tableau de bord simple et sécurisé.',
            'cta' => ['label' => 'Créer un compte propriétaire', 'url' => '/inscription?role=owner'],
        ]);
    }

    public function catalogue(): void
    {
        $filters = [
            'location' => trim((string) input('destination', '')),
            'type' => trim((string) input('type', '')),
            'capacity' => trim((string) input('capacity', '')),
            'max_price' => trim((string) input('max_price', '')),
            'amenity' => trim((string) input('amenity', '')),
            'start_date' => trim((string) input('start_date', '')),
            'end_date' => trim((string) input('end_date', '')),
            'sort' => trim((string) input('sort', 'newest')),
        ];
        $this->view('public/catalogue', [
            'title' => 'Hébergements insolites en France',
            'metaDescription' => 'Catalogue fictif des hébergements insolites AtypikHouse avec filtres par destination, type, capacité et prix.',
            'properties' => (new Property())->published($filters),
            'filters' => $filters,
        ]);
    }

    public function property(string $slug): void
    {
        $model = new Property();
        $property = $model->findBySlug($slug);
        if (!$property) {
            $this->notFound();
            return;
        }
        audit($_SESSION['user_id'] ?? null, 'property_view', 'property', (int) $property['id']);
        $this->view('public/property', [
            'title' => $property['title'] . ' - AtypikHouse',
            'metaDescription' => $property['short_description'],
            'property' => $property,
            'images' => $model->images((int) $property['id']),
            'amenities' => $model->amenities((int) $property['id']),
            'availabilities' => $model->availabilities((int) $property['id']),
            'reviews' => (new Review())->forProperty((int) $property['id']),
            'related' => $model->published(['type' => $property['type']], 3),
        ]);
    }

    public function blog(): void
    {
        $this->view('public/blog', [
            'title' => 'Blog AtypikHouse',
            'posts' => (new BlogPost())->published(),
        ]);
    }

    public function blogPost(string $slug): void
    {
        $post = (new BlogPost())->findBySlug($slug);
        if (!$post) {
            $this->notFound();
            return;
        }
        $this->view('public/blog-post', [
            'title' => $post['title'] . ' - Blog AtypikHouse',
            'metaDescription' => $post['excerpt'],
            'post' => $post,
        ]);
    }

    public function contact(): void
    {
        $this->view('public/contact', ['title' => 'Contact AtypikHouse']);
    }

    public function faq(): void
    {
        $this->view('public/faq', [
            'title' => 'Questions fréquentes - AtypikHouse',
            'metaDescription' => 'Questions fréquentes sur la plateforme fictive AtypikHouse et son paiement de démonstration.',
        ]);
    }

    public function sendContact(): void
    {
        verify_csrf();
        $required = ['name', 'email', 'subject', 'message'];
        foreach ($required as $field) {
            if (trim((string) input($field, '')) === '') {
                remember_old($_POST);
                flash('error', 'Tous les champs du formulaire de contact sont obligatoires.');
                $this->redirect('/contact');
            }
        }
        if (!filter_var(input('email'), FILTER_VALIDATE_EMAIL)) {
            remember_old($_POST);
            flash('error', 'Adresse email invalide.');
            $this->redirect('/contact');
        }
        if (input('privacy_consent') !== '1') {
            remember_old($_POST);
            flash('error', 'Veuillez accepter l’utilisation de vos données pour être recontacté.');
            $this->redirect('/contact');
        }
        (new ContactMessage())->create($_POST);
        audit($_SESSION['user_id'] ?? null, 'contact_form_submit', 'contact_message');
        clear_old();
        flash('success', 'Votre message a bien été enregistré.');
        $this->redirect('/contact');
    }

    public function legal(): void
    {
        $page = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $labels = [
            'mentions-legales' => 'Mentions légales',
            'cgu' => 'Conditions générales d’utilisation',
            'cgv' => 'Conditions générales de vente fictives',
            'politique-confidentialite' => 'Politique de confidentialité',
            'cookies' => 'Gestion des cookies',
        ];
        $this->view('public/legal', [
            'title' => $labels[$page] ?? 'Page légale',
            'heading' => $labels[$page] ?? 'Page légale',
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('public/static', [
            'title' => 'Page introuvable',
            'heading' => 'Page introuvable',
            'body' => 'La page demandée n’existe pas ou a été déplacée.',
        ]);
    }
}
