<?php
/**
 * @var $payload mixed
 */

$message = "🚫 <b>Issue Closed </b> to <a href=\"{$payload->issue->html_url}\">{$payload->repository->full_name}#{$payload->issue->number}</a>\n\n";

$message .= "🔔 <b>{$payload->issue->title}</b> by <a href=\"{$payload->issue->user->html_url}\">@{$payload->issue->user->login}</a>\n";

if (isset($payload->issue->assignee)) {
    $message .= "🙋 Assignee: <a href=\"{$payload->issue->assignee->html_url}\">@{$payload->issue->assignee->login}</a>\n\n";
}

$body = $payload->issue->body;
if (strlen($body) > 50) {
    $body = substr($body, 0, 50) . '...';
}
$message .= "📖 {$body}";

echo $message;
