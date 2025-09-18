<?php

namespace ConvoChat\LaravelSmsGateway\Security;

use Illuminate\Support\Facades\Cache;

class RateLimiter
{
    public function attempt(string $key, int $maxAttempts = 60, int $decayMinutes = 1): bool
    {
        $cacheKey = "rate_limit:{$key}";
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= $maxAttempts) {
            return false;
        }

        Cache::put($cacheKey, $attempts + 1, now()->addMinutes($decayMinutes));

        return true;
    }

    public function tooManyAttempts(string $key, int $maxAttempts = 60): bool
    {
        return !$this->attempt($key, $maxAttempts, 0);
    }

    public function remaining(string $key, int $maxAttempts = 60): int
    {
        $cacheKey = "rate_limit:{$key}";
        $attempts = Cache::get($cacheKey, 0);

        return max(0, $maxAttempts - $attempts);
    }

    public function clear(string $key): void
    {
        Cache::forget("rate_limit:{$key}");
    }

    public function retriesLeft(string $key, int $maxAttempts = 60): int
    {
        return $this->remaining($key, $maxAttempts);
    }
}