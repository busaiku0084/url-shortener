<?php

namespace App\Services;

use App\Models\Url;
use Illuminate\Support\Str;

class UrlShortenerService
{
    private const CODE_LENGTH = 6;
    private const MAX_ATTEMPTS = 10;

    public function shorten(string $originalUrl, ?string $customCode = null, ?string $expiresAt = null): Url
    {
        $shortCode = $customCode ?? $this->generateUniqueCode();

        return Url::create([
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
            'expires_at' => $expiresAt,
        ]);
    }

    public function resolve(string $shortCode): ?Url
    {
        $url = Url::where('short_code', $shortCode)->first();

        if ($url === null || $url->isExpired()) {
            return null;
        }

        $url->increment('clicks');

        return $url;
    }

    private function generateUniqueCode(): string
    {
        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            $code = Str::random(self::CODE_LENGTH);

            if (!Url::where('short_code', $code)->exists()) {
                return $code;
            }
        }

        throw new \RuntimeException('Failed to generate a unique short code.');
    }
}
