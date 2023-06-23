<?php

namespace TelegramGithubNotify\App\Services;

class NotificationService
{
    public function accessDenied(TelegramService $telegramService): void
    {
        $reply = "🔒 <b>Access Denied to Bot </b>🚫\n\nPlease contact administrator for further information, Thank You..";
        $content = array('chat_id' => $telegramService->chatId, 'text' => $reply, 'disable_web_page_preview' => true, 'parse_mode' => "HTML");
        $telegramService->telegram->sendMessage($content);
    }
}
