<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;
use Nextpointer\Prestashop\Sync\SyncManager;

class Products
{
    protected PrestashopClient $client;
    protected array $query = [];

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

    public function only(array $fields): static
    {
        $this->query['only'] = implode(',', $fields);
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->query['limit'] = $limit;
        return $this;
    }

    public function get(): array
    {
        return $this->client->request('products', 'get', $this->query);
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
        SyncManager::run('products', $this->client, $callback, $this->query);
    }

    public function lazy(): \Generator
    {
        return SyncManager::lazy('products', $this->client, $this->query);
    }
}