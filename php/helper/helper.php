<?php

use Database\UserDAO;

function logger($data)
{
    $data = var_export($data, true);
    $logpath = 'logs/'.date('Y-m-d').'-application.log';
    if(!file_exists($logpath)) {
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

function get_user($input)
{
    $m = $input['entry'][0]['messaging'][0];
    $chat = $m['sender']['id'];
    $dao = new UserDAO();
    $user = $dao->retrieve($chat, 'facebook');
    if(!$user) {
        return $dao->insert(null, $chat, 'facebook', json_encode($m));
    } else {
        $user->meta = json_encode($m);
        $dao->update($user);
        return $user;
    }
}

function update_user($user)
{
    $dao = new UserDAO();
    $dao->update($user);
    return $user;
}

function verify($request)
{
    if ($request['hub_verify_token'] == 'oratio-subscription') {
        echo $request['hub_challenge'];
        http_response_code(202);
    } else {
        http_response_code(400);
    }
    exit(0);
}

if(array_key_exists('hub_verify_token', get_input())) {
    verify(get_input());
}
