<?php
/**
 * @var $payload mixed
 */

if (isset($event) && isset($payload) && !empty($payload->object_attributes->description)) {
    $body = $payload->object_attributes->description;
    if (strlen($body) > 50) {
        $body = substr($body, 0, 50) . '...';
    }
    return "📖 <b>Content:</b>\n{$body}";
}

return '';
