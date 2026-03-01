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
        $size = $baseQuery['limit'] ?? 100;

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
        // αν έχεις offset στο baseQuery, το σεβόμαστε
        $offset = (int)($baseQuery['offset'] ?? 0);

        while (true) {
            $query = array_merge($baseQuery, [
                'limit'  => $size,
                'offset' => $offset,
            ]);

            $response = $client->request($entity, $method, $query);

            $data = $response['data'] ?? [];
            $meta = $response['meta'] ?? [];

            if (!empty($data)) {
                $callback($data);
            }

            $countThisPage = count($data);

            // Τερματισμός όταν πάρουμε λιγότερα από limit
            if ($countThisPage < $size) {
                break;
            }

            $offset += $size;

            // Προαιρετικό: αν το API δίνει total count, μπορείς να σταματήσεις νωρίτερα
            if (isset($meta['count']) && is_numeric($meta['count']) && $offset >= (int)$meta['count']) {
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

        while (true) {
            $query = array_merge($baseQuery, [
                'limit'  => $limit,
                'offset' => $offset,
            ]);

            $response = $client->request($entity, $method, $query);

            $data = $response['data'] ?? [];
            $meta = $response['meta'] ?? [];

            foreach ($data as $row) {
                yield $row;
            }

            $countThisPage = count($data);

            if ($countThisPage < $limit) {
                break;
            }

            $offset += $limit;

            if (isset($meta['count']) && is_numeric($meta['count']) && $offset >= (int)$meta['count']) {
                break;
            }
        }
    }
}