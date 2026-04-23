<?php

namespace App\Http\Controllers;

use App\Models\SignatureRequest;
use App\Services\SignatureService;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    public function __construct(private SignatureService $signatures) {}

    public function show(string $token)
    {
        $signRequest = SignatureRequest::where('token', $token)->firstOrFail();

        if ($signRequest->isExpired()) {
            return view('signature.expired');
        }

        if ($signRequest->isCompleted()) {
            return view('signature.completed');
        }

        return view('signature.public-sign', [
            'request' => $signRequest,
            'signable' => $signRequest->signable,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $signRequest = SignatureRequest::where('token', $token)->firstOrFail();

        if ($signRequest->isExpired() || $signRequest->isCompleted()) {
            return response()->json(['error' => 'Solicitud no válida o expirada'], 403);
        }

        $request->validate([
            'signature' => 'required|string',
        ]);

        $path = $this->signatures->store($request->input('signature'));

        $signable = $signRequest->signable;

        if ($signable instanceof \App\Models\User) {
            $signable->update(['signature' => $path]);
        } elseif ($signable instanceof \App\Models\FerramentaLog) {
            $signable->update(['assinatura_path' => $path]);
        } elseif ($signable instanceof \App\Models\EpiEntrega) {
            $signable->update(['firma' => $path]);

            // Aplicar firma a todas as entregas do batch
            $batchIds = $signRequest->metadata['batch_entrega_ids'] ?? [];
            $otherIds = array_filter($batchIds, fn ($id) => $id !== $signable->id);
            if (! empty($otherIds)) {
                \App\Models\EpiEntrega::whereIn('id', $otherIds)->update(['firma' => $path]);
            }
        }

        $signRequest->update([
            'signature_data' => $path,
            'status' => 'completed',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'completed_at' => now(),
        ]);

        try {
            \App\Models\AppNotification::where('url', 'like', '%'.$token.'%')
                ->update(['activa' => false]);
        } catch (\Exception) {
            // columna url aún no existe en producción
        }

        return response()->json(['success' => true]);
    }

    public function status(string $token)
    {
        $signRequest = SignatureRequest::where('token', $token)->first();

        if (! $signRequest) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json(['status' => $signRequest->status]);
    }
}
