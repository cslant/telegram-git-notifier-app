<?php

namespace TelegramGithubNotify\App\Services;

use Symfony\Component\HttpFoundation\Request;
use TelegramGithubNotify\App\Models\Event;
use TelegramGithubNotify\App\Models\Setting;

class EventService extends AppService
{
    public const LINE_ITEM_COUNT = 2;

    public const EVENT_HAS_ACTION_SEPARATOR = 'action.';

    protected Setting $setting;

    protected Event $event;

    public function __construct()
    {
        parent::__construct();

        $this->setting = new Setting();
        $this->event = new Event();
    }

    /**
     * Validate access event before send notify
     *
     * @param Request $request
     * @param $payload
     * @return bool
     */
    public function validateAccessEvent(Request $request, $payload): bool
    {
        if (!$this->setting->isNotified()) {
            return false;
        }

        if ($this->setting->allEventsNotifyStatus()) {
            return true;
        }

        $eventConfig = $this->event->getEventConfig();

        $event = singularity($request->server->get('HTTP_X_GITHUB_EVENT'));
        $eventConfig = $eventConfig[$event] ?? false;

        if (isset($payload->action) && isset($eventConfig[$payload->action])) {
            $eventConfig = $eventConfig[$payload->action];
        }

        if (!$eventConfig) {
            error_log('\n Event config is not found \n');
        }

        return (bool)$eventConfig;
    }

    /**
     * Create markup for select event
     *
     * @param string|null $event
     * @return array
     */
    public function eventMarkup(?string $event = null): array
    {
        $replyMarkup = $replyMarkupItem = [];

        $events = $event === null ? $this->event->eventConfig : $this->event->eventConfig[$event];

        foreach ($events as $event => $value) {
            if (count($replyMarkupItem) === self::LINE_ITEM_COUNT) {
                $replyMarkup[] = $replyMarkupItem;
                $replyMarkupItem = [];
            }

            $callbackData = $this->getCallbackData($event, $value);
            $eventName = $this->getEventName($event, $value);

            $replyMarkupItem[] = $this->telegram->buildInlineKeyBoardButton($eventName, '', $callbackData);
        }

        // add last item to a reply_markup array
        if (count($replyMarkupItem) > 0) {
            $replyMarkup[] = $replyMarkupItem;
        }

        $replyMarkup[] = [$this->telegram->buildInlineKeyBoardButton('🔙 Back', '', 'back')];
        $replyMarkup[] = [$this->telegram->buildInlineKeyBoardButton('📚 Menu', '', $this->setting::SETTING_PREFIX . '.back.menu')];

        return $replyMarkup;
    }

    /**
     * Get event name for markup
     *
     * @param string $event
     * @param $value
     * @return string
     */
    private function getEventName(string $event, $value): string
    {
        if (is_array($value)) {
            return '⚙ ' . $event;
        } elseif ($value) {
            return '✅ ' . $event;
        } else {
            return '❌ ' . $event;
        }
    }

    /**
     * Get callback data for markup
     *
     * @param string $event
     * @param $value
     * @return string
     */
    private function getCallbackData(string $event, $value): string
    {
        if (is_array($value)) {
            return $this->event::EVENT_PREFIX . self::EVENT_HAS_ACTION_SEPARATOR . $event;
        } else {
            return $this->event::EVENT_PREFIX . $event;
        }
    }

    /**
     * Handle event callback settings
     *
     * @param string|null $callback
     * @return void
     */
    public function eventHandle(?string $callback = null): void
    {
        if ($this->setting::SETTING_CUSTOM_EVENTS === $callback || empty($callback)) {
            $this->editMessageText(
                view('tools.custom_event'),
                ['reply_markup' => $this->eventMarkup()]
            );
            return;
        }

        if (str_contains($callback, self::EVENT_HAS_ACTION_SEPARATOR)) {
            $event = str_replace($this->event::EVENT_PREFIX . self::EVENT_HAS_ACTION_SEPARATOR, '', $callback);
            $this->editMessageText(
                view('tools.custom_event_action', compact('event')),
                ['reply_markup' => $this->eventMarkup($event)]
            );
        }
    }
}
