<?php

namespace TelegramGithubNotify\App\Services;

use Symfony\Component\HttpFoundation\Request;

class EventSettingService
{
    /**
     * Validate access event before send notify
     *
     * @param Request $request
     * @param $payload
     * @return bool
     */
    public function validateAccessEvent(Request $request, $payload): bool
    {
        if (enable_all_events()) {
            return true;
        }

        $eventConfig = event_config();

        if (empty($eventConfig)) {
            error_log('\n Event config is empty \n');
            return false;
        }

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
}
