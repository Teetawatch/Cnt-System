<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FcmService
{
    private string $projectId;
    private string $serviceAccountPath;

    public function __construct()
    {
        $this->projectId = config('services.fcm.project_id', 'workcnt-68512');
        $this->serviceAccountPath = config('services.fcm.service_account_path', '');
    }

    /**
     * Send notification to all registered FCM tokens
     */
    public function sendToAll(string $title, string $body, array $data = []): void
    {
        $tokens = FcmToken::pluck('token')->toArray();

        if (empty($tokens)) {
            Log::info('[FCM] No tokens registered, skipping notification');
            return;
        }

        $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send notification to specific tokens (one-by-one via FCM v1 API)
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): void
    {
        $accessToken = $this->getOAuth2Token();

        if (!$accessToken) {
            Log::error('[FCM] Cannot obtain OAuth2 access token — check FCM_SERVICE_ACCOUNT_PATH in .env');
            return;
        }

        $success = 0;
        $failure = 0;

        foreach ($tokens as $token) {
            $result = $this->sendV1($accessToken, $token, $title, $body, $data);
            if ($result) {
                $success++;
            } else {
                $failure++;
            }
        }

        Log::info('[FCM] Batch sent', ['success' => $success, 'failure' => $failure]);
    }

    /**
     * Send a single message via FCM v1 HTTP API
     */
    private function sendV1(string $accessToken, string $token, string $title, string $body, array $data): bool
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'sound'        => 'default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'channel_id'   => 'cnt_events_channel',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
                'data' => array_map('strval', $data),
            ],
        ];

        try {
            $response = Http::withToken($accessToken)
                ->post($url, $payload);

            if ($response->successful()) {
                return true;
            }

            $error = $response->json('error', []);
            $errorCode = $error['details'][0]['errorCode'] ?? $error['status'] ?? 'UNKNOWN';

            // Remove unregistered/invalid tokens
            if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                FcmToken::where('token', $token)->delete();
                Log::info('[FCM] Removed stale token', ['code' => $errorCode]);
            } else {
                Log::warning('[FCM] Send failed', ['code' => $errorCode, 'token_prefix' => substr($token, 0, 20)]);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('[FCM] HTTP error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get OAuth2 access token from service account (cached 55 min)
     */
    private function getOAuth2Token(): ?string
    {
        return Cache::remember('fcm_oauth2_token', 55 * 60, function () {
            $path = $this->serviceAccountPath;

            if (empty($path) || !file_exists($path)) {
                Log::error('[FCM] Service account file not found: ' . $path);
                return null;
            }

            $serviceAccount = json_decode(file_get_contents($path), true);
            if (!$serviceAccount) {
                Log::error('[FCM] Invalid service account JSON');
                return null;
            }

            $jwt = $this->generateJWT($serviceAccount);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if (!$response->successful()) {
                Log::error('[FCM] OAuth2 token request failed', $response->json() ?? []);
                return null;
            }

            return $response->json('access_token');
        });
    }

    /**
     * Generate a signed JWT for service account OAuth2
     */
    private function generateJWT(array $serviceAccount): string
    {
        $now = time();

        $header = base64url_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claim  = base64url_encode(json_encode([
            'iss'   => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signingInput = $header . '.' . $claim;
        openssl_sign($signingInput, $signature, $serviceAccount['private_key'], OPENSSL_ALGO_SHA256);

        return $signingInput . '.' . base64url_encode($signature);
    }
}

if (!function_exists('base64url_encode')) {
    function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
