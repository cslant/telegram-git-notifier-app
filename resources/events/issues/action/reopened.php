<?php
/**
 * @var $payload mixed
 */

// reopen issue

$message = "⚠️ <b>Issue has been reopened</b> to <a href=\"{$payload->issue->html_url}\">{$payload->repository->full_name}#{$payload->issue->number}</a>\n\n";

if (isset($payload->issue->assignee)) {
    $message .= "🙋 Assignee: <a href=\"{$payload->issue->assignee->html_url}\">@{$payload->issue->assignee->login}</a>\n";
}

$message .= "🔔 <a href=\"{$payload->issue->html_url}\">{$payload->issue->title}</a> by <a href=\"{$payload->issue->user->html_url}\">@{$payload->issue->user->login}</a>\n\n";

if (isset($payload->changes->title)) {
    $message .= "📖 <b>Title</b> has been changed\n";
    $message .= "📝 <b>From:</b> {$payload->changes->title->from}\n";
    $message .= "📝 <b>To:</b> {$payload->issue->title}\n";
}
if (isset($payload->changes->body)) {
    $message .= "📖 <b>Body</b> has been changed\n";
    $message .= "Please check the issue for more details\n";
}

echo $message;
