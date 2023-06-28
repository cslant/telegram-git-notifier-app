<?php
/**
 * @var $payload mixed
 */

$message = "🎉 <b>Action Completed</b> from <a href=\"{$payload->repository->html_url}\">{$payload->repository->full_name} </a>\n\n";

$message .= "Done action: 🎉 <b>{$payload->workflow_job->runner_name}</b> ✨ \n\n";

$message .= "🔗 Link: <a href=\"{$payload->workflow_job->html_url}\">{$payload->workflow_job->html_url}</a>\n\n";

echo $message;
