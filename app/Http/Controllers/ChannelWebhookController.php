<?php

namespace App\Http\Controllers;

use App\Models\ChannelConnection;
use App\Services\Channels\WebhookIngestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChannelWebhookController extends Controller
{
    public function __construct(
        protected WebhookIngestionService $ingestion,
    ) {}

    public function inbound(Request $request, string $uuid): JsonResponse|Response
    {
        $connection = ChannelConnection::withoutCompanyScope()
            ->where('uuid', $uuid)
            ->first();

        if (! $connection) {
            return response()->json(['message' => 'Unknown channel connection.'], 404);
        }

        $company = $connection->company()->first();

        if (! $company) {
            return response()->json(['message' => 'Channel company is unavailable.'], 404);
        }

        $signature = $request->header('X-Channel-Signature')
            ?? $request->header('X-Hub-Signature-256')
            ?? $request->header('X-Signature');

        $event = $this->ingestion->ingest(
            company: $company,
            provider: $connection->provider,
            payload: $request->getContent() ?: '{}',
            connection: $connection,
            signature: $signature,
            eventType: $request->header('X-Channel-Event', 'inbound'),
            headers: [
                'content_type' => $request->header('Content-Type'),
                'user_agent' => $request->userAgent(),
            ],
        );

        if ($event->signature_valid === false) {
            return response()->json([
                'message' => 'Invalid signature.',
                'event_id' => $event->uuid,
            ], 401);
        }

        return response()->json([
            'message' => 'accepted',
            'event_id' => $event->uuid,
            'status' => $event->status->value,
        ], 202);
    }
}
