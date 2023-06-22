<?php

namespace TelegramGithubNotify\App\Services;

use Telegram;

class TelegramNotifyService
{
    public string $token;

    public string $chatId;

    public Telegram $telegram;

    public array $messageData;

    /**
     * @return void
     */
    public function setToken(): void
    {
        $this->token = config('telegram-bot.token');
    }

    /**
     * @return void
     */
    public function setChatId(): void
    {
        $this->chatId = config('telegram-bot.chat_id');
    }

    /**
     * @return Telegram
     */
    public function storeByToken(): Telegram
    {
        $this->telegram = new Telegram($this->token);
        return $this->telegram;
    }

    /**
     * @return void
     */
    public function getDataOfMessage(): void
    {
        $this->messageData = $this->telegram->getData();
    }
}
