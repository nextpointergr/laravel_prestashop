<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;

class Countries
{
    protected PrestashopClient $client;
    protected array $query = [];

    public function __construct(PrestashopClient $client)
    {
        $this->client = $client;
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
        // Εδώ καλούμε τη μέθοδο 'get_countries' που περιμένει ο GeographyService σου
        $this->query['method'] = 'get_countries';
        return $this->client->request('geography', 'get', $this->query);
    }

    public function count(): int
    {
        $response = $this->get();
        return $response['meta']['total'] ?? 0;
    }
}
