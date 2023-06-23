<?php

namespace TelegramGithubNotify\App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Request;

class NotificationService
{
    public $payload;

    public string $message = "";

    /**
     * @param TelegramService $telegramService
     * @return void
     */
    public function accessDenied(TelegramService $telegramService): void
    {
        $reply = "🔒 <b>Access Denied to Bot </b>🚫\n\nPlease contact administrator for further information, Thank You..";
        $content = array(
            'chat_id' => $telegramService->chatId,
            'text' => $reply,
            'disable_web_page_preview' => true,
            'parse_mode' => "HTML"
        );
        $telegramService->telegram->sendMessage($content);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function setPayload(Request $request): void
    {
        $this->payload = json_decode($request->request->get('payload'), true);
        if (is_null($request->server->get('HTTP_X_GITHUB_EVENT'))) {
            echo "invalid request";
            die;
        } else {
            $this->setMessage($request->server->get('HTTP_X_GITHUB_EVENT'));
        }
    }

    /**
     * @param $typeEvent
     * @return void
     */
    private function setMessage($typeEvent): void
    {
        switch ($typeEvent) {
            case 'push':
                $count = count($this->payload->commits);
                $noun = ($count > 1) ? "commits" : "commit";
                $this->message .= "⚙️ <b>{$count}</b> new {$noun} to <b>{$this->payload->repository->name}:{$this->payload->repository->default_branch}</b>\n\n";

                foreach ($this->payload->commits as $commit) {
                    $commitId = substr($commit->id, -7);
                    $this->message .= "<a href=\"{$commit->url}\">{$commitId}</a>: {$commit->message} by <i>{$commit->author->name}</i>\n";
                }

                $this->message .= "\nPushed by : <b>{$this->payload->pusher->name}</b>\n";

                break;
            case 'ping':
                $this->message .= "♻️ <b>Connection Successful</b>\n\n";
                break;
            case 'issues':
                if ($this->payload->action == "opened") {
                    $this->message .= "⚠️ <b>New Issue</b> - <a href=\"{$this->payload->issue->html_url}\">{$this->payload->repository->full_name}#{$this->payload->issue->number}</a>\n\n";
                    $this->message .= "<a href=\"{$this->payload->issue->html_url}\">{$this->payload->issue->title}</a> by <a href=\"{$this->payload->issue->user->html_url}\">@{$this->payload->issue->user->login}</a>\n\n";
                    $this->message .= " {$this->payload->issue->body}";
                } elseif ($this->payload->action == "closed") {
                    $this->message .= "🚫 <b>Issue Closed </b> - <a href=\"{$this->payload->issue->html_url}\">{$this->payload->repository->full_name}#{$this->payload->issue->number}</a>\n\n";
                    $this->message .= "<a href=\"{$this->payload->issue->html_url}\">{$this->payload->issue->title}</a> by <a href=\"{$this->payload->issue->user->html_url}\">@{$this->payload->issue->user->login}</a>\n\n";
                    $this->message .= " {$this->payload->issue->body}";
                }

                break;
            case 'pull_request':
                if ($this->payload->action == "opened") {
                    $this->message .= "👷‍♂️🛠️ <b>New Pull Request</b> - <a href=\"{$this->payload->pull_request->html_url}\">{$this->payload->repository->full_name}#{$this->payload->pull_request->number}</a>\n\n";
                    $this->message .= "<a href=\"{$this->payload->pull_request->url}\">{$this->payload->pull_request->title}</a> by <a href=\"{$this->payload->pull_request->user->html_url}\">@{$this->payload->pull_request->user->login}</a>\n\n";
                    $this->message .= " {$this->payload->pull_request->body}";
                } elseif ($this->payload->action == "closed") {
                    $this->message .= "✅ <b>Pull Request Merged </b> - <a href=\"{$this->payload->pull_request->html_url}\">{$this->payload->repository->full_name}#{$this->payload->pull_request->number}</a>\n\n";
                    $this->message .= "<a href=\"{$this->payload->pull_request->html_url}\">{$this->payload->pull_request->title}</a> by <a href=\"{$this->payload->pull_request->user->html_url}\">@{$this->payload->pull_request->user->login}</a>\n\n";
                    $this->message .= " {$this->payload->pull_request->body}";
                }

                break;
            case 'issue_comment':
                $this->message .= "📬 <b>New comment </b> on <a href=\"{$this->payload->comment->html_url}\">{$this->payload->repository->full_name}#{$this->payload->issue->number}</a>\n\n";
                $this->message .= "<a href=\"{$this->payload->comment->html_url}\">comment</a> by <a href=\"{$this->payload->comment->user->html_url}\">@{$this->payload->comment->user->login}</a>\n\n";
                $this->message .= " {$this->payload->comment->body}";

                break;
        }
    }

    /**
     * @throws GuzzleException
     */
    public function sendNotify($chatId): bool
    {
        $text = urlencoded_message($this->message);
        $method_url = 'https://api.telegram.org/bot' . config('telegram-bot.token') . '/sendMessage';
        $url = $method_url . '?chat_id=' . $chatId . '&disable_web_page_preview=1&parse_mode=html&text=' . $text;

        $client = new Client();
        $response = $client->request('GET', $url);

        if ($response->getStatusCode() == 200) {
            return true;
        }

        return false;
    }
}
