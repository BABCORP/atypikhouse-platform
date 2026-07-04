<?php

return [
    'name' => 'AtypikHouse',
    'env' => getenv('APP_ENV') ?: 'local',
    'debug' => (getenv('APP_DEBUG') ?: 'true') === 'true',
    'base_url' => rtrim(getenv('APP_URL') ?: 'http://localhost:8000', '/'),
    'ga4_id' => getenv('GA4_ID') ?: '',
    'gtm_id' => getenv('GTM_ID') ?: '',
    'brevo_api_key' => getenv('BREVO_API_KEY') ?: '',
    'brevo_list_id' => getenv('BREVO_LIST_ID') ?: '',
    'brevo_enabled' => (getenv('BREVO_ENABLED') ?: 'false') === 'true',
    'academic_disclaimer' => 'Projet étudiant fictif — aucun achat, paiement ou réservation réelle ne peut être effectué.',
    'upload_max_bytes' => 5 * 1024 * 1024,
];
