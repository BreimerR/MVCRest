<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/26/20
 * Time: 2:33 PM
 */

class App
{

    protected static $_instance;
    public $dirs = [];

    private function __construct($app_name = 'Pass app name', $debug = false)
    {
        define("DEBUG_STATE", $debug);
        define("APP_NAME", $app_name);

        // Upgrade to php 7.1 E_STRING ERROR FIX.
    }

    public static function init(
        $app_name = "App Name", $theme_name = "Unify", $dir_structure = [], $debug = false
    )
    {
        try {

            $app = self::getInstance($app_name, $debug);

            $app->registerDirStructure($dir_structure);
            $app->declareDefinitions();
            $app->prepareAutoloader();

            $app->startSession();
            $app->respond();

            return $app;

        } catch (Exception $e) {
            /// look who is to hold errors
            echo "Fatal Error";

            return null;
        }
    }

    public static function getInstance($name, $debug = false)
    {
        if (self::$_instance == null) self::$_instance = new self($name, $debug);

        return self::$_instance;
    }

    function registerDirStructure($dirs, $src = "")
    {
        foreach ($dirs as $name => $sub_dirs) {
            if (is_numeric($name)) {
                if (isset($this->dirs[$sub_dirs])) {
                    $relatives = explode("/", rtrim($src, "/"));
                    $c = count($relatives) - 1;
                    $name = $relatives[$c] . ucfirst($sub_dirs);
                    while ($c >= 0) {
                        if (!isset($this->dirs[$name])) break;
                        $name = ucfirst($relatives[$c] . $name);
                        $c--;
                    }
                    $this->dirs[$name] = $src . "$sub_dirs/";
                } else $this->dirs[$sub_dirs] = $src . "$sub_dirs/";
            } else {
                if (is_array($sub_dirs)) {
                    self::registerDirStructure([$name], $src);
                    self::registerDirStructure($sub_dirs, $src . "$name/");
                } else {
                    self::registerDirStructure([$name], $src);
                    self::registerDirStructure([$sub_dirs], $src . "$name/");
                }
            }
        }
    }

    function declareDefinitions()
    {
        $path = explode("/", rtrim(dirname(__FILE__)));

        $REQUEST_URI = filter_var($_SERVER["REQUEST_URI"], FILTER_SANITIZE_URL);

        define('REQUEST_URI', $REQUEST_URI);

        $URL = explode("?", $REQUEST_URI);

        $URI = explode("/", ltrim($URL[0], "/"));

        $c = count($URI);

        $c > 1 ? define("DOMAIN_NAME", $URI[0]) : define("DOMAIN_NAME", $path[count($path) - 1]);

        !($c > 1) ?: define("controller", $URI[1]);

        !($c > 2) ?: define("view", $URI[2]);

        !($c > 3) ?: define("MVC_REQUEST", join("/", array_slice($URI, 1)));

        !(count($URL) > 1) ?: define('GET_STRING', $URL[1]);

        define('HTTP_HOST', ($HTTP_HOST = $_SERVER["HTTP_HOST"]) == DOMAIN_NAME ? "" : $HTTP_HOST);

        define('REQUEST_URL', HTTP_HOST . "/" . DOMAIN_NAME);

        define('STATIC_FILES', 'public');

        define('BASE_ROUTE', '../');

        /** TODO
         * Base folder set up.
         * app/ => app php files go here.
         * public/ => all the css, images, tmp etc go here.ad
         */

        setUpStaticFiles(STATIC_FILES, array(
            'public/files/images/companies',
            'public/files/fonts',
        ));
    }

    function prepareAutoloader()
    {
        if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
            //SPL auto loading was introduced in PHP 5.1.2
            if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
                spl_autoload_register('App::autoload', true, true);
            } else {
                spl_autoload_register('App::autoload');
            }
        } else {
            /**
             * Fall back to traditional autoload for old PHP versions
             * @param $class string $class name The name of the class to load
             */
            function __autoload($class)
            {
                App::autoload($class);
            }
        }

    }

    private static function autoload($class)
    {
        list($bool, $file) = self::classExists($class);

        /** @noinspection PhpIncludeInspection */
        !$bool ?: require_once $file;
    }

    static function classExists($class)
    {
        $folders = self::$_instance->dirs;

        $source = null;
        $bool = false;
        foreach ($folders as $folder => $src) {
            if ($bool = file_exists($src . "$class.php")) {
                $source = $src . "$class.php";
                break;
            }
        }

        return [$bool, $source];
    }

    function startSession()
    {
        // start a session if none
        if (!(session_status() === PHP_SESSION_ACTIVE)) {
            session_start();
        }
    }

    /**
     * responds to the requested page;
     * */
    function respond()
    {

        $url = $this->parseUrl();


        $controller_name = ucfirst($url[0]);


        /**TODO
         * Thought if we want to have controllers that share a location
         * changes have to be made to this logic.
         * Change the page that is to be the landing for non existing controllers
         * @redirect = required page
         */

        $missing_controller_error = 'Page is in construction mode. We will get back to you soon';


        // check if a view is requested.
        isset($url[1]) ?: self::redirect("/index");

        $view = $url[1];

        // check if controller is there || check if there is a folder for the controller
        if (($boolean = file_exists("app/controllers/gui/views/{$controller_name}.php")) || folder_exists("app/gui/views/" . strtolower($controller_name))) {

            $_controller = $boolean ? new $controller_name($controller_name) : new View($controller_name);

            $_controller->controller_name = $controller_name;

            $_controller->controller = $_controller;

            $_controller->view = $view;

            !isset($_SERVER['HTTP_REFERER']) ?: $_controller->previous_page = $_SERVER['HTTP_REFERER'];

            call_user_func([$_controller, $_controller->view]);

        } // redirect to an autogenerated page while project initialization of the framework
        else self::redirect("Home/index", ['error' => $missing_controller_error, 'page' =>
            $controller_name]);

    }


    /**
     * @return array|null
     */
    public function parseUrl()
    {
        $url = isset($_GET['url']) && strlen($url = $_GET['url']) > 0 ? $url : null;
        return $_url = is_null($url)
            ? self::redirect("Home/index", array('error' => 'User Has Not requested for any content'))
            : explode('/', rtrim(filter_var($url, FILTER_SANITIZE_URL), '/'));
    }

    /**
     * @param $location
     * @param array|string $data key=>value spaces represented by underscores
     * @return array
     *
     */

    public static function redirect($location, $data = array())
    {

        if (!is_array($data)) {
            $data = array(($data = explode('=', $data))[0] => $data[1]);
        }

        $errors = "";

        if ($count = count($data)) {
            $i = 0;
            $errors .= '?';
            foreach ($data as $error => $value) {
                $errors .= "$error=" . $value . ($i < $count - 1 ? '&' : '');
                $i++;
            }
        }
        // location = base URL location/site.domain.com
        $domain = (HTTP_HOST == "localhost" || HTTP_HOST == "127.0.0.1" ? "localhost/" . DOMAIN_NAME : DOMAIN_NAME);

        header("location:" . $_SERVER("REQUEST_SCHEME") . "://" . $domain . "/$location" . $errors);

        exit("403");
    }

    /**
     * TODO
     * Evaluates the type of the system and
     * requests for the required type of page with.
     * for mobile devices and also for pc version.
     * this would not be necessary if the page is responsive but useful
     * for javascript disabled phones.
     */
    function deviceType()
    {
        /**
         * TODO
         * Pick From home.co.ke
         */

        $_deviceType = "laptop";
    }

    public function secureLink()
    {

    }
}