<?php

class Database
{
    protected $connection;

    public function __construct()
    {
        $serverName = 'localhost';
        $dbname = 'message_board';
        $username = 'root';
        $password = 'root';

        $this->connection = new PDO(
            "mysql:host=$serverName;dbname=$dbname",
            $username,
            $password
        );
    }

    public function insertTo($tableName, $attrAndValue)
    {
        $params = $this->assocToSqlParams($attrAndValue);

        return $this->connection->exec(
            "INSERT INTO $tableName ({$params['attrs']}) VALUES ({$params['values']})"
        );
    }

    public function selectAll($tableName)
    {
        return $this->connection->query(
            "SELECT * FROM $tableName"
        );
    }

    public function update($tableName, $id, $attrAndValue)
    {
        $updateStmt = implode(
            ',',
            array_map(function ($value, $attr) {
                return "$attr='$value'";
            }, $attrAndValue, array_keys($attrAndValue))
        );

        return $this->connection->exec(
            "UPDATE $tableName SET $updateStmt WHERE id = $id"
        );
    }

    public function delete($tableName, $id)
    {
        return $this->connection->exec(
            "DELETE FROM $tableName WHERE id = $id"
        );
    }

    protected function assocToSqlParams($attrAndValue)
    {
        $attrs = implode(',', array_keys($attrAndValue));
        $values = implode(',',
            array_map(function ($value) {
                return "'$value'";
            }, $attrAndValue)
        );

        return compact('attrs', 'values');
    }
}
