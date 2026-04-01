<?php

namespace Nextpointer\Prestashop\Entities;

use Nextpointer\Prestashop\Client\PrestashopClient;

class OrderStatuses
{
    protected PrestashopClient $client;
    protected array $query = [];

    public function __construct(PrestashopClient $client)
    {
        $this->client = $client;
    }

    /**
     * Φιλτράρισμα με βάση συγκεκριμένο ID
     */
    public function id(int $id): static
    {
        $this->query['id'] = $id;
        return $this;
    }

    /**
     * Φιλτράρισμα με βάση ημερομηνία τροποποίησης (Incremental Sync)
     */
    public function since(string $date): static
    {
        $this->query['since'] = $date;
        return $this;
    }

    /**
     * Επιλογή συγκεκριμένων πεδίων μόνο
     */
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

    /**
     * Εκτέλεση του αιτήματος
     */
    public function get(): array
    {
        // Προσοχή: Το string 'order_statuses' πρέπει να συμπίπτει
        // με το route που δήλωσες στον Dispatcher του PrestaShop module.
        return $this->client->request('order_status', 'get', $this->query);
    }

    /**
     * Επιστροφή του συνολικού αριθμού εγγραφών
     */
    public function count(): int
    {
        $originalQuery = $this->query;
        $this->limit(1);
        $response = $this->get();
        $this->query = $originalQuery;

        // Το meta['total'] έρχεται από το IBResponse::success που φτιάξαμε στο Service
        return $response['meta']['total'] ?? 0;
    }
}
