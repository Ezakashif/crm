<?php

namespace App\Providers;

use App\Services\Channels\Adapters\GenericWebhookAdapter;
use App\Services\Channels\ChannelManager;
use Illuminate\Support\ServiceProvider;

class ChannelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChannelManager::class, function ($app) {
            $manager = new ChannelManager;

            // M1: generic webhook adapter powers the engine end-to-end.
            // Provider-specific Meta/WhatsApp adapters register in later milestones.
            $manager->register($app->make(GenericWebhookAdapter::class));

            return $manager;
        });
    }
}
