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
    protected function with(array $params): static
    {
        $clone = clone $this;
        $clone->query = array_merge($this->query, $params);
        return $clone;
    }
    public function id(int $id): static
    {
        return $this->with(['id' => $id]);
    }

    public function since(string $date): static
    {
        return $this->with(['since' => $date]);
    }

    public function cursor(string $cursor): static
    {
        return $this->with(['cursor' => $cursor]);
    }

    public function limit(int $limit): static
    {
        return $this->with(['limit' => $limit]);
    }

    public function offset(int $offset): static
    {
        return $this->with(['offset' => $offset]);
    }

    public function only(array $fields): static
    {
        return $this->with([
            'only' => implode(',', $fields)
        ]);
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
    public function updateOrCreate(array $data, ?int $id = null): array
    {
        // 1. Αν δοθεί ID → update
        if ($id !== null) {
            $data['id'] = $id;
            return $this->client->request('products', 'post', [], $data);
        }
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
}
