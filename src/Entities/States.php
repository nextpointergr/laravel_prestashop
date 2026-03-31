<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;

class States
{
    protected PrestashopClient $client;
    protected array $query = [];

    public function __construct(PrestashopClient $client)
    {
        $this->client = $client;
    }

    public function forCountry(int $id_country): static
    {
        $this->query['id_country'] = $id_country;
        return $this;
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

    public function get(): array
    {
        return $this->client->request('geography', 'get_states', $this->query);
    }
}
