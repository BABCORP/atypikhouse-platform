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
            'title' => 'Le concept AtypikHouse - Séjours insolites et tourisme responsable',
            'metaDescription' => 'Découvrez le concept AtypikHouse : une marketplace fictive dédiée aux cabanes, tiny houses et séjours nature responsables.',
            'canonical' => url('/concept'),
            'breadcrumbs' => [
                ['label' => 'Le concept', 'url' => null],
            ],
        ]);
    }

    public function host(): void
    {
        $this->view('public/host', [
            'title' => 'Devenir hôte AtypikHouse',
            'metaDescription' => 'Rejoindre AtypikHouse comme propriétaire : publier un logement insolite, gérer les disponibilités et suivre les réservations de démonstration académique.',
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
            'title' => 'Hébergements insolites en France - Cabanes, dômes et tiny houses',
            'metaDescription' => 'Comparez les hébergements insolites AtypikHouse : cabanes dans les arbres, tiny houses, dômes et séjours nature.',
            'canonical' => url('/hebergements'),
            'breadcrumbs' => [
                ['label' => 'Hébergements', 'url' => null],
            ],
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
        $propertyId = (int) $property['id'];
        $images = $model->images($propertyId);
        $availabilities = $model->availabilities($propertyId);
        $bookedDates = (new Booking())->bookedDatesForProperty($propertyId);
        audit($_SESSION['user_id'] ?? null, 'property_view', 'property', (int) $property['id']);
        $this->view('public/property', [
            'title' => $property['title'] . ' - AtypikHouse',
            'metaDescription' => trim($property['short_description'] . ' Séjour insolite à ' . $property['city'] . ' avec AtypikHouse.'),
            'canonical' => url('/hebergements/' . $property['slug']),
            'breadcrumbs' => [
                ['label' => 'Hébergements', 'url' => '/hebergements'],
                ['label' => $property['title'], 'url' => null],
            ],
            'ogImage' => image_url($images[0]['image_path'] ?? null),
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
                'priceRange' => money($property['price_per_night']) . ' / nuit - démonstration académique',
                'additionalProperty' => ['name' => 'Projet étudiant fictif', 'value' => config('academic_disclaimer')],
            ],
            'property' => $property,
            'images' => $images,
            'amenities' => $model->amenities($propertyId),
            'availabilities' => $availabilities,
            'calendarData' => [
                'availabilities' => array_map(static fn (array $row): array => [
                    'date' => $row['date'],
                    'is_available' => (int) $row['is_available'],
                    'price_override' => $row['price_override'] !== null ? (float) $row['price_override'] : null,
                ], $availabilities),
                'booked_dates' => $bookedDates,
            ],
            'reviews' => $reviewModel->forProperty($propertyId),
            'ratingSummary' => $reviewModel->summaryForProperty($propertyId),
            'favoriteIds' => $userId ? (new Favorite())->idsForUser($userId) : [],
            'currentUser' => $userId ? \App\Core\Auth::user() : null,
            'related' => $model->published(['type' => $property['type']], 3),
        ]);
    }

    public function blog(): void
    {
        $this->view('public/blog', [
            'title' => 'Blog AtypikHouse - Conseils pour séjours insolites',
            'metaDescription' => 'Explorez les conseils AtypikHouse pour préparer un week-end insolite, choisir un hébergement nature et comprendre le tourisme responsable.',
            'canonical' => url('/blog'),
            'breadcrumbs' => [
                ['label' => 'Blog', 'url' => null],
            ],
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
            'breadcrumbs' => [
                ['label' => 'Blog', 'url' => '/blog'],
                ['label' => $post['title'], 'url' => null],
            ],
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
            'metaDescription' => 'Contacter l’équipe AtypikHouse pour une question sur les hébergements insolites, le compte propriétaire ou la démonstration académique.',
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
        if (!verify_captcha('contact')) {
            remember_old($_POST);
            flash('error', 'La vérification de sécurité est incorrecte. Merci de réessayer.');
            $this->redirect('/contact');
        }
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
        $emailSent = (new MailService())->sendContactNotification(
            trim((string) input('name')),
            strtolower(trim((string) input('email'))),
            trim((string) input('subject'))
        );
        audit($_SESSION['user_id'] ?? null, $emailSent ? 'email_contact_admin_sent' : 'email_contact_admin_failed', 'contact_message');
        clear_old();
        flash('success', 'Votre message a bien été transmis. L’équipe AtypikHouse reviendra vers vous prochainement.');
        $this->redirect('/contact');
    }

    public function newsletter(): void
    {
        verify_csrf();
        $email = strtolower(trim((string) input('email', '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || input('newsletter_consent') !== '1') {
            flash('error', 'Merci d’indiquer une adresse email valide et d’accepter le consentement newsletter dans le cadre de la démonstration académique.');
            $this->redirect('/');
        }
        $mode = (new NewsletterService())->subscribe($email, isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null);
        flash('success', $mode === 'demo' ? 'Votre inscription newsletter a été enregistrée pour la démonstration académique.' : 'Inscription enregistrée dans la file emailing sécurisée.');
        $this->redirect('/');
    }

    public function privacyRequest(): void
    {
        $this->view('public/privacy-request', [
            'title' => 'Demande relative aux données personnelles',
            'metaDescription' => 'Formulaire de demande relative aux données personnelles pour le projet étudiant AtypikHouse.',
            'canonical' => url('/mes-donnees'),
        ]);
    }

    public function siteMap(): void
    {
        $this->view('public/site-map', [
            'title' => 'Plan du site - AtypikHouse',
            'metaDescription' => 'Plan du site AtypikHouse pour accéder aux principales pages publiques, espaces utilisateurs et informations légales.',
            'canonical' => url('/plan-du-site'),
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
        flash('success', 'Votre demande relative aux données personnelles a bien été enregistrée.');
        $this->redirect('/mes-donnees');
    }

    public function legal(): void
    {
        $page = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $labels = [
            'mentions-legales' => 'Mentions légales',
            'cgu' => 'Conditions générales d’utilisation',
            'cgv' => 'Conditions générales de vente',
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
            'body' => 'Le lien demandé n’existe pas ou a été déplacé sur AtypikHouse.',
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
            'title' => 'Erreur technique',
            'code' => 500,
            'heading' => 'Une erreur est survenue',
            'body' => 'Un problème technique est survenu. Aucun paiement ni réservation réelle n’est concerné.',
        ]);
    }
}
