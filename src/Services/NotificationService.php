<?php

namespace TelegramGithubNotify\App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Request;

class NotificationService
{
    public mixed $payload;

    public string $message = "";

    /**
     * Notify access denied to other chat ids
     *
     * @param TelegramService $telegramService
     * @param string|null $chatId
     * @return void
     */
    public function accessDenied(TelegramService $telegramService, string $chatId = null): void
    {
        $reply = view('globals.access_denied', ['chatId' => $chatId]);
        $content = array(
            'chat_id' => config('telegram-bot.chat_id'),
            'text' => $reply,
            'disable_web_page_preview' => true,
            'parse_mode' => 'HTML'
        );
        $telegramService->telegram->sendMessage($content);
    }

    /**
     * Set payload from request
     *
     * @param Request $request
     * @return mixed|void
     */
    public function setPayload(Request $request)
    {
        $this->payload = json_decode($request->request->get('payload'));
        if (is_null($request->server->get('HTTP_X_GITHUB_EVENT'))) {
            echo 'invalid request';
            exit;
        } else {
            $this->setMessage($request->server->get('HTTP_X_GITHUB_EVENT'));
        }

        return $this->payload;
    }

    /**
     * Set message from payload
     *
     * @param string $typeEvent
     * @return void
     */
    public function setMessage(string $typeEvent): void
    {
        if (isset($this->payload->action) && !empty($this->payload->action)) {
            $this->message = view(
                'events.' . $typeEvent . '.' . $this->payload->action,
                [
                    'payload' => $this->payload,
                    'event' => singularity($typeEvent),
                ]
            );
        } else {
            $this->message = view('events.' . $typeEvent . '.default', ['payload' => $this->payload]);
        }
    }

    /**
     * Send notify to telegram
     *
     * @param string $chatId
     * @param string|null $message
     * @return bool
     */
    public function sendNotify(string $chatId, string $message = null): bool
    {
        if (!is_null($message)) {
            $this->message = $message;
        }

        $method_url = 'https://api.telegram.org/bot' . config('telegram-bot.token') . '/sendMessage';
        $url = $method_url . '?chat_id=' . $chatId . '&disable_web_page_preview=1&parse_mode=html&text='
            . urlencoded_message($this->message);

        $client = new Client();

        try {
            $response = $client->request('GET', $url);

            if ($response->getStatusCode() === 200) {
                return true;
            }

            return false;
        } catch (GuzzleException $e) {
            return false;
        }
    }
}
