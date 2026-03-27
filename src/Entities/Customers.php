<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;
use Nextpointer\Prestashop\Sync\SyncManager;

class Customers
{
    protected PrestashopClient $client;
    protected array $query = [];
    protected string $method = 'get';

    public function __construct(PrestashopClient $client)
    {
        $this->client = $client;
    }

    /**
     * Ορίζει το ID του πελάτη για το query (χρήσιμο για Get ή Delete)
     */
    public function id(int $id): static
    {
        $this->query['id'] = $id;
        return $this;
    }

    /**
     * Ορίζει το email για αναζήτηση ή διαγραφή
     */
    public function email(string $email): static
    {
        $this->query['email'] = $email;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->query['offset'] = $offset;
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->query['limit'] = $limit;
        return $this;
    }

    /**
     * Λήψη πελατών (GET)
     */
    public function get(): array
    {
        return $this->client->request(
            'customers',
            'get',
            $this->query
        );
    }

    /**
     * Δημιουργία ή Ενημέρωση (UPSERT)
     * Στο PrestaShop service σου, το 'post' κάνει τη δουλειά του upsert.
     */
    public function upsert(array $data): array
    {
        // Αν έχει οριστεί id μέσω της μεθόδου ->id(), το περνάμε στο payload
        if (isset($this->query['id'])) {
            $data['id'] = $this->query['id'];
        }

        return $this->client->request('customers', 'post', [], $data);
    }

    /**
     * Διαγραφή πελάτη (DELETE)
     * Δέχεται ID ή χρησιμοποιεί το ID/Email που ορίστηκε προηγουμένως.
     */
    public function delete(?int $id = null): array
    {
        $params = $this->query;
        if ($id) {
            $params['id'] = $id;
        }

        return $this->client->request('customers', 'delete', $params);
    }

    // --- Helper Sync Methods (Όπως στο Products) ---

    public function sync(callable $callback): void
    {
        SyncManager::run(
            entity: 'customers',
            client: $this->client,
            callback: $callback,
            baseQuery: $this->query,
            method: $this->method
        );
    }

    public function chunk(int $size, callable $callback): void
    {
        SyncManager::chunk(
            entity: 'customers',
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
            entity: 'customers',
            client: $this->client,
            baseQuery: $this->query,
            method: $this->method
        );
    }
}
