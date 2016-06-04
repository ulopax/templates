<?php

namespace Database;

use Entities\User;

class UserDAO
{
    public function __construct()
    {

    }

    public function insert($name, $chat, $network, $meta)
    {
        $params = [
            'name' => $name,
            'chat' => $chat,
            'network' => $network,
            'meta' => $meta,
            'status' => 'start'
        ];
        $db = new DB();
        $result = $db->create('user', $params);
        return new User($result['id'], $result['name'], $result['chat'], $result['status'],
            $result['meta'], $result['language'], $result['created_at'], $result['updated_at']);
    }

    public function retrieve($chat, $network)
    {
        $params = [
            'chat' => $chat,
            'network' => $network,
        ];
        $db = new DB();
        $result = $db->retrieve('user', $params);
        if(!$result) {
            return $result;
        }

        return new User($result['id'], $result['name'], $result['chat'], $result['status'],
            $result['meta'], $result['language'], $result['created_at'], $result['updated_at']);
    }

    public function update($user)
    {
        $db = new DB();
        $params = $user->toArray();
        return $db->update('user', $params, $user->id);
    }
}
