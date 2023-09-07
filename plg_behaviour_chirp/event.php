<?php

/**
 * @copyright 2023 Name
 * @license MIT	<
 */

// Disable default disconnect checks
ignore_user_abort(true);

// Set headers for stream
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Access-Control-Allow-Origin: *");

if (connection_aborted())
{
	return;
}
else
{
	$contents = file_get_contents('event.data');

	if ($contents)
	{
		// $json = json_decode($contents);
		echo "event: alert\n";
		echo "data: $contents\n\n";
		ob_flush();
		flush();

		sleep(4);
		file_put_contents('event.data', '');
	}
	else
	{
		// No new data to send
		echo ": heartbeat\n\n";
		ob_flush();
		flush();
	}
}
