<?php

namespace Nextpointer\Prestashop\Sync;

use Nextpointer\Prestashop\Client\PrestashopClient;

class SyncManager
{
    public static function run(
        string $entity,
        PrestashopClient $client,
        callable $callback,
        array $baseQuery = []
    ): void {
        $cursor = CursorStore::get($entity);

        do {
            $query = array_merge($baseQuery, [
                'cursor' => $cursor,
                'limit' => 100,
            ]);

            $response = $client->request($entity, 'get', $query);

            $data = $response['data'] ?? [];
            $meta = $response['meta'] ?? [];

            foreach ($data as $row) {
                $callback($row);
            }

            $cursor = $meta['cursor_next'] ?? null;

            if ($cursor) {
                CursorStore::set($entity, $cursor);
            }

        } while (!empty($meta['has_more']));
    }

    public static function lazy(
        string $entity,
        PrestashopClient $client,
        array $baseQuery = []
    ): \Generator {
        $cursor = CursorStore::get($entity);

        do {
            $query = array_merge($baseQuery, [
                'cursor' => $cursor,
                'limit' => 100,
            ]);

            $response = $client->request($entity, 'get', $query);

            $data = $response['data'] ?? [];
            $meta = $response['meta'] ?? [];

            foreach ($data as $row) {
                yield $row;
            }

            $cursor = $meta['cursor_next'] ?? null;

            if ($cursor) {
                CursorStore::set($entity, $cursor);
            }

        } while (!empty($meta['has_more']));
    }
}