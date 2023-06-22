<?php

namespace TelegramGithubNotify\App\Http\Actions;

class SetWebhookAction
{
    /**
     * @return false|string
     */
    public function __invoke(): false|string
    {
        $token = config('telegram-bot.token');
        $appUrl = config('app_url');

        $url = "https://api.telegram.org/bot{$token}/setWebhook?url={$appUrl}";

        return @file_get_contents($url);
    }
}