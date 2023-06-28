<?php
/**
 * @var $payload mixed
 */

$message = "🔧 <b>Workflow Requested</b> from <a href=\"{$payload->repository->html_url}\">{$payload->repository->full_name} </a>\n\n";

$message .= "Running workflow: 💥 <b>{$payload->workflow_run->name}</b> ⏳\n\n";

$message .= "📤 Commit: <b>{$payload->workflow_run->head_commit->message}</b>\n";

$message .= "🔗 Link: <a href=\"{$payload->workflow_run->html_url}\">{$payload->workflow_run->runner_id}</a>\n\n";

echo $message;
