<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebPushController extends Controller
{
    public function publicKey(): JsonResponse
    {
        return response()->json(['publicKey' => config('webpush.vapid_public_key')]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'colaborador_id' => 'required|integer|exists:colaboradores,id',
            'endpoint' => 'required|string',
            'p256dh' => 'required|string',
            'auth' => 'required|string',
        ]);

        $hash = hash('sha256', $data['endpoint']);

        PushSubscription::updateOrCreate(
            ['endpoint_hash' => $hash],
            [
                'colaborador_id' => $data['colaborador_id'],
                'endpoint' => $data['endpoint'],
                'endpoint_hash' => $hash,
                'p256dh' => $data['p256dh'],
                'auth' => $data['auth'],
            ]
        );

        return response()->json(['status' => 'subscribed']);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $data = $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('endpoint_hash', hash('sha256', $data['endpoint']))->delete();

        return response()->json(['status' => 'unsubscribed']);
    }
}
