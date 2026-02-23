<?php

namespace Nextpointer\Prestashop\Sync;

use Illuminate\Support\Facades\Cache;

class CursorStore
{
    protected static function key(string $entity): string
    {
        return "prestashop_cursor_{$entity}";
    }

    public static function get(string $entity): ?string
    {
        return Cache::get(self::key($entity));
    }

    public static function set(string $entity, string $cursor): void
    {
        Cache::forever(self::key($entity), $cursor);
    }

    public static function reset(string $entity): void
    {
        Cache::forget(self::key($entity));
    }
}