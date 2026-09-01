<?php

namespace App\Services;

final class StripeService
{
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = (string) config('stripe_secret_key', '');
    }

    public function isReady(): bool
    {
        return (bool) config('stripe_enabled')
            && (string) config('stripe_mode', 'test') === 'test'
            && str_starts_with((string) config('stripe_public_key', ''), 'pk_test_')
            && str_starts_with($this->secretKey, 'sk_test_');
    }

    public function createCheckoutSession(array $booking): array
    {
        if (!$this->isReady()) {
            return ['success' => false, 'error' => 'Stripe test n’est pas configuré.'];
        }

        $bookingId = (int) $booking['id'];
        $amount = (int) round(((float) $booking['total_price']) * 100);
        if ($bookingId < 1 || $amount < 50) {
            return ['success' => false, 'error' => 'Montant Stripe invalide.'];
        }

        $baseUrl = rtrim((string) config('base_url'), '/');
        $payload = [
            'mode' => 'payment',
            'success_url' => $baseUrl . '/paiement/' . $bookingId . '/stripe/succes?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $baseUrl . '/paiement/' . $bookingId . '/stripe/annule',
            'client_reference_id' => (string) $bookingId,
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $amount,
                        'product_data' => [
                            'name' => 'Réservation fictive AtypikHouse #' . $bookingId,
                            'description' => (string) ($booking['title'] ?? 'Séjour AtypikHouse'),
                        ],
                    ],
                ],
            ],
            'metadata' => [
                'booking_id' => (string) $bookingId,
                'project' => 'AtypikHouse démonstration académique',
            ],
        ];

        return $this->request('POST', 'checkout/sessions', $payload);
    }

    public function retrieveCheckoutSession(string $sessionId): array
    {
        if (!$this->isReady()) {
            return ['success' => false, 'error' => 'Stripe test n’est pas configuré.'];
        }
        if (!preg_match('/^cs_test_[A-Za-z0-9_]+$/', $sessionId)) {
            return ['success' => false, 'error' => 'Session Stripe invalide.'];
        }

        return $this->request('GET', 'checkout/sessions/' . rawurlencode($sessionId));
    }

    private function request(string $method, string $endpoint, array $payload = []): array
    {
        $url = 'https://api.stripe.com/v1/' . ltrim($endpoint, '/');
        $body = http_build_query($payload);

        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 12,
                CURLOPT_USERPWD => $this->secretKey . ':',
            ];
            if ($method === 'POST') {
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_POSTFIELDS] = $body;
            }
            curl_setopt_array($curl, $options);
            $response = curl_exec($curl);
            $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);
        } else {
            $headers = "Authorization: Basic " . base64_encode($this->secretKey . ':') . "\r\n";
            if ($method === 'POST') {
                $headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
            }
            $context = stream_context_create([
                'http' => [
                    'method' => $method,
                    'header' => $headers,
                    'content' => $method === 'POST' ? $body : '',
                    'timeout' => 12,
                    'ignore_errors' => true,
                ],
            ]);
            $response = @file_get_contents($url, false, $context);
            $statusLine = $http_response_header[0] ?? '';
            preg_match('/\s(\d{3})\s/', $statusLine, $matches);
            $status = (int) ($matches[1] ?? 0);
            $error = '';
        }

        if (!is_string($response) || $response === '') {
            return ['success' => false, 'error' => $error !== '' ? $error : 'Réponse Stripe vide.'];
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return ['success' => false, 'error' => 'Réponse Stripe illisible.'];
        }
        if ($status < 200 || $status >= 300) {
            return ['success' => false, 'error' => (string) ($data['error']['message'] ?? 'Stripe a refusé la demande.')];
        }

        $data['success'] = true;
        return $data;
    }
}
