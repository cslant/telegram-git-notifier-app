<?php
/**
 * @var $payload mixed
 */

$message = "🚫 <b>Issue Closed </b> to <a href=\"{$payload->issue->html_url}\">{$payload->repository->full_name}#{$payload->issue->number}</a>\n\n";

$message .= "🔔 <a href=\"{$payload->issue->html_url}\">{$payload->issue->title}</a> by <a href=\"{$payload->issue->user->html_url}\">@{$payload->issue->user->login}</a>\n\n";

if (isset($payload->issue->assignee)) {
    $message .= "🙋 Assignee: <a href=\"{$payload->issue->assignee->html_url}\">@{$payload->issue->assignee->login}</a>\n";
}

$body = $payload->issue->body;
if (strlen($body) > 50) {
    $body = substr($body, 0, 50) . '...';
}
$message .= "📖 {$body}";

echo $message;
