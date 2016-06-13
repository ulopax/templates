<?php

use Database\UserDAO;
use Facebook\FacebookAPI;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function logger($data)
{
    $data = var_export($data, true);
    $logpath = 'logs/'.date('Y-m-d').'-application.log';
    if (!file_exists($logpath)) {
        $logfile = fopen($logpath, 'w');
        fclose($logfile);
    }

    file_put_contents($logpath, $data.PHP_EOL, FILE_APPEND);
}

function get_input()
{
    if ($_GET && count($_GET)) {
        return $_GET;
    } elseif ($_POST && count($_POST)) {
        return $_POST;
    } else {
        return json_decode(file_get_contents('php://input'), true);
    }
}

function get_messages($input)
{
    if (array_key_exists('entry', $input)) {
        $messages = [];
        foreach ($input['entry'] as $entry) {
            foreach ($entry['messaging'] as $m) {
                $messages[] = $m;
            }
        }

        return $messages;
    }

    return [$input];
}

function wanted($msg)
{
    if (array_key_exists('delivery', $msg) || array_key_exists('read', $msg)) {
        return false;
    }

    return true;
}

function get_user($m)
{
    $chat = $m['sender']['id'];
    $dao = new UserDAO();
    $user = $dao->retrieve($chat, 'facebook');
    if (!$user) {
        return $dao->insert(null, $chat, 'facebook', json_encode($m));
    } else {
        $user->meta = json_encode($m);
        $dao->update($user);

        return $user;
    }
}

function get_cmd($payload) {
    if(array_key_exists('message', $payload)
        && array_key_exists('text', $payload['message'])) {
        $cmd = $payload['message']['text'];
    } else if(array_key_exists('postback', $payload)) {
        $cmd = $payload['postback']['payload'];
    } else {
        $cmd = 'unknown';
    }

    return $cmd;
}

function update_user($user)
{
    $dao = new UserDAO();
    $dao->update($user);

    return $user;
}

function verify($request)
{
    if ($request['hub_verify_token'] == 'botshackathon') {
        echo $request['hub_challenge'];
        http_response_code(202);
    } else {
        http_response_code(400);
    }
    exit(0);
}
