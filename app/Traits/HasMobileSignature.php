<?php

namespace App\Traits;

use App\Models\SignatureRequest;

trait HasMobileSignature
{
    public $signatureRequestToken = null;

    public $showSignatureModal = false;

    public $signatureStatus = 'pending';

    public function generateSignatureRequest($model, array $metadata = [])
    {
        $token = \Illuminate\Support\Str::random(32);

        $request = \App\Models\SignatureRequest::create([
            'token' => $token,
            'status' => 'pending',
            'signable_type' => get_class($model),
            'signable_id' => $model->id,
            'metadata' => $metadata,
            'expires_at' => now()->addMinutes(30),
        ]);

        $this->signatureRequestToken = $token;
        $this->signatureStatus = 'pending';
        $this->showSignatureModal = true;
    }

    public function checkSignatureStatus()
    {
        if (! $this->signatureRequestToken || $this->signatureStatus === 'completed') {
            return;
        }

        $request = SignatureRequest::where('token', $this->signatureRequestToken)->first();

        if ($request && $request->status === 'completed') {
            $this->signatureStatus = 'completed';
            $this->showSignatureModal = false;

            // Notification for Livewire
            $this->dispatch('signature-completed');

            // This is a hook that can be implemented in the component
            if (method_exists($this, 'onSignatureCompleted')) {
                $this->onSignatureCompleted($request);
            }
        }
    }

    public function getSignatureUrlProperty()
    {
        if (! $this->signatureRequestToken) {
            return null;
        }

        return route('signature.show', $this->signatureRequestToken);
    }
}
