<?php
/**
 * @var $payload mixed
 * @var $event string
 */

if (isset($event) && isset($payload) && !empty($payload->{$event}->assignee)) {
    return "🙋 Assignee: <a href=\"{$payload->{$event}->assignee->html_url}\">@{$payload->{$event}->assignee->login}</a>\n";
}

return '';
