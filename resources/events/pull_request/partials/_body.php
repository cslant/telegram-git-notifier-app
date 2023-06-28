<?php
/**
 * @var $payload mixed
 */

if (!empty($payload->pull_request->body)) {
    $body = $payload->pull_request->body;
    if (strlen($body) > 50) {
        $body = substr($body, 0, 50) . '...';
    }
    return "📖 <b>Body:</b>\n{$body}";
}

return "";
