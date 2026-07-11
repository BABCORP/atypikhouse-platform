<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\NewsletterService;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Favorite;
use App\Models\Property;
use App\Models\Review;
use App\Services\MailService;

final class PublicController extends Controller
{
    public function home(): void
    {
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $this->view('public/home', [
            'title' => 'AtypikHouse - Hébergements insolites et responsables',
            'metaDescription' => 'Réservez un séjour insolite fictif en cabane dans les arbres, yourte nature, tiny house écologique ou dôme avec AtypikHouse.',
            'canonical' => url('/'),
            'properties' => (new Property())->featured(),
            'favoriteIds' => $userId ? (new Favorite())->idsForUser($userId) : [],
            'destinations' => (new Property())->availableDestinations(),
        ]);
    }

    public function concept(): void
    {
        $this->view('public/concept', [
            'title' => 'Le concept AtypikHouse',
            'metaDescription' => 'Découvrez le concept AtypikHouse : une marketplace fictive dédiée aux hébergements insolites, au séjour nature et au tourisme responsable.',
            'canonical' => url('/concept'),
        ]);
    }

    public function host(): void
    {
        $this->view('public/host', [
            'title' => 'Devenir hôte AtypikHouse',
            'metaDescription' => 'Rejoindre AtypikHouse comme propriétaire fictif : publier un logement insolite, gérer les disponibilités et suivre les réservations de démonstration.',
            'canonical' => url('/devenir-hote'),
        ]);
    }

