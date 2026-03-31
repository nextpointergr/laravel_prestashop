<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;
use Nextpointer\Prestashop\Sync\SyncManager;

class Products
{
    protected PrestashopClient $client;
    protected array $query = [];
    protected string $method = 'get';

    public function __construct(PrestashopClient $client)
    {
        $this->client = $client;
    }

    public function id(int $id): static
    {
        $this->query['id'] = $id;
        return $this;
    }

    public function ids(array $ids): static
    {
        $this->query['ids'] = implode(',', $ids);
        return $this;
    }

    public function since(string $date): static
    {
        $this->query['since'] = $date;
        return $this;
    }

    public function cursor(string $cursor): static
    {
        $this->query['cursor'] = $cursor;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->query['offset'] = $offset;
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->query['limit'] = $limit;
        return $this;
    }

    public function only(array $fields): static
    {
        $this->query['only'] = implode(',', $fields);
        return $this;
    }

    public function get(): array
    {
        return $this->client->request(
            'products',
            $this->method,
            $this->query
        );
    }

    public function create(array $data): array
    {
        return $this->client->request('products', 'post', [], $data);
    }

    public function update(int $id, array $data): array
    {
        $data['id'] = $id;
        return $this->client->request('products', 'post', [], $data);
    }

    public function delete(int $id, bool $force = false): array
    {
        return $this->client->request('products', 'delete', [
            'id' => $id,
            'force' => $force ? 1 : 0
        ]);
    }

    public function stock(int $id, int $quantity): array
    {
        return $this->client->request('products', 'stock', [], [
            'id' => $id,
            'quantity' => $quantity
        ]);
    }

    public function sync(callable $callback): void
    {
        SyncManager::run(
            entity: 'products',
            client: $this->client,
            callback: $callback,
            baseQuery: $this->query,
            method: $this->method
        );
    }

    public function chunk(int $size, callable $callback): void
    {
        SyncManager::chunk(
            entity: 'products',
            client: $this->client,
            callback: $callback,
            baseQuery: $this->query,
            method: $this->method,
            size: $size
        );
    }

    public function lazy(): \Generator
    {
        return SyncManager::lazy(
            entity: 'products',
            client: $this->client,
            baseQuery: $this->query,
            method: $this->method
        );
    }

    // Μέσα στην κλάση Nextpointer\Prestashop\Entities\Products

    public function triggerSync(): array
    {

        return $this->client->request('products', 'sync', [], []);


    }
}
