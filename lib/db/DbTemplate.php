<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/8/20
 * Time: 4:11 AM
 * @property string error
 */

trait DbTemplate
{

    protected array $_errors = [];

    protected array $_bindValues = [];

    protected array $_bindKeys = [];

    protected ?string $_sql = null;

    protected ?string $_preparedSql = null;

    protected ?PDOStatement $_preparedStatement = null;

    protected $conn;

    protected function __construct($settings = [])
    {
        try {
            $this->conn = $this->connect(static::getConfigs());
        } catch (PDOException $e) {
            /*TODO handle this better*/
            switch ($code = $e->getCode()) {
                case  1049:
                    // db does not exist
                    try {
                        $this->createDatabase();
                    } catch (PDOException $e) {
                        $this->error = $e->getMessage();
                    }
                    break;
                case  2002:
                    // db connection refused
                    echo "Failed to get access to  provided host ";
                    break;
                default:
                    echo $e->getMessage();
            }
        }
    }

    abstract function connect($configs): PDO;

    abstract function createDatabase();

    protected function addError($error)
    {
        array_push($this->_errors, $error);
    }

    function __set($name, $value)
    {
        if ($name == "error") {
            $this->addError($value);
        }
    }

    public function __get($name)
    {
        switch ($name) {
            // returns the last available error
            case "error":
                $error = $this->_errors == null ? "" : $this->_errors[count($this->_errors) - 1];
                // unset for other errors.
                $c = count($this->_errors);
                if (isset($this->_errors[$c - 1])) {
                    return $error . "<br> SQL = " . $this->sql;
                } else return "";
            case "errors":
                return $this->_errors;

            case "preparedSql":
                return $this->_preparedSql;
                break;
            case "sql":
                $sql = $this->_preparedSql;

                if (count($this->_bindValues) > 0) {
                    for ($i = 0; $i < count($this->_bindValues); $i++) {
                        $sql = str_replace(":key_$i", $this->_bindValues[$i], $sql);
                    }
                } else $sql = $this->_preparedSql;

                return $sql;

            case "hasError":
                return count($this->_errors) > 0;

        }

        return null;
    }
}