    public function catalogue(): void
    {
        $propertyModel = new Property();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
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
            'metaDescription' => 'Catalogue fictif d’hébergements insolites en France : cabanes, yourtes, tiny houses, dômes et séjours nature responsables.',
            'canonical' => url('/hebergements'),
            'properties' => $propertyModel->published($filters),
            'filters' => $filters,
            'destinations' => $propertyModel->availableDestinations(),
            'favoriteIds' => $userId ? (new Favorite())->idsForUser($userId) : [],
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
        $reviewModel = new Review();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        audit($_SESSION['user_id'] ?? null, 'property_view', 'property', (int) $property['id']);
        $this->view('public/property', [
            'title' => $property['title'] . ' - AtypikHouse',
            'metaDescription' => $property['short_description'],
            'canonical' => url('/hebergements/' . $property['slug']),
            'ogImage' => image_url($model->images((int) $property['id'])[0]['image_path'] ?? null),
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => 'LodgingBusiness',
                'name' => $property['title'],
                'description' => $property['short_description'],
                'url' => url('/hebergements/' . $property['slug']),
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $property['city'],
                    'addressRegion' => $property['region'],
                    'addressCountry' => 'FR',
                ],
                'priceRange' => money($property['price_per_night']) . ' / nuit - démonstration fictive',
                'additionalProperty' => ['name' => 'Projet étudiant fictif', 'value' => config('academic_disclaimer')],
            ],
            'property' => $property,
            'images' => $model->images((int) $property['id']),
            'amenities' => $model->amenities((int) $property['id']),
            'availabilities' => $model->availabilities((int) $property['id']),
            'calendarData' => [
                'availabilities' => array_map(static fn (array $row): array => [
                    'date' => $row['date'],
                    'is_available' => (int) $row['is_available'],
                    'price_override' => $row['price_override'] !== null ? (float) $row['price_override'] : null,
                ], $model->availabilities((int) $property['id'])),
                'booked_dates' => (new Booking())->bookedDatesForProperty((int) $property['id']),
            ],
            'reviews' => $reviewModel->forProperty((int) $property['id']),
            'ratingSummary' => $reviewModel->summaryForProperty((int) $property['id']),
            'favoriteIds' => $userId ? (new Favorite())->idsForUser($userId) : [],
            'currentUser' => $userId ? \App\Core\Auth::user() : null,
            'related' => $model->published(['type' => $property['type']], 3),
        ]);
    }

    public function blog(): void
    {
        $this->view('public/blog', [
            'title' => 'Blog AtypikHouse',
            'metaDescription' => 'Conseils et inspirations fictives pour préparer un week-end insolite proche de Paris ou un séjour nature responsable.',
            'canonical' => url('/blog'),
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
            'canonical' => url('/blog/' . $post['slug']),
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post['title'],
                'description' => $post['excerpt'],
                'datePublished' => $post['created_at'],
                'dateModified' => $post['updated_at'],
                'author' => ['@type' => 'Organization', 'name' => 'AtypikHouse'],
            ],
            'post' => $post,
        ]);
    }

    public function contact(): void
    {
        $this->view('public/contact', [
            'title' => 'Contact AtypikHouse',
            'metaDescription' => 'Contacter l’équipe fictive AtypikHouse pour une question sur les hébergements insolites, le compte propriétaire ou la démonstration.',
            'canonical' => url('/contact'),
        ]);
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
        (new MailService())->sendContactNotification(
            trim((string) input('name')),
            strtolower(trim((string) input('email'))),
            trim((string) input('subject'))
        );
        clear_old();
        flash('success', 'Votre message a bien été enregistré.');
        $this->redirect('/contact');
    }

    public function newsletter(): void
    {
        verify_csrf();
        $email = strtolower(trim((string) input('email', '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || input('newsletter_consent') !== '1') {
            flash('error', 'Merci d’indiquer une adresse email valide et d’accepter le consentement newsletter de démonstration.');
            $this->redirect('/');
        }
        $mode = (new NewsletterService())->subscribe($email, isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null);
        flash('success', $mode === 'demo' ? 'Inscription newsletter de démonstration enregistrée.' : 'Inscription enregistrée dans la file Brevo de démonstration sécurisée.');
        $this->redirect('/');
    }

    public function privacyRequest(): void
    {
        $this->view('public/privacy-request', [
            'title' => 'Demande RGPD de démonstration',
            'metaDescription' => 'Formulaire de demande RGPD simulée pour le projet étudiant AtypikHouse.',
            'canonical' => url('/mes-donnees'),
        ]);
    }

    public function sendPrivacyRequest(): void
    {
        verify_csrf();
        $type = (string) input('request_type');
        if (!in_array($type, ['access', 'rectification', 'deletion', 'opposition'], true)) {
            flash('error', 'Type de demande invalide.');
            $this->redirect('/mes-donnees');
        }
        foreach (['name', 'email', 'message'] as $field) {
            if (trim((string) input($field, '')) === '') {
                remember_old($_POST);
                flash('error', 'Merci de remplir tous les champs de la demande.');
                $this->redirect('/mes-donnees');
            }
        }
        if (!filter_var(input('email'), FILTER_VALIDATE_EMAIL) || input('privacy_consent') !== '1') {
            remember_old($_POST);
            flash('error', 'Adresse email invalide ou consentement manquant.');
            $this->redirect('/mes-donnees');
        }

        $labels = [
            'access' => 'accès',
            'rectification' => 'rectification',
            'deletion' => 'suppression',
            'opposition' => 'opposition',
        ];
        (new ContactMessage())->create([
            'name' => input('name'),
            'email' => input('email'),
            'subject' => 'Demande RGPD - ' . $labels[$type],
            'message' => (string) input('message') . "\n\nDemande académique simulée : " . $labels[$type],
        ]);
        audit($_SESSION['user_id'] ?? null, 'privacy_request_submit', 'contact_message');
        clear_old();
        flash('success', 'Votre demande RGPD de démonstration a été enregistrée localement.');
        $this->redirect('/mes-donnees');
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
            'page' => $page,
            'metaDescription' => 'Informations légales, RGPD et cookies du projet étudiant fictif AtypikHouse.',
            'canonical' => url('/' . $page),
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('public/error', [
            'title' => 'Page introuvable',
            'code' => 404,
            'heading' => 'Cette page reste introuvable',
            'body' => 'Le lien demandé n’existe pas ou a été déplacé dans cette démonstration AtypikHouse.',
        ]);
    }

    public function forbidden(): void
    {
        http_response_code(403);
        $this->view('public/error', [
            'title' => 'Accès refusé',
            'code' => 403,
            'heading' => 'Accès réservé',
            'body' => 'Votre rôle ne permet pas d’accéder à cette page. Les espaces locataire, propriétaire et administrateur restent séparés.',
        ]);
    }

    public function serverError(): void
    {
        http_response_code(500);
        $this->view('public/error', [
            'title' => 'Erreur de démonstration',
            'code' => 500,
            'heading' => 'Une erreur est survenue',
            'body' => 'La démonstration a rencontré un problème technique. Aucun paiement ni réservation réelle n’est concerné.',
        ]);
    }
}
