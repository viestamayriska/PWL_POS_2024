<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            // Remove token
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

            if ($removeToken) {
                // Return JSON response
                return response()->json([
                    'success' => true,
                    'message' => 'Logout Berhasil!'
                ]);
            }
        } catch (JWTException $e) {
            // Handle token invalidation failure
            return response()->json([
                'success' => false,
                'message' => 'Logout failed. Could not invalidate token.'
            ], 500);
        }
    }
}
