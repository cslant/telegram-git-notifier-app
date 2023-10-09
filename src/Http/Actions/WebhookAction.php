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
        $this->webhookService->setToken(config('telegram-git-notifier.bot.token'));
        $this->webhookService->setUrl(config('telegram-git-notifier.app.url'));
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
