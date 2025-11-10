<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Wifi2BleSimulatorClient
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('wifi2ble.base_url'), '/');
        $this->apiKey = (string) config('wifi2ble.api_key');
        $this->timeout = (int) config('wifi2ble.timeout', 5);

        if ($this->baseUrl === '' || $this->apiKey === '') {
            throw new \RuntimeException('Wifi2Ble simulator configuration is missing a base URL or API key.');
        }
    }

    /**
     * @return array<int, string>
     * @throws RequestException|ConnectionException
     */
    public function listDesks(): array
    {
        $response = $this->client()->get($this->endpoint());

        return $response->throw()->json();
    }

    /**
     * @return array<string, mixed>
     * @throws RequestException|ConnectionException
     */
    public function getDesk(string $deskId): array
    {
        $response = $this->client()->get($this->endpoint("/{$deskId}"));

        return $response->throw()->json();
    }

    /**
     * @return array<string, mixed>
     * @throws RequestException|ConnectionException
     */
    public function getDeskCategory(string $deskId, string $category): array
    {
        $category = Str::lower($category);

        $response = $this->client()->get($this->endpoint("/{$deskId}/{$category}"));

        return $response->throw()->json();
    }

    /**
     * @return array<string, mixed>
     * @throws RequestException|ConnectionException
     */
    public function updateDeskState(string $deskId, array $payload): array
    {
        $response = $this->client()->put($this->endpoint("/{$deskId}/state"), $payload);

        return $response->throw()->json();
    }

    private function endpoint(string $path = ''): string
    {
        return "/api/v2/{$this->apiKey}/desks" . $path;
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout($this->timeout)
            ->retry(2, 100);
    }
}
