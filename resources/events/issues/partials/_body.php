<?php
/**
 * @var $payload mixed
 */

if (!empty($payload->issue->body)) {
    $body = $payload->issue->body;
    if (strlen($body) > 50) {
        $body = substr($body, 0, 50) . '...';
    }
    return "📖 {$body}";
}

return "";
