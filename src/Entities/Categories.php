<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;
use Nextpointer\Prestashop\Sync\SyncManager;

class Categories
{
    protected PrestashopClient $client;
    protected array $query = [];

    public function __construct(PrestashopClient $client, array $query = [])
    {
        $this->client = $client;
        $this->query = $query;
    }

    // 🔥 IMMUTABLE HELPER
    protected function with(array $params): static
    {
        return new static(
            $this->client,
            array_merge($this->query, $params)
        );
    }

    public function id(int $id): static
    {
        return $this->with(['id' => $id]);
    }

    public function since(string $date): static
    {
        return $this->with(['since' => $date]);
    }

    public function until(string $date): static
    {
        return $this->with(['until' => $date]);
    }

    public function cursor(string $cursor): static
    {
        return $this->with(['cursor' => $cursor]);
    }

    public function only(array $fields): static
    {
        return $this->with([
            'only' => implode(',', $fields)
        ]);
    }

    public function limit(int $limit): static
    {
        return $this->with(['limit' => $limit]);
    }

    public function offset(int $offset): static
    {
        return $this->with(['offset' => $offset]);
    }

    public function get(): array
    {
        return $this->client->request(
            'categories',
            'get',
            $this->query
        );
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
