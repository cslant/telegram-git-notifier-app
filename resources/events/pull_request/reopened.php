<?php
/**
 * @var $payload mixed
 */

$message = "👷‍♂️🛠️ <b>Reopened Pull Request</b> - <a href=\"{$payload->pull_request->html_url}\">{$payload->repository->full_name}#{$payload->pull_request->number}</a> by <a href=\"{$payload->pull_request->user->html_url}\">@{$payload->pull_request->user->login}</a>\n\n";

$message .= "🛠 <b>{$payload->pull_request->title}</b> \n\n";

$message .= require __DIR__ . '/../../shared/partials/_assignee.php';

$message .= require __DIR__ . '/partials/_reviewers.php';

$message .= require __DIR__ . '/../../shared/partials/_body.php';

echo $message;
