<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;

class Payments
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

    public function only(array $fields): static
    {
        $this->query['only'] = implode(',', $fields);
        return $this;
    }

    public function since(string $date): static
    {
        $this->query['since'] = $date;
        return $this;
    }

    public function get(): array
    {
        return $this->client->request('payments', 'get', $this->query);
    }
    public function limit(int $limit): static
    {
        $this->query['limit'] = $limit;
        return $this;
    }
    public function offset(int $offset): static
    {
        $this->query['offset'] = $offset;
        return $this;
    }

}
