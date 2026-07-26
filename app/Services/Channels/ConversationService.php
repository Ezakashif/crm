<?php

namespace App\Services\Channels;

use App\Models\ChannelContact;
use App\Models\ChannelWebhookEvent;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Lead;
use App\Services\ActivityLogger;
use App\Services\Channels\DTOs\InboundLeadDTO;
use App\Services\Channels\DTOs\InboundMessageDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConversationService
{
    public function __construct(
        protected LeadMatchingService $matcher,
        protected LeadProcessingService $leads,
    ) {}

    /**
     * @return array{conversation: Conversation, message: ConversationMessage, lead: Lead, contact: ChannelContact}
     */
    public function ingestInboundMessage(InboundMessageDTO $dto, ChannelWebhookEvent $event): array
    {
        return DB::transaction(function () use ($dto, $event) {
            $companyId = (int) $event->company_id;

            if (filled($dto->providerMessageId)) {
                $existingMessage = ConversationMessage::query()
                    ->where('company_id', $companyId)
                    ->where('provider_message_id', $dto->providerMessageId)
                    ->first();

                if ($existingMessage) {
                    $conversation = $existingMessage->conversation()->firstOrFail();
                    $contact = $conversation->contact()->firstOrFail();
                    $lead = $conversation->lead ?: $contact->lead()->first();

                    if (! $lead) {
                        $lead = $this->ensureLeadFromMessage($dto, $event);
                    }

                    return [
                        'conversation' => $conversation,
                        'message' => $existingMessage,
                        'lead' => $lead,
                        'contact' => $contact,
                    ];
                }
            }

            $lead = $this->ensureLeadFromMessage($dto, $event);

            $contact = $this->matcher->upsertContact(
                provider: $dto->provider,
                companyId: $companyId,
                connection: $event->connection,
                lead: $lead,
                externalUserId: $dto->externalUserId,
                email: $dto->email ?? $lead->email,
                phone: $dto->phone ?? $lead->phone,
                displayName: $dto->displayName ?? $lead->name,
            );

            $threadId = $dto->externalThreadId ?: $dto->externalUserId;

            $conversation = Conversation::query()->firstOrNew([
                'company_id' => $companyId,
                'provider' => $dto->provider,
                'external_thread_id' => $threadId,
            ]);

            $conversation->fill([
                'channel_connection_id' => $event->channel_connection_id,
                'channel_contact_id' => $contact->id,
                'lead_id' => $lead->id,
                'status' => $conversation->exists ? $conversation->status : Conversation::STATUS_OPEN,
                'last_message_at' => now(),
                'last_inbound_at' => now(),
                'unread_count' => ($conversation->unread_count ?? 0) + 1,
            ]);
            $conversation->save();

            $message = ConversationMessage::query()->create([
                'company_id' => $companyId,
                'conversation_id' => $conversation->id,
                'channel_contact_id' => $contact->id,
                'direction' => ConversationMessage::DIRECTION_INBOUND,
                'type' => $dto->type,
                'provider_message_id' => $dto->providerMessageId,
                'body' => $dto->body,
                'status' => 'received',
                'sent_at' => now(),
                'meta' => $dto->meta,
            ]);

            ActivityLogger::log('channel.message_received', $conversation, [
                'provider' => $dto->provider->value,
                'message_id' => $message->id,
                'lead_id' => $lead->id,
                'webhook_event_id' => $event->id,
            ]);

            return compact('conversation', 'message', 'lead', 'contact');
        });
    }

    protected function ensureLeadFromMessage(InboundMessageDTO $dto, ChannelWebhookEvent $event): Lead
    {
        $matched = $this->matcher->matchLead($dto, (int) $event->company_id);

        if ($matched) {
            return $matched;
        }

        $name = $dto->displayName
            ?: ($dto->phone ?: ($dto->email ?: 'Channel contact '.Str::limit($dto->externalUserId, 12, '')));

        $result = $this->leads->process(new InboundLeadDTO(
            provider: $dto->provider,
            name: $name,
            email: $dto->email,
            phone: $dto->phone,
            notes: $dto->body !== '' ? Str::limit($dto->body, 500) : null,
            externalUserId: $dto->externalUserId,
            meta: $dto->meta,
        ), $event);

        return $result['lead'];
    }
}
