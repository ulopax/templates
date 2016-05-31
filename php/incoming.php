<?php

require __DIR__ . '/vendor/autoload.php';

// get the incoming message and log it to file
$input = get_input();
logger($input);

$user = get_user($input);
logger($user);

$message = get_message($input);


// respond 200 to tell the requester that everything is fine,
// do NOT delete this lines
http_response_code(200);
exit(0);

?>
