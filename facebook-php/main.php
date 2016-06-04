<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// get the incoming message and log it to file
$input = get_input();
logger($input);

$msgs = get_messages($input);

foreach($msgs as $m) {
    if(wanted($m)) {
        MessageHandler::processMessage(get_cmd($m), get_user($m));
    }
}


// respond 200 to tell the requester that everything is fine,
// do NOT delete this lines
http_response_code(200);
exit(0);

?>
