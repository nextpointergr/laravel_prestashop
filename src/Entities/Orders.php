<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;
use Nextpointer\Prestashop\Sync\SyncManager;

class Orders
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

    public function limit(int $limit): static
    {
        $this->query['limit'] = $limit;
        return $this;
    }

    public function get(): array
    {
      return $this->client->request(
        'orders',
        $this->method,
        $this->query
    );
    }

public function offset(int $offset): static
{
    $this->query['offset'] = $offset;
    return $this;
}

	 public function specific(): static
	{
		$this->method = 'specific';
		return $this;
	}

    public function status(int $orderId, int $stateId, ?string $tracking = null): array
    {
        $payload = [
            'id' => $orderId,
            'id_order_state' => $stateId,
        ];

        if ($tracking) {
            $payload['tracking_number'] = $tracking;
        }

        return $this->client->request('orders', 'status', [], $payload);
    }

   public function sync(callable $callback): void
    {
        SyncManager::run(
            entity: 'orders',
            client: $this->client,
            callback: $callback,
            baseQuery: $this->query,
            method: $this->method
        );
    }

   public function lazy(): \Generator
    {
        return SyncManager::lazy(
            entity: 'orders',
            client: $this->client,
            baseQuery: $this->query,
            method: $this->method
        );
    }

    public function only(array $fields): static
    {
        $this->query['only'] = implode(',', $fields);
        return $this;
    }
	
	public function chunk(int $size, callable $callback): void
    {
        SyncManager::chunk(
            entity: 'orders',
            client: $this->client,
            callback: $callback,
            baseQuery: $this->query,
            method: $this->method,
            size: $size
        );
    }

}