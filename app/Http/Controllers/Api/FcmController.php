<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    /**
     * Register or update FCM token from mobile app
     */
    public function registerToken(Request $request)
    {
        $request->validate([
            'token'     => 'required|string|max:512',
            'platform'  => 'nullable|in:android,ios',
            'device_id' => 'nullable|string|max:255',
        ]);

        FcmToken::register(
            token: $request->token,
            platform: $request->platform ?? 'android',
            deviceId: $request->device_id,
        );

        return response()->json(['message' => 'Token registered']);
    }

    /**
     * Remove FCM token (when user uninstalls or logs out)
     */
    public function removeToken(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        FcmToken::where('token', $request->token)->delete();
        return response()->json(['message' => 'Token removed']);
    }
}
