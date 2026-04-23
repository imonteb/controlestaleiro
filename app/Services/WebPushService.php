<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    private WebPush $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => config('webpush.vapid_subject'),
                'publicKey' => config('webpush.vapid_public_key'),
                'privateKey' => config('webpush.vapid_private_key'),
            ],
        ];

        $this->webPush = new WebPush($auth);
        $this->webPush->setReuseVAPIDHeaders(true);
        $this->webPush->setAutomaticPadding(false);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function sendToColaborador(int $colaboradorId, array $payload): void
    {
        $subscriptions = PushSubscription::where('colaborador_id', $colaboradorId)->get();

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'keys' => ['p256dh' => $sub->p256dh, 'auth' => $sub->auth],
                'contentEncoding' => 'aesgcm',
            ]);

            $this->webPush->queueNotification($subscription, json_encode($payload));
        }

        $staleIds = [];

        foreach ($this->webPush->flush() as $report) {
            if ($report->isSuccess()) {
                continue;
            }

            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSubscriptionExpired()) {
                $staleIds[] = $endpoint;
            } else {
                Log::error('WebPush failed', [
                    'colaborador_id' => $colaboradorId,
                    'endpoint' => $endpoint,
                    'reason' => $report->getReason(),
                    'response' => $report->getResponse()?->getBody()->__toString(),
                ]);
            }
        }

        if (! empty($staleIds)) {
            PushSubscription::whereIn('endpoint', $staleIds)->delete();
        }
    }

    /**
     * Notifica a todos los colaboradores que tengan suscripción activa.
     *
     * @param  array<string, mixed>  $payload
     */
    public function broadcast(array $payload): void
    {
        $subscriptions = PushSubscription::all();

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'keys' => ['p256dh' => $sub->p256dh, 'auth' => $sub->auth],
                'contentEncoding' => 'aesgcm',
            ]);

            $this->webPush->queueNotification($subscription, json_encode($payload));
        }

        foreach ($this->webPush->flush() as $report) {
            if (! $report->isSuccess() && $report->isSubscriptionExpired()) {
                PushSubscription::where('endpoint', $report->getRequest()->getUri()->__toString())->delete();
            }
        }
    }
}
