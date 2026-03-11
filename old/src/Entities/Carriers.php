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

    public function get(): array
    {
        return $this->client->request('carriers', 'get', $this->query);
    }
}