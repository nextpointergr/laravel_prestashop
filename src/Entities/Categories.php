<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;
use Nextpointer\Prestashop\Sync\SyncManager;

class Categories
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

    public function get(): array
    {
        return $this->client->request('categories', 'get', $this->query);
    }

    public function create(array $data): array
    {
        return $this->client->request('categories', 'post', [], $data);
    }

    public function update(int $id, array $data): array
    {
        $data['id'] = $id;
        return $this->client->request('categories', 'post', [], $data);
    }

    public function delete(int $id): array
    {
        return $this->client->request('categories', 'delete', [
            'id' => $id
        ]);
    }

    public function sync(callable $callback): void
    {
        SyncManager::run('categories', $this->client, $callback, $this->query);
    }

    public function lazy(): \Generator
    {
        return SyncManager::lazy('categories', $this->client, $this->query);
    }
}