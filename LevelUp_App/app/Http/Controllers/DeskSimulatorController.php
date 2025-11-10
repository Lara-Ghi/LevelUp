<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDeskStateRequest;
use App\Services\Wifi2BleSimulatorClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DeskSimulatorController extends Controller
{
    public function __construct(private readonly Wifi2BleSimulatorClient $client)
    {
    }

    public function index(): JsonResponse
    {
        try {
            return response()->json($this->client->listDesks());
        } catch (Throwable $exception) {
            return $this->errorFromSimulator($exception);
        }
    }

    public function show(string $deskId): JsonResponse
    {
        try {
            return response()->json($this->client->getDesk($deskId));
        } catch (Throwable $exception) {
            return $this->errorFromSimulator($exception);
        }
    }

    public function showCategory(string $deskId, string $category): JsonResponse
    {
        try {
            return response()->json($this->client->getDeskCategory($deskId, $category));
        } catch (Throwable $exception) {
            return $this->errorFromSimulator($exception);
        }
    }

    public function updateState(UpdateDeskStateRequest $request, string $deskId): JsonResponse
    {
        try {
            $payload = $request->validated();

            return response()->json($this->client->updateDeskState($deskId, $payload));
        } catch (Throwable $exception) {
            return $this->errorFromSimulator($exception);
        }
    }

    private function errorFromSimulator(Throwable $exception): JsonResponse
    {
        if ($exception instanceof RequestException && $exception->response) {
            return response()->json(
                $exception->response->json() ?? ['error' => 'Simulator request failed'],
                $exception->response->status()
            );
        }

        if ($exception instanceof ConnectionException) {
            return response()->json(
                ['error' => 'Simulator service is unavailable'],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        report($exception);

        return response()->json(
            ['error' => 'Unexpected simulator error'],
            Response::HTTP_BAD_GATEWAY
        );
    }
}
