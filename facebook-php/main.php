<?php

require __DIR__.'/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;



set_error_handler('error_handler');
register_shutdown_function('error_handler');

function error_handler()
{
    $log = new Logger('logger');
    $log->pushHandler(new StreamHandler(__DIR__.'/logs/error.log', Logger::ERROR));
    $log->pushHandler(new StreamHandler(__DIR__.'/logs/error.log', Logger::WARNING));

    $lasterror = error_get_last();
    switch ($lasterror['type']) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_PARSE:
            $error = '[SHUTDOWN] lvl:'.$lasterror['type'].' | msg:'.$lasterror['message'].' | file:'.$lasterror['file'].' | ln:'.$lasterror['line'];
            $log->error($error);
    }
}

if (array_key_exists('hub_verify_token', get_input())) {
    verify(get_input());
}

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// get the incoming message and log it to file
$input = get_input();
logger($input);

$msgs = get_messages($input);

foreach ($msgs as $m) {
    if (wanted($m)) {
        if(array_key_exists('text', $m['message'])) {
            MessageHandler::processMessage(get_cmd($m), get_user($m));
        }
        if(array_key_exists('attachments', $m['message'])) {
            foreach($m['message']['attachments'] as $a) {
                switch($a['type']) {
                    case 'image':
                        $url = preg_replace('/\?.*/', '', $a['payload']['url']);
                        MessageHandler::processImage($url, get_user($m));
                        break;
                    case 'location':
                        MessageHandler::processLocation($a['payload']['coordinates']['lat'],
                            $a['payload']['coordinates']['long'], get_user($m));
                        break;
                }

            }
            //
        }
    }
}

// respond 200 to tell the requester that everything is fine,
// do NOT delete this lines
http_response_code(200);
exit(0);
