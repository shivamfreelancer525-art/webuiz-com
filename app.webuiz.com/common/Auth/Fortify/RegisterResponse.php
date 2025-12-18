<?php

namespace Common\Auth\Fortify;

use Common\Core\Bootstrap\BootstrapData;
use Common\Core\Bootstrap\MobileBootstrapData;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): JsonResponse
    {
        try {
            $response = [
                'status' => $request->user()->hasVerifiedEmail()
                    ? 'success'
                    : 'needs_email_verification',
            ];

            // for mobile
            if ($request->has('token_name')) {
                $bootstrapData = app(MobileBootstrapData::class)->init();
                $bootstrapData->refreshToken($request->get('token_name'));
                $response['bootstrapData'] = $bootstrapData->get();

                // for web
            } else {
                $bootstrapData = app(BootstrapData::class)->init();
                $response['bootstrapData'] = $bootstrapData->getEncoded();
            }

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('RegisterResponse error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return a minimal response to prevent 500 error
            return response()->json([
                'status' => $request->user()->hasVerifiedEmail()
                    ? 'success'
                    : 'needs_email_verification',
                'bootstrapData' => json_encode(['user' => $request->user()->toArray()]),
            ], 200);
        }
    }
}
