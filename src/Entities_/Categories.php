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

    /**
     * Φιλτράρισμα με βάση το ID της κατηγορίας
     */
    public function id(int $id): static
    {
        $this->query['id'] = $id;
        return $this;
    }

    /**
     * Φιλτράρισμα για εγγραφές που άλλαξαν μετά από μια ημερομηνία
     */
    public function since(string $date): static
    {
        $this->query['since'] = $date;
        return $this;
    }

    /**
     * Χρήση cursor για incremental sync
     */
    public function cursor(string $cursor): static
    {
        $this->query['cursor'] = $cursor;
        return $this;
    }

    /**
     * Επιλογή συγκεκριμένων πεδίων (π.χ. ['id', 'name', 'active'])
     */
    public function only(array $fields): static
    {
        $this->query['only'] = implode(',', $fields);
        return $this;
    }

    /**
     * Ενεργοποιεί την επιστροφή του full_path (breadcrumb string)
     */
    public function withPath(): static
    {
        $this->query['path'] = 1;
        return $this;
    }

    /**
     * Όριο αποτελεσμάτων ανά σελίδα
     */
    public function limit(int $limit): static
    {
        $this->query['limit'] = $limit;
        return $this;
    }

    /**
     * Offset για κλασικό pagination
     */
    public function offset(int $offset): static
    {
        $this->query['offset'] = $offset;
        return $this;
    }

    /**
     * Εκτέλεση του GET request
     */
    public function get(): array
    {
        return $this->client->request('categories', 'get', $this->query);
    }

    /**
     * Δημιουργία νέας κατηγορίας
     */
    public function create(array $data): array
    {
        return $this->client->request('categories', 'post', [], $data);
    }

    /**
     * Ενημέρωση υπάρχουσας κατηγορίας (Upsert logic)
     */
    public function update(int $id, array $data): array
    {
        $data['id'] = $id;
        return $this->client->request('categories', 'post', [], $data);
    }

    /**
     * Διαγραφή κατηγορίας
     */
    public function delete(int $id): array
    {
        return $this->client->request('categories', 'delete', [
            'id' => $id
        ]);
    }

    /**
     * Μαζικός συγχρονισμός μέσω SyncManager
     */
    public function sync(callable $callback): void
    {
        SyncManager::run('categories', $this->client, $callback, $this->query);
    }

    /**
     * Generator για επεξεργασία μεγάλου όγκου δεδομένων (memory efficient)
     */
    public function lazy(): \Generator
    {
        return SyncManager::lazy('categories', $this->client, $this->query);
    }
}
