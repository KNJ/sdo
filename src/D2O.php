<?php
namespace Wazly;

use PDO;

class D2O extends PDO
{
    protected $stmt; // PDOStatement
    protected $styles = [
        'a' => PDO::FETCH_ASSOC,
        'arr' => PDO::FETCH_ASSOC,
        'ary' => PDO::FETCH_ASSOC,
        'array' => PDO::FETCH_ASSOC,
        'assoc' => PDO::FETCH_ASSOC,
        'association' => PDO::FETCH_ASSOC,
        'n' => PDO::FETCH_NUM,
        'num' => PDO::FETCH_NUM,
        'number' => PDO::FETCH_NUM,
        'o' => PDO::FETCH_OBJ,
        'obj' => PDO::FETCH_OBJ,
        'object' => PDO::FETCH_OBJ,
    ];

    public function state($statement, $driver_options = [])
    {
        $this->stmt = $this->prepare($statement, $driver_options);
        return $this;
    }

    public function bind($input_parameters, $type = 'value')
    {
        foreach ($input_parameters as $key => $value) {
            $value = (array) $value;
            if (isset($value[1])) {
                if (strtoupper($value[1]) === 'BOOL') {
                    $value[1] = PDO::PARAM_BOOL;
                } else if (strtoupper($value[1]) === 'NULL') {
                    $value[1] = PDO::PARAM_NULL;
                } else if (strtoupper($value[1]) === 'INT') {
                    $value[1] = PDO::PARAM_INT;
                } else if (strtoupper($value[1]) === 'LOB') {
                    $value[1] = PDO::PARAM_LOB;
                } else if (strtoupper($value[1]) === 'STMT') {
                    $value[1] = PDO::PARAM_STMT;
                } else if (strtoupper($value[1]) === 'INPUT_OUTPUT') {
                    $value[1] = PDO::PARAM_INPUT_OUTPUT;
                }
            } else {
                $value[1] = PDO::PARAM_STR;
            }
            if ($type === 'param') {
                $this->stmt->bindParam($key, $value[0], $value[1]);
            } else {
                $this->stmt->bindValue($key, $value[0], $value[1]);
            }
        }
        return $this;
    }

    public function run($input_parameters = [], $type = 'value')
    {
        $this->bind($input_parameters, $type);
        $this->stmt->execute();
        return $this;
    }

    public function pick($style = 'object')
    {
        return $this->stmt->fetch($this->styles[$style]);
    }

    public function format($style = 'object')
    {
        return $this->stmt->fetchAll($this->styles[$style]);
    }

    public function execute()
    {
        $this->stmt->execute();
        return $this->stmt;
    }

    public function find()
    {
        $result = $this->execute();
        return $result->fetchObject();
    }
}
