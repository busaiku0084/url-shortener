<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUrlRequest;
use App\Models\Url;
use App\Services\UrlShortenerService;
use Illuminate\Http\JsonResponse;

class UrlController extends Controller
{
    public function __construct(
        private readonly UrlShortenerService $service,
    ) {}

    public function store(StoreUrlRequest $request): JsonResponse
    {
        $url = $this->service->shorten(
            originalUrl: $request->validated('url'),
            customCode: $request->validated('custom_code'),
            expiresAt: $request->validated('expires_at'),
        );

        return response()->json([
            'short_url' => url("/{$url->short_code}"),
            'short_code' => $url->short_code,
            'original_url' => $url->original_url,
            'expires_at' => $url->expires_at?->toIso8601String(),
        ], 201);
    }

    public function redirect(string $code)
    {
        $url = $this->service->resolve($code);

        if ($url === null) {
            abort(404, 'Short URL not found or expired.');
        }

        return redirect()->away($url->original_url);
    }

    public function show(string $code): JsonResponse
    {
        $url = Url::where('short_code', $code)->first();

        if ($url === null) {
            abort(404, 'Short URL not found.');
        }

        return response()->json([
            'short_code' => $url->short_code,
            'original_url' => $url->original_url,
            'short_url' => url("/{$url->short_code}"),
            'clicks' => $url->clicks,
            'is_expired' => $url->isExpired(),
            'expires_at' => $url->expires_at?->toIso8601String(),
            'created_at' => $url->created_at->toIso8601String(),
        ]);
    }
}
