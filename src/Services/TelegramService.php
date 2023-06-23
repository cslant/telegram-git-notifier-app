<?php

namespace TelegramGithubNotify\App\Services;

use Symfony\Component\HttpFoundation\Request;
use Telegram;

class TelegramService
{
    public string $token;

    public string $chatId;

    public Telegram $telegram;

    public array $messageData;

    protected Request $request;

    public function __construct()
    {
        $this->setToken();
        $this->setChatId();
        $this->storeByToken();
        $this->getDataOfMessage();

        $this->request = Request::createFromGlobals();
    }

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
