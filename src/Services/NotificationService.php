<?php

namespace TelegramNotificationBot\App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Request;

class NotificationService
{
    protected mixed $payload;

    protected string $message = '';

    public string $platform = 'github';

    public const WEBHOOK_EVENT_HEADER = [
        'github' => 'HTTP_X_GITHUB_EVENT',
        'gitlab' => 'HTTP_X_GITLAB_EVENT',
    ];

    /**
     * Notify access denied to other chat ids
     *
     * @param TelegramService $telegramService
     * @param string|null $chatId
     * @return void
     */
    public function accessDenied(TelegramService $telegramService, string $chatId = null): void
    {
        $telegramService->telegram->sendMessage([
            'chat_id' => config('telegram-bot.chat_id'),
            'text' => view('globals.access_denied', ['chatId' => $chatId]),
            'disable_web_page_preview' => true,
            'parse_mode' => 'HTML'
        ]);
    }

    /**
     * Set payload from request
     *
     * @param Request $request
     * @param string $event
     *
     * @return mixed|void
     */
    public function setPayload(Request $request, string $event)
    {
        if ($this->platform === 'gitlab') {
            $this->payload = $request->getContent();
        } elseif ($this->platform === 'github') {
            $this->payload = json_decode($request->request->get('payload'));
        }
        $this->setMessage($event);

        return $this->payload;
    }

    /**
     * Set message from payload
     *
     * @param string $typeEvent
     * @return void
     */
    private function setMessage(string $typeEvent): void // need to add condition to gitlab
    {
        if ($this->platform === 'gitlab') {
            $action = $this->payload->object_attributes->action ?? '';
        } elseif ($this->platform === 'github') {
            $action = $this->payload->action ?? '';
        }
        if (!empty($action)) {
            $this->message = view(
                "events.{$this->platform}." . get_event_name($typeEvent) . ".{$action}",
                [
                    'payload' => $this->payload,
                    'event' => convert_event_name($typeEvent),
                ]
            );
        } else {
            $this->message = view("events.{$this->platform}.{$typeEvent}.default", ['payload' => $this->payload]);
        }
    }

    /**
     * Send notification to telegram
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
        } catch (GuzzleException $e) {
            error_log($e->getMessage());
        }

        return false;
    }
}
