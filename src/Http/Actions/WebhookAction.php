<?php

namespace LbilTech\TelegramGitNotifierApp\Http\Actions;

use LbilTech\TelegramGitNotifier\Services\WebhookService;

class WebhookAction
{
    protected string $token;

    protected WebhookService $webhookService;

    public function __construct()
    {
        $this->webhookService = new WebhookService();
        $this->webhookService->setToken(config('telegram-bot.token'));
        $this->webhookService->setUrl(config('app.url'));
    }

    /**
     * Set webhook for telegram bot
     *
     * @return false|string
     */
    public function set(): false|string
    {
        return $this->webhookService->setWebhook();
    }

    /**
     * Delete webhook for telegram bot
     *
     * @return false|string
     */
    public function delete(): false|string
    {
        return $this->webhookService->deleteWebHook();
    }
}
