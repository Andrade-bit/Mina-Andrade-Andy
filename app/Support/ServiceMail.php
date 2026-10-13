<?php

namespace App\Support;

use App\Models\EmailMessage;
use App\Models\User;
use App\Notifications\ServiceEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ServiceMail
{
    public function queue(User $user, string $kind, array $content, string $key, ?\DateTimeInterface $expires = null): EmailMessage
    {
        return EmailMessage::firstOrCreate(['deduplication_key' => hash('sha256', $key)], [
            'user_id' => $user->id, 'shop_id' => $user->shop_id, 'kind' => $kind,
            'payload' => [...$content, 'name' => $user->name], 'expires_at' => $expires,
            'available_at' => now(), 'status' => 'pending',
        ]);
    }

    public function deliver(int $id): bool
    {
        if (! config('saas.email_enabled') || config('mail.default') !== 'smtp') {
            return false;
        }

        return DB::transaction(function () use ($id): bool {
            $message = EmailMessage::lockForUpdate()->findOrFail($id);
            if (! in_array($message->status, ['pending', 'failed']) || $message->available_at?->isFuture()) {
                return false;
            }
            $user = User::find($message->user_id);
            if (! $user || (! $user->is_active && $message->kind !== 'account_change') || ! $message->payload || $message->expires_at?->isPast()) {
                $message->update(['status' => 'expired', 'payload' => null]);

                return false;
            }
            if (EmailMessage::where('sent_at', '>=', now()->startOfDay())->count() >= config('saas.daily_email_limit')) {
                return false;
            }
            try {
                Notification::sendNow($user, new ServiceEmail($message->payload));
                $message->update(['status' => 'sent', 'sent_at' => now(), 'payload' => null, 'attempts' => $message->attempts + 1]);

                return true;
            } catch (\Throwable) {
                $message->update(['status' => 'failed', 'attempts' => $message->attempts + 1, 'available_at' => now()->addMinutes(min(1440, 5 * (2 ** min(8, $message->attempts))))]);

                return false;
            }
        });
    }

    public function cancel(User $user, string $kind): void
    {
        EmailMessage::where('user_id', $user->id)->where('kind', $kind)->whereIn('status', ['pending', 'failed'])->update(['status' => 'cancelled', 'payload' => null]);
    }

    public static function url(string $path): string
    {
        return rtrim(config('app.url'), '/').'/'.ltrim($path, '/');
    }
}
