<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class VapidController extends Controller
{
    public function publicKey(): JsonResponse
    {
        return response()->json([
            'public_key' => Setting::get('vapid_public_key'),
        ]);
    }
}
