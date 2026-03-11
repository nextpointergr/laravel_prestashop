<?php

namespace Nextpointer\Prestashop\Client;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Nextpointer\Prestashop\Exceptions\ApiException;
use Nextpointer\Prestashop\Entities\Products;
use Nextpointer\Prestashop\Entities\Orders;
use Nextpointer\Prestashop\Entities\Categories;
use Nextpointer\Prestashop\Entities\Carriers;
use Nextpointer\Prestashop\Entities\Taxes;
use Nextpointer\Prestashop\Entities\Payments;

class PrestashopClient
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;

    public function __construct(string $baseUrl, string $apiKey, int $timeout = 15)
    {
        if (!$baseUrl || !$apiKey) {
            throw new ApiException('Prestashop base_url or api_key not configured.');
        }

        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey  = $apiKey;
        $this->timeout = $timeout;
    }

    /* ========================================================= */
    /* ======================= CORE REQUEST ===================== */
    /* ========================================================= */

public function request(
    string $entity,
    string $method,
    array $query = [],
    array $payload = []
): array {

    $url = $this->baseUrl . '/module/psapi/api';

    $query = array_merge($query, [
        'entity' => $entity,
        'method' => $method,
    ]);

    try {

        $http = Http::timeout($this->timeout)
            ->retry(
                config('prestashop.retry.times', 3),
                config('prestashop.retry.sleep', 200),
                throw: false
            )
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Nextpointer-Token' => $this->apiKey,
                'Authorization' => $this->apiKey, // fallback
            ]);

        $response = empty($payload)
            ? $http->get($url, $query)
            : $http->post($url . '?' . http_build_query($query), $payload);

    } catch (\Exception $e) {
        throw new ApiException(
            'Connection to Prestashop failed: ' . $e->getMessage(),
            0
        );
    }

    if (!$response->successful()) {
        throw new ApiException(
            $response->body(),
            $response->status()
        );
    }

    $json = $response->json();

    if (!is_array($json)) {
        throw new ApiException('Invalid JSON response from Prestashop.');
    }

    return [
        'data' => $json['data'] ?? [],
        'meta' => $json['meta'] ?? [],
        'raw'  => $json,
    ];
}

    /* ========================================================= */
    /* ===================== RESPONSE NORMALIZER ================ */
    /* ========================================================= */

    protected function normalizeResponse(array $json): array
    {
        if (isset($json['success']) && $json['success'] === false) {
            throw new ApiException(
                $json['message'] ?? 'Prestashop API error',
                400
            );
        }

        return [
            'data' => $json['data'] ?? [],
            'meta' => $json['meta'] ?? [],
            'raw'  => $json,
        ];
    }

    /* ========================================================= */
    /* ======================= ENTITY ACCESSORS ================= */
    /* ========================================================= */

    public function products(): Products
    {
        return new Products($this);
    }

    public function orders(): Orders
    {
        return new Orders($this);
    }

    public function categories(): Categories
    {
        return new Categories($this);
    }

    public function carriers(): Carriers
    {
        return new Carriers($this);
    }

    public function taxes(): Taxes
    {
        return new Taxes($this);
    }

    public function payments(): Payments
    {
        return new Payments($this);
    }
}