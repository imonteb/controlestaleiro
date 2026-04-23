<?php

namespace App\Livewire\Concerns;

use App\Models\AutoSocorroKit;
use App\Models\Extintor;

trait HasScanner
{
    public $scanning = false;

    public $assetToken = '';

    public function checkAsset(): void
    {
        $token = trim($this->assetToken);
        if (! $token) {
            return;
        }

        $extintor = Extintor::where('qr_code_token', $token)->first();
        if ($extintor) {
            $extintor->update([
                'data_verificacao' => now(),
                'needs_restock' => false,
                'estado' => 'Conforme',
            ]);
            $this->reset(['assetToken', 'scanning']);
            session()->flash('success', 'Extintor verificado com sucesso.');

            return;
        }

        $kit = AutoSocorroKit::where('qr_code_token', $token)->first();
        if ($kit) {
            $kit->update(['needs_restock' => false]);
            $this->reset(['assetToken', 'scanning']);
            session()->flash('success', 'Kit de saúde verificado com success.');

            return;
        }

        session()->flash('error', 'QR Code não reconhecido.');
    }
}
