<?php

namespace TelegramGithubNotify\App\Http\Actions;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Request;
use TelegramGithubNotify\App\Services\NotificationService;
use TelegramGithubNotify\App\Services\TelegramService;

class SendNotifyAction
{
    protected TelegramService $telegramService;

    protected NotificationService $notificationService;

    protected Request $request;

    protected array $chatIds = [];

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->telegramService = new TelegramService();
        $this->notificationService = new NotificationService();

        $this->chatIds = config('telegram-bot.gr_chat_ids');
    }

    /**
     * Send notify to telegram
     *
     * @return void
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $this->telegramService->checkCallback();
        $chatMessageId = $this->telegramService->messageData['message']['chat']['id'] ?? '';

        // Send a result to only the bot owner
        if (!empty($chatMessageId) && $chatMessageId == $this->telegramService->chatId) {
            $this->telegramService->telegramToolHandler($this->telegramService->messageData['message']['text']);
            return;
        }

        // Send a result to all chat ids in config
        if (!in_array($chatMessageId, $this->chatIds)) {
            $this->notificationService->setPayload($this->request);
            foreach ($this->chatIds as $chatId) {
                $this->notificationService->sendNotify($chatId);
            }
            return;
        }

        // Notify access denied to other chat ids
        $this->notificationService->accessDenied($this->telegramService);
    }
}
