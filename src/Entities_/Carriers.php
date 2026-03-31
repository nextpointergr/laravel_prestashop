<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;

class Carriers
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

    /**
     * Ορισμός ορίου αποτελεσμάτων
     */
    public function limit(int $limit): static
    {
        $this->query['limit'] = $limit;
        return $this;
    }

    /**
     * Ορισμός offset για pagination
     */
    public function offset(int $offset): static
    {
        $this->query['offset'] = $offset;
        return $this;
    }

    public function get(): array
    {
        return $this->client->request('carriers', 'get', $this->query);
    }

    public function count(): int
    {
        $originalQuery = $this->query;
        $this->limit(1);
        $response = $this->get();
        $this->query = $originalQuery;

        return $response['meta']['total'] ?? 0;
    }
}
