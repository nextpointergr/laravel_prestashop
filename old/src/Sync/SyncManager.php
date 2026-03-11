<?php

namespace Nextpointer\Prestashop\Sync;

use Nextpointer\Prestashop\Client\PrestashopClient;

class SyncManager
{
    public static function run(
        string $entity,
        PrestashopClient $client,
        callable $callback,
        array $baseQuery = [],
        string $method = 'get'
    ): void {
        $size = (int)($baseQuery['limit'] ?? 100);

        self::chunk(
            entity: $entity,
            client: $client,
            callback: function (array $rows) use ($callback) {
                foreach ($rows as $row) {
                    $callback($row);
                }
            },
            baseQuery: $baseQuery,
            method: $method,
            size: $size
        );
    }

    public static function chunk(
        string $entity,
        PrestashopClient $client,
        callable $callback,
        array $baseQuery = [],
        string $method = 'get',
        int $size = 100
    ): void {
        $offset = (int)($baseQuery['offset'] ?? 0);

        // guards για να μην κολλήσει ΠΟΤΕ
        $maxPages = (int)($baseQuery['max_pages'] ?? 100000); // τεράστιο default
        $page = 0;

        while (true) {
            $query = array_merge($baseQuery, [
                'limit'  => $size,
                'offset' => $offset,
            ]);

            // μην στέλνεις εσωτερικά flags στο PS API
            unset($query['max_pages']);

            $response = $client->request($entity, $method, $query);

            $data = $response['data'] ?? [];
            $meta = $response['meta'] ?? [];

       
            if (empty($data)) {
                break;
            }

            $callback($data);
            $hasMore = (bool)($meta['has_more'] ?? false);
            if (!$hasMore) {
                break;
            }

   
            if (count($data) < $size) {
                break;
            }

            $offset += $size;
            $page++;

          
            if ($page >= $maxPages) {
                break;
            }
        }
    }

    public static function lazy(
        string $entity,
        PrestashopClient $client,
        array $baseQuery = [],
        string $method = 'get'
    ): \Generator {
        $offset = (int)($baseQuery['offset'] ?? 0);
        $limit  = (int)($baseQuery['limit'] ?? 100);

        $maxPages = (int)($baseQuery['max_pages'] ?? 100000);
        $page = 0;

        while (true) {
            $query = array_merge($baseQuery, [
                'limit'  => $limit,
                'offset' => $offset,
            ]);

            unset($query['max_pages']);

            $response = $client->request($entity, $method, $query);

            $data = $response['data'] ?? [];
            $meta = $response['meta'] ?? [];

            if (empty($data)) {
                break;
            }

            foreach ($data as $row) {
                yield $row;
            }

            $hasMore = (bool)($meta['has_more'] ?? false);
            if (!$hasMore) {
                break;
            }

            if (count($data) < $limit) {
                break;
            }

            $offset += $limit;
            $page++;

            if ($page >= $maxPages) {
                break;
            }
        }
    }
}