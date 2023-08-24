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
        $action = $this->getActionOfEvent($platform, $payload);
        if (!empty($action) && isset($eventConfig[$action])) {
            $eventConfig = $eventConfig[$action];
        }

        if (!$eventConfig) {
            error_log('\n Event config is not found \n');
        }

        return (bool)$eventConfig;
    }

    /**
     * Get action name of event from payload data
     *
     * @param string $platform
     * @param $payload
     *
     * @return string
     */
    private function getActionOfEvent(string $platform, $payload): string
    {
        if ($platform === 'github') {
            return $payload->action ?? '';
        } elseif ($platform === 'gitlab') {
            return $payload->object_attributes->action ?? '';
        }

        return '';
    }

    /**
     * Create markup for select event
     *
     * @param string|null $parentEvent
     * @param string|null $platform
     * @return array
     */
    public function eventMarkup(?string $parentEvent = null, ?string $platform = 'github'): array
    {
        $replyMarkup = $replyMarkupItem = [];

        $this->event->setEventConfig($platform);
        $events = $parentEvent === null ? $this->event->eventConfig : $this->event->eventConfig[$parentEvent];

        foreach ($events as $key => $value) {
            if (count($replyMarkupItem) === self::LINE_ITEM_COUNT) {
                $replyMarkup[] = $replyMarkupItem;
                $replyMarkupItem = [];
            }

            $callbackData = $this->getCallbackData($key, $platform, $value, $parentEvent);
            $eventName = $this->getEventName($key, $value);

            $replyMarkupItem[] = $this->telegram->buildInlineKeyBoardButton($eventName, '', $callbackData);
        }

        // add last item to a reply_markup array
        if (count($replyMarkupItem) > 0) {
            $replyMarkup[] = $replyMarkupItem;
        }

        $replyMarkup[] = $this->getEndKeyboard($platform, $parentEvent);

        return $replyMarkup;
    }

    /**
     * Get callback data for markup
     *
     * @param string  $event
     * @param string $platform
     * @param array|bool $value
     * @param string|null $parentEvent
     *
     * @return string
     */
    private function getCallbackData(string $event, string $platform, array|bool $value = false, ?string $parentEvent = null): string
    {
        $platform = $platform === 'github' ? $this->event::GITHUB_EVENT_SEPARATOR : $this->event::GITLAB_EVENT_SEPARATOR;

        $prefix = $this->event::EVENT_PREFIX . $platform;
        if (is_array($value)) {
            return $prefix . self::EVENT_HAS_ACTION_SEPARATOR . $event;
        } elseif ($parentEvent) {
            return $prefix . $parentEvent . '.' . $event . self::EVENT_UPDATE_SEPARATOR;
        }

        return $prefix . $event . self::EVENT_UPDATE_SEPARATOR;
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
     * Get end keyboard buttons
     *
     * @param string $platform
     * @param string|null $parentEvent
     * @return array
     */
    private function getEndKeyboard(string $platform, ?string $parentEvent = null): array
    {
        $back = $this->setting::SETTING_BACK . 'settings';

        if ($parentEvent) {
            $back = $this->setting::SETTING_BACK . 'settings.custom_events.' . $platform;
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
     * @param string|null $platform
     *
     * @return void
     */
    public function eventHandle(?string $callback = null, ?string $platform = null): void
    {
        if (str_contains($callback, $this->event::GITHUB_EVENT_SEPARATOR)) {
            $platform = 'github';
        } elseif (str_contains($callback, $this->event::GITLAB_EVENT_SEPARATOR)) {
            $platform = 'gitlab';
        }

        if ($this->settingEventMessageHandle($platform, $callback)) {
            return;
        }

        $event = str_replace([$this->event::EVENT_PREFIX, $this->event::GITHUB_EVENT_SEPARATOR, $this->event::GITLAB_EVENT_SEPARATOR], '', $callback);

        // if event has actions
        if (str_contains($callback, self::EVENT_HAS_ACTION_SEPARATOR)) {
            $event = str_replace(self::EVENT_HAS_ACTION_SEPARATOR, '', $event);
            $this->editMessageText(
                view('tools.custom_event_actions', compact('event')),
                ['reply_markup' => $this->eventMarkup($event, $platform)]
            );
        }

        if (str_contains($event, self::EVENT_UPDATE_SEPARATOR)) {
            $event = str_replace(self::EVENT_UPDATE_SEPARATOR, '', $event);
            $this->eventUpdateHandle($event, $platform);
        }
    }

    /**
     * First event settings
     *
     * @param string $platform
     * @param string|null $callback
     * @return bool
     */
    private function settingEventMessageHandle(string $platform, ?string $callback = null): bool
    {
        if ($this->setting::SETTING_GITHUB_EVENTS === $callback
            || $this->setting::SETTING_GITLAB_EVENTS === $callback
            || !$callback
        ) {
            $this->editMessageText(
                view('tools.custom_events', ['platform' => $platform]),
                ['reply_markup' => $this->eventMarkup(null, $platform)]
            );
            return true;
        }

        return false;
    }

    /**
     * Handle event update
     *
     * @param string $event
     * @param string $platform
     *
     * @return void
     */
    private function eventUpdateHandle(string $event, string $platform): void
    {
        $event = explode('.', $event);
        $action = $event[1] ?? null;
        $event = $event[0];

        $this->event->setEventConfig($platform);
        $this->event->updateEvent($event, $action);
        $this->eventHandle(
            $action
                ? $this->event::PLATFORM_EVENT_SEPARATOR[$platform] . self::EVENT_HAS_ACTION_SEPARATOR . $event
                : null,
            $platform
        );
    }
}
