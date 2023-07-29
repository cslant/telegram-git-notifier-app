<?php

namespace TelegramGithubNotify\App\Http\Actions;

class WebhookAction
{
    protected string $token;

    public function __construct()
    {
        $this->token = config('telegram-bot.token');
    }

    /**
     * Set webhook for telegram bot
     *
     * @return false|string
     */
    public function set(): false|string
    {
        $appUrl = config('app.url');
        $url = "https://api.telegram.org/bot{$this->token}/setWebhook?url={$appUrl}";

        return file_get_contents($url);
    }

    /**
     * Delete webhook for telegram bot
     *
     * @return false|string
     */
    public function delete(): false|string
    {
        $url = "https://api.telegram.org/bot{$this->token}/deleteWebhook";

        return file_get_contents($url);
    }
}
