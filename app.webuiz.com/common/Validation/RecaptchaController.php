<?php namespace Common\Validation;

use Common\Core\BaseController;
use Common\Settings\Settings;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class RecaptchaController extends BaseController
{
    public function __construct(
        protected Request $request,
        protected Client $http,
        protected Settings $settings,
    ) {
    }

    public function verify()
    {
        $this->validate($this->request, [
            'token' => 'required|string',
        ]);

        $secretKey = $this->settings->get('recaptcha.secret_key');
        
        // Check if secret key is configured
        if (empty($secretKey)) {
            \Log::warning('reCAPTCHA verification failed: Secret key not configured');
            return $this->success(['success' => false]);
        }

        try {
            $token = $this->request->get('token');
            $remoteIp = $this->request->getClientIp();
            
            $response = $this->http->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'form_params' => [
                        'response' => $token,
                        'secret' => $secretKey,
                        'remoteip' => $remoteIp,
                    ],
                    'timeout' => 10,
                ],
            );

            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            // Log the full response for debugging (remove in production if needed)
            \Log::info('reCAPTCHA verification response', [
                'success' => $responseData['success'] ?? null,
                'score' => $responseData['score'] ?? null,
                'error_codes' => $responseData['error-codes'] ?? null,
                'action' => $responseData['action'] ?? null,
                'challenge_ts' => $responseData['challenge_ts'] ?? null,
            ]);

            // Check if response is valid
            if (!isset($responseData['success'])) {
                \Log::warning('reCAPTCHA verification failed: Invalid response', [
                    'response' => $responseData,
                    'response_body' => $responseBody,
                ]);
                return $this->success(['success' => false]);
            }

            // Check if Google says it's successful first
            if ($responseData['success'] !== true) {
                // Check for error codes
                $errorCodes = $responseData['error-codes'] ?? [];
                \Log::warning('reCAPTCHA verification failed: Google returned false', [
                    'error_codes' => $errorCodes,
                    'full_response' => $responseData,
                ]);
                // Return error codes to frontend for debugging
                return $this->success([
                    'success' => false,
                    'error_codes' => $errorCodes,
                    'message' => $this->getErrorMessage($errorCodes),
                ]);
            }

            // If we have error codes even though success is true, still fail
            if (isset($responseData['error-codes']) && !empty($responseData['error-codes'])) {
                \Log::warning('reCAPTCHA verification failed: Error codes present', [
                    'error_codes' => $responseData['error-codes'],
                    'success' => $responseData['success'],
                ]);
                return $this->success(['success' => false]);
            }

            // For reCAPTCHA v3, check score (0.0 to 1.0, higher is better)
            // For reCAPTCHA v2, success is enough (no score field)
            if (isset($responseData['score'])) {
                // reCAPTCHA v3: require score >= 0.1 (lowered threshold for better UX)
                // Score of 0.1-0.3 is suspicious but acceptable, 0.3+ is good, 0.9+ is excellent
                $score = (float) $responseData['score'];
                $success = $score >= 0.1;
                
                if (!$success) {
                    \Log::warning('reCAPTCHA verification failed: Score too low', [
                        'score' => $score,
                        'threshold' => 0.1,
                        'action' => $responseData['action'] ?? null,
                    ]);
                } else {
                    \Log::info('reCAPTCHA verification passed', [
                        'score' => $score,
                        'action' => $responseData['action'] ?? null,
                    ]);
                }
            } else {
                // reCAPTCHA v2: success is enough
                $success = true;
                \Log::info('reCAPTCHA v2 verification passed');
            }

            return $this->success(['success' => $success]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            \Log::error('reCAPTCHA verification HTTP error', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            return $this->success(['success' => false]);
        } catch (\Exception $e) {
            \Log::error('reCAPTCHA verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->success(['success' => false]);
        }
    }

    /**
     * Get human-readable error message from error codes
     */
    private function getErrorMessage(array $errorCodes): string
    {
        $messages = [
            'missing-input-secret' => 'The secret parameter is missing. Please configure reCAPTCHA secret key in admin settings.',
            'invalid-input-secret' => 'The secret parameter is invalid or malformed. Please check your reCAPTCHA secret key in admin settings.',
            'missing-input-response' => 'The response parameter is missing.',
            'invalid-input-response' => 'The response parameter is invalid or malformed. The token may be expired or invalid.',
            'bad-request' => 'The request is invalid or malformed.',
            'timeout-or-duplicate' => 'The response is no longer valid: either is too old or has been used previously.',
            'browser-error' => 'reCAPTCHA encountered a browser error. This usually means: 1) The site key is incorrect or doesn\'t match the domain, 2) You\'re using a v2 key with v3 API (or vice versa), 3) The domain (localhost) is not whitelisted in Google reCAPTCHA console. Please check your reCAPTCHA configuration in admin settings.',
        ];

        if (empty($errorCodes)) {
            return 'Unknown reCAPTCHA error.';
        }

        $firstError = $errorCodes[0];
        return $messages[$firstError] ?? "reCAPTCHA error: {$firstError}";
    }
}
