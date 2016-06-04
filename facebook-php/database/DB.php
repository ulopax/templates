<?php

namespace Database;
use PDO;
use PDOException;

class DB
{
    public function __construct()
    {
        $servername = '127.0.0.1';
        $username = 'homestead';
        $password = 'secret';
        $dbname = 'botshack';
        $port = '33060';

        try {
            $this->conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Helper::logger('Connected successfully');
        } catch (PDOException $e) {
            logger('Connection failed: '.$e->getMessage());
        }
    }

    public function create($table, $values)
    {
        $fields = implode(',', array_keys($values));
        $params = implode(',', array_map(function($value) {
            return ':'.$value;
        }, array_keys($values)));
        $sql = "INSERT INTO $table ($fields) VALUES ($params)";
        $stmt = $this->conn->prepare($sql);
        foreach($values as $k=>$v) {
            $stmt->bindParam(":$k", $values[$k]);
        }
        $stmt->execute();

        return $this->retrieve($table, ['id' => $this->conn->lastInsertId()]);
    }

    public function retrieve($table, $values)
    {
        $fields = array_merge([], $values);
        array_walk($fields, function(&$k, $v) {
            $k = $v.'=:'.$v;
        });
        $fields = implode(' AND ', $fields);
        $stmt = $this->conn->prepare("SELECT * FROM $table WHERE $fields");
        foreach($values as $k=>$v) {
            $stmt->bindParam(":$k", $values[$k]);
        }
        $stmt->execute();

        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->single($stmt->fetchAll());
    }

    public function update($table, $values, $id)
    {
        unset($values['id']);
        $fields = array_merge([], $values);
        array_walk($fields, function(&$k, $v) {
            $k = $v.'=:'.$v;
        });
        $fields = implode(',', $fields);
        $sql = "UPDATE $table SET $fields, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        foreach($values as $k=>$v) {
            $stmt->bindParam(":$k", $values[$k]);
        }
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    private function single($set) {
        if(count($set) == 0 || count($set) > 1)
            return null;
        $item = $set[0];
        return $item;
    }
}
