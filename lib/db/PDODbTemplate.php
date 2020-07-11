<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/8/20
 * Time: 4:11 AM
 * @property string error
 */


trait PDODbTemplate
{

    protected array $_errors = [];

    protected array $_bindValues = [];

    protected array $_bindKeys = [];

    protected ?string $_sql = null;

    protected ?string $_preparedSql = null;

    protected ?PDOStatement $_preparedStatement = null;

    use OnCreateCallBack;

    use OnDestroyCallBack;

    protected function __construct($settings = [])
    {

        if ($this->initConnection($settings)) {
            if (!$this->use($dbName = $settings["dbName"])) {
                switch ($code = $this->errorCode) {
                    case 1049:
                        $this->onMissingDatabase($dbName);
                        break;
                    case 42000:
                        $this->onMissingDatabase($dbName);
                }
            }
        } else {
            switch ($code = $this->errorCode) {
                case  2002:
                    $this->onConnectionFailed($settings["host"]);
                    break;
                default:
                    throw $this->_errors[$this->errorCount - 1];
            }
        }

    }


    function onConnectionFailed($host)
    {
        throw $this->exception;
    }

    function initConnection($settings)
    {
        try {
            $this->conn = $this->connect($settings);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->handleError($e);
        }

        return $this->hasConnection;
    }

    function use($dbName)
    {
        return $this->query("use `$dbName`;");
    }

    function onMissingDatabase($dbName)
    {
        throw new PDOException("Missing database $dbName");
    }

    abstract function connect($configs): PDO;

    function createDatabase($dbName)
    {

        /**TODO create db does not allow for binding params
         * $key = ":key_1";
         *
         * $executed = $this->query("CREATE DATABASE IF NOT EXISTS $key;", [$key], [$dbName]);
         */
        $executed = $this->query("CREATE DATABASE IF NOT EXISTS $dbName;");

        if (!$executed) {
            $this->handleError(new PDOException($this->error));
        }

        $this->use($dbName);

        $this->onCreate();


    }

    function onCreate()
    {

    }

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
                $exception = $this->exception;

                if ($exception != null) {
                    return strtolower(get_class($exception)) == strtolower(PDOException::class) ? $exception->getMessage() : $exception;
                } else return null;
                break;
            case "exception":
                $c = $this->errorCount;
                if (isset($this->_errors[$c - 1])) {
                    return $this->_errors[$c - 1];
                } else return null;
                break;
            case "errorCode":
                if (($error = $this->exception) != null) {
                    return strtolower(get_class($error)) == strtolower(PDOException::class) ? $error->getCode() : null;
                } else return null;
                break;
            case "errors":
                return $this->_errors;
                break;
            case "errorCount":
                return count($this->_errors);
                break;
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
                break;
            case "hasError":
                return count($this->_errors) > 0;
                break;
            case "hasConnection":
                return $this->conn != null;
                break;

        }

        return null;
    }
}