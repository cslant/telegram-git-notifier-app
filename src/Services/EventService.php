<?php

namespace TelegramNotificationBot\App\Services;

use TelegramNotificationBot\App\Models\Event;
use TelegramNotificationBot\App\Models\Setting;

class EventService extends AppService
{
    public const LINE_ITEM_COUNT = 2;

    public const EVENT_HAS_ACTION_SEPARATOR = 'atc.';

    public const EVENT_UPDATE_SEPARATOR = '.upd';

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
     * @param string $platform Source code platform (GitHub, GitLab)
     * @param string $event Event name (push, pull_request)
     * @param $payload
     *
     * @return bool
     */
    public function validateAccessEvent(string $platform, string $event, $payload): bool
    {
        if (!$this->setting->isNotified()) {
            return false;
        }

        if ($this->setting->allEventsNotifyStatus()) {
            return true;
        }

        $this->event->setEventConfig($platform);
        $eventConfig = $this->event->getEventConfig();
        $eventConfig = $eventConfig[convert_event_name($event)] ?? false;

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

        foreach ($events as $key => $value) {
            if (count($replyMarkupItem) === self::LINE_ITEM_COUNT) {
                $replyMarkup[] = $replyMarkupItem;
                $replyMarkupItem = [];
            }

            $callbackData = $this->getCallbackData($key, $value, $event);
            $eventName = $this->getEventName($key, $value);

            $replyMarkupItem[] = $this->telegram->buildInlineKeyBoardButton($eventName, '', $callbackData);
        }

        // add last item to a reply_markup array
        if (count($replyMarkupItem) > 0) {
            $replyMarkup[] = $replyMarkupItem;
        }

        $replyMarkup[] = $this->getEndKeyboard($event);

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
        }

        return '❌ ' . $event;
    }

    /**
     * Get callback data for markup
     *
     * @param string  $event
     * @param array|bool $value
     * @param string|null $parentEvent
     *
     * @return string
     */
    private function getCallbackData(string $event, array|bool $value = false, ?string $parentEvent = null): string
    {
        if (is_array($value)) {
            return $this->event::EVENT_PREFIX . self::EVENT_HAS_ACTION_SEPARATOR . $event;
        } elseif ($parentEvent) {
            return $this->event::EVENT_PREFIX . $parentEvent . '.' . $event . self::EVENT_UPDATE_SEPARATOR;
        }

        return $this->event::EVENT_PREFIX . $event . self::EVENT_UPDATE_SEPARATOR;
    }

    /**
     * Get end keyboard buttons
     *
     * @param string|null $event
     * @return array
     */
    public function getEndKeyboard(?string $event = null): array
    {
        $back = $this->setting::SETTING_BACK . 'settings';

        if ($event) {
            $back = $this->setting::SETTING_BACK . 'settings.custom_events';
        }

        return [
            $this->telegram->buildInlineKeyBoardButton('🔙 Back', '', $back),
            $this->telegram->buildInlineKeyBoardButton('📚 Menu', '', $this->setting::SETTING_BACK . 'menu')
        ];
    }

    /**
     * Handle event callback settings
     *
     * @param string|null $callback
     * @return void
     */
    public function eventHandle(?string $callback = null): void
    {
        // first event settings
        if ($this->setting::SETTING_CUSTOM_EVENTS === $callback || empty($callback)) {
            $this->editMessageText(
                view('tools.custom_events'),
                ['reply_markup' => $this->eventMarkup()]
            );
            return;
        }

        $event = str_replace($this->event::EVENT_PREFIX, '', $callback);

        // if event has actions
        if (str_contains($callback, self::EVENT_HAS_ACTION_SEPARATOR)) {
            $event = str_replace(self::EVENT_HAS_ACTION_SEPARATOR, '', $event);
            $this->editMessageText(
                view('tools.custom_event_actions', compact('event')),
                ['reply_markup' => $this->eventMarkup($event)]
            );
        }

        if (str_contains($event, self::EVENT_UPDATE_SEPARATOR)) {
            $event = str_replace(self::EVENT_UPDATE_SEPARATOR, '', $event);
            $this->eventUpdateHandle($event);
        }
    }

    /**
     * Handle event update
     *
     * @param string $event
     * @return void
     */
    public function eventUpdateHandle(string $event): void
    {
        $event = explode('.', $event);
        $action = $event[1] ?? null;
        $event = $event[0];

        $this->event->updateEvent($event, $action);
        $this->eventHandle($action ? self::EVENT_HAS_ACTION_SEPARATOR . $event : null);
    }
}
