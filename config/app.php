<?php

return [
    'name' => 'AtypikHouse',
    'env' => getenv('APP_ENV') ?: 'local',
    'debug' => getenv('APP_DEBUG') !== false ? getenv('APP_DEBUG') === 'true' : (getenv('APP_ENV') ?: 'local') !== 'production',
    'base_url' => rtrim(getenv('APP_URL') ?: 'http://localhost:8000', '/'),
    'ga4_id' => getenv('GA4_ID') ?: '',
    'gtm_id' => getenv('GTM_ID') ?: '',
    'turnstile_enabled' => (getenv('TURNSTILE_ENABLED') ?: 'false') === 'true',
    'turnstile_site_key' => getenv('TURNSTILE_SITE_KEY') ?: '',
    'turnstile_secret_key' => getenv('TURNSTILE_SECRET_KEY') ?: '',
    'brevo_api_key' => getenv('BREVO_API_KEY') ?: '',
    'brevo_list_id' => getenv('BREVO_LIST_ID') ?: '',
    'brevo_enabled' => (getenv('BREVO_ENABLED') ?: 'false') === 'true',
    'stripe_enabled' => (getenv('STRIPE_ENABLED') ?: 'false') === 'true',
    'stripe_mode' => getenv('STRIPE_MODE') ?: 'test',
    'stripe_public_key' => getenv('STRIPE_PUBLIC_KEY') ?: (getenv('STRIPE_PUBLISHABLE_KEY') ?: ''),
    'stripe_secret_key' => getenv('STRIPE_SECRET_KEY') ?: '',
    'academic_disclaimer' => 'Projet étudiant fictif. Aucun achat, paiement ou réservation réelle ne peut être effectué. Les informations présentées sont utilisées dans le cadre d’une démonstration académique.',
    'upload_max_bytes' => 5 * 1024 * 1024,
];
