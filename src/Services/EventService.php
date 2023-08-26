<?php

namespace TelegramNotificationBot\App\Services;

use TelegramNotificationBot\App\Models\Event;
use TelegramNotificationBot\App\Models\Setting;

class EventService extends AppService
{
    public const LINE_ITEM_COUNT = 2;

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
        $eventConfig = $this->event->eventConfig;

        $eventConfig = $eventConfig[convert_event_name($event)] ?? false;
        $action = $this->getActionOfEvent($payload);

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
     * @param $payload
     * @return string
     */
    public function getActionOfEvent($payload): string
    {
        $action = $payload?->action
            ?? $payload?->object_attributes?->action
            ?? $payload?->object_attributes?->noteable_type
            ?? '';

        if (!empty($action)) {
            return ($action);
        }

        return '';
    }

    /**
     * Create markup for select event
     *
     * @param string|null $parentEvent
     * @param string $platform
     * @return array
     */
    public function eventMarkup(?string $parentEvent = null, string $platform = Event::DEFAULT_PLATFORM): array
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
     * @param string $event
     * @param string $platform
     * @param array|bool $value
     * @param string|null $parentEvent
     *
     * @return string
     */
    private function getCallbackData(string $event, string $platform, array|bool $value = false, ?string $parentEvent = null): string
    {
        $platformSeparator = $platform === $this->event::DEFAULT_PLATFORM
            ? $this->event::GITHUB_EVENT_SEPARATOR
            : $this->event::GITLAB_EVENT_SEPARATOR;
        $prefix = $this->event::EVENT_PREFIX . $platformSeparator;

        if (is_array($value)) {
            return $prefix . $this->event::EVENT_HAS_ACTION_SEPARATOR . $event;
        } elseif ($parentEvent) {
            return $prefix . $parentEvent . '.' . $event . $this->event::EVENT_UPDATE_SEPARATOR;
        }

        return $prefix . $event . $this->event::EVENT_UPDATE_SEPARATOR;
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
        $platform = $this->getPlatformFromCallback($callback, $platform);

        if ($this->settingEventMessageHandle($platform, $callback)) {
            return;
        }

        $event = $this->getEventFromCallback($callback);

        if ($this->handleEventWithActions($event, $platform)) {
            return;
        }

        $this->handleEventUpdate($event, $platform);
    }

    /**
     * Get the platform from callback
     *
     * @param string|null $callback
     * @param string|null $platform
     * @return string
     */
    private function getPlatformFromCallback(?string $callback, ?string $platform): string
    {
        if ($platform) {
            return $platform;
        }

        if (str_contains($callback, $this->event::GITHUB_EVENT_SEPARATOR)) {
            return 'github';
        } elseif (str_contains($callback, $this->event::GITLAB_EVENT_SEPARATOR)) {
            return 'gitlab';
        }

        return $this->event::DEFAULT_PLATFORM;
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
     * Get event name from callback
     *
     * @param string|null $callback
     * @return string
     */
    private function getEventFromCallback(?string $callback): string
    {
        return str_replace([
            $this->event::EVENT_PREFIX, $this->event::GITHUB_EVENT_SEPARATOR,
            $this->event::GITLAB_EVENT_SEPARATOR
        ], '', $callback);
    }

    /**
     * Handle event with actions
     *
     * @param string $event
     * @param string $platform
     * @return bool
     */
    private function handleEventWithActions(string $event, string $platform): bool
    {
        if (str_contains($event, $this->event::EVENT_HAS_ACTION_SEPARATOR)) {
            $event = str_replace($this->event::EVENT_HAS_ACTION_SEPARATOR, '', $event);
            $this->editMessageText(
                view('tools.custom_event_actions', compact('event', 'platform')),
                ['reply_markup' => $this->eventMarkup($event, $platform)]
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
     * @return void
     */
    private function handleEventUpdate(string $event, string $platform): void
    {
        if (str_contains($event, $this->event::EVENT_UPDATE_SEPARATOR)) {
            $event = str_replace($this->event::EVENT_UPDATE_SEPARATOR, '', $event);
            $this->eventUpdateHandle($event, $platform);
        }
    }

    /**
     * Handle event update
     *
     * @param string $event
     * @param string $platform
     * @return void
     */
    private function eventUpdateHandle(string $event, string $platform): void
    {
        [$event, $action] = explode('.', $event);

        $this->event->setEventConfig($platform);
        $this->event->updateEvent($event, $action);
        $this->eventHandle(
            $action
                ? $this->event::PLATFORM_EVENT_SEPARATOR[$platform]
                . $this->event::EVENT_HAS_ACTION_SEPARATOR . $event
                : null,
            $platform
        );
    }
}
