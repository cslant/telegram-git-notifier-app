<?php

namespace TelegramGithubNotify\App\Services;

use Symfony\Component\HttpFoundation\Request;
use TelegramGithubNotify\App\Models\Setting;

class EventService
{
    protected Setting $setting;

    public function __construct()
    {
        $this->setting = new Setting();
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

        $eventConfig = event_config();

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

    public function eventHandle()
    {
    }
}
