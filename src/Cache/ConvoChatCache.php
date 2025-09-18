<?php

namespace ConvoChat\LaravelSmsGateway\Cache;

use Illuminate\Support\Facades\Cache;

class ConvoChatCache
{
    private const CACHE_TTL = [
        'devices' => 300,        // 5 minutos
        'credits' => 60,         // 1 minuto
        'accounts' => 600,       // 10 minutos
        'rates' => 3600,         // 1 hora
        'subscription' => 1800,  // 30 minutos
    ];

    public static function remember(string $type, string $key, callable $callback): mixed
    {
        $cacheKey = "convochat.{$type}.{$key}";
        $ttl = self::CACHE_TTL[$type] ?? 300;

        return Cache::remember($cacheKey, $ttl, $callback);
    }

    public static function forget(string $type, string $key): void
    {
        $cacheKey = "convochat.{$type}.{$key}";
        Cache::forget($cacheKey);
    }

    public static function invalidateType(string $type): void
    {
        $pattern = "convochat.{$type}.*";

        if (method_exists(Cache::store(), 'deleteByPattern')) {
            Cache::store()->deleteByPattern($pattern);
        } else {
            // Fallback para stores que no soportan patterns
            Cache::flush();
        }
    }

    public static function warmUp(): void
    {
        // Pre-cargar datos frecuentemente accedidos
        try {
            self::remember('devices', 'all', fn() => app('convochat.sms')->getDevices());
            self::remember('credits', 'balance', fn() => app('convochat.sms')->getCredits());
            self::remember('accounts', 'whatsapp', fn() => app('convochat.whatsapp')->getWhatsAppAccounts());
        } catch (\Exception $e) {
            // Silenciar errores durante el warming
            logger()->warning('ConvoChat cache warming failed: ' . $e->getMessage());
        }
    }
}