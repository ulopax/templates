<?php

namespace Entities;

class User
{
    public $id;
    public $name;
    public $chat;
    public $status;
    public $meta;
    public $language;
    public $created_at;
    public $updated_at;

    public function __construct($id, $name, $chat, $status, $meta, $language, $created_at, $updated_at)
    {
        $this->id = $id;
        $this->name = $name;
        $this->chat = $chat;
        $this->status = $status;
        $this->meta = $meta;
        $this->language = $language;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function toArray()
    {
        return (array) $this;
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }
}
