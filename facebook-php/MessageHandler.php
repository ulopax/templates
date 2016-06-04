<?php

require __DIR__ . '/vendor/autoload.php';

class MessageHandler {

    public static function processMessage($cmd, $user) {
        send_text($user, $cmd);
    }
}

?>
