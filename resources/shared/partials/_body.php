<?php
/**
 * @var $payload mixed
 * @var $event string
 */

if (isset($event) && isset($payload) && !empty($payload->{$event}->body)) {
    $body = $payload->{$event}->body;
    if (strlen($body) > 50) {
        $body = substr($body, 0, 50) . '...';
    }
    return "📖 <b>Content:</b>\n{$body}";
}

return '';
