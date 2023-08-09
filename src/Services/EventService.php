<?php

namespace TelegramGithubNotify\App\Services;

use Symfony\Component\HttpFoundation\Request;
use TelegramGithubNotify\App\Models\Event;
use TelegramGithubNotify\App\Models\Setting;

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

            $callbackData = $this->event::EVENT_PREFIX . $event;

            if (is_array($value)) {
                $eventName = '⚙ ' . $event;
                $callbackData .= '.child';
            } elseif ($value) {
                $eventName = '✅ ' . $event;
            } else {
                $eventName = '❌ ' . $event;
            }

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
     * @return void
     */
    public function eventHandle(): void
    {
        $this->sendMessage(
            view('tools.event'),
            ['reply_markup' => $this->eventMarkup()]
        );
    }
}
