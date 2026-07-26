<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channel_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('provider', 50);
            $table->string('name');
            $table->string('status', 32)->default('disconnected')->index();
            $table->string('external_account_id')->nullable()->index();
            $table->string('external_page_id')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->text('webhook_secret')->nullable();
            $table->string('verify_token')->nullable();
            $table->timestamp('token_expires_at')->nullable()->index();
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('last_event_at')->nullable();
            $table->timestamp('last_error_at')->nullable();
            $table->unsignedInteger('error_count')->default(0);
            $table->string('last_error_message', 1000)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'provider', 'external_account_id']);
            $table->index(['company_id', 'provider', 'status']);
        });

        Schema::create('channel_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_connection_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('provider', 50)->index();
            $table->string('event_type', 100)->nullable();
            $table->string('idempotency_key')->nullable();
            $table->string('status', 32)->default('received')->index();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->json('headers')->nullable();
            $table->longText('payload');
            $table->string('signature')->nullable();
            $table->boolean('signature_valid')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'provider', 'idempotency_key']);
            $table->index(['channel_connection_id', 'status']);
        });

        Schema::create('channel_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_connection_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider', 50);
            $table->string('external_user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('display_name')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'provider', 'external_user_id']);
            $table->index(['company_id', 'email']);
            $table->index(['company_id', 'phone']);
            $table->index(['lead_id']);
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_connection_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('channel_contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider', 50);
            $table->string('external_thread_id')->nullable();
            $table->string('status', 32)->default('open')->index();
            $table->string('subject')->nullable();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamp('last_inbound_at')->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'provider', 'external_thread_id']);
            $table->index(['company_id', 'status', 'last_message_at']);
            $table->index(['assigned_to']);
        });

        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction', 16);
            $table->string('type', 32)->default('text');
            $table->string('provider_message_id')->nullable();
            $table->text('body')->nullable();
            $table->string('status', 32)->default('received');
            $table->timestamp('sent_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'provider_message_id']);
            $table->index(['conversation_id', 'sent_at']);
        });

        Schema::create('lead_channel_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_connection_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('channel_webhook_event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider', 50);
            $table->string('campaign_id')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('adset_id')->nullable();
            $table->string('ad_id')->nullable();
            $table->string('ad_name')->nullable();
            $table->string('form_id')->nullable();
            $table->string('form_name')->nullable();
            $table->string('page_id')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->unique(['lead_id', 'provider']);
            $table->index(['company_id', 'provider', 'campaign_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_channel_meta');
        Schema::dropIfExists('conversation_messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('channel_contacts');
        Schema::dropIfExists('channel_webhook_events');
        Schema::dropIfExists('channel_connections');
    }
};
