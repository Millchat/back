<?php
namespace Millchat\Core;

use Millchat\Envorinment\Loader;
use Millchat\Core\Request;

// FIXME Esto no deberia estar pero tengo un problema con las direcciones de use
// require_once (__DIR__ . "/Request.php"); 
// require_once (__DIR__ . "/../Envorinment/Loader.php");
// require_once __DIR__ . '/../../../autoload.php';

/**
 * Class App
 *
 * @package Millchat\Core
 *
 *          Maneja el funcionamiento básico de la aplicación.
 */
class App
{

    private static $rootPath;

    private static $appPath;

    private static $publicPath;

    private static $viewsPath;

    private static $urlPath;

    /** @var Request La petición del usuario. */
    protected $request;

    /** @var Loader Objeto con las variables de entorno. */
    private static $loader;

    /**
     * App constructor.
     *
     * @param
     *            $rootPath
     */
    public function __construct($rootPath)
    {
        self::$rootPath = $rootPath;
        self::$appPath = $rootPath . '/app';
        self::$publicPath = $rootPath . '/public';
        self::$viewsPath = $rootPath . '/views';

        self::$urlPath = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];

        self::$urlPath = substr(self::$urlPath, 0, - 9);

        // Instanciamos el Loader.
        self::$loader = new Loader(self::$rootPath);
    }

    /**
     * Arranca la aplicación.
     */
    public function run()
    {
        // Obtenemos la petición.
        $this->request = new Request();
        
//         print_r(get_included_files ());
        // Verificamos si la ruta existe.
        
        if (Route::exists($this->request->getMethod(), $this->request->getRequestedUrl())) {
            $controller = Route::getController($this->request->getMethod(), $this->request->getRequestedUrl());
            $this->executeController($controller);
        } else {
            
            throw new \Exception("No existe la ruta especificada. ");
            // Opcionalmente, podemos directamente llamar a una página que muestre un error 404 o una página
            // template que diga que el recurso no se encontró.
        }
    }

    /**
     * Instancia el controller y ejecuta el método.
     *
     * @param string $controller
     *            El controller y su método.
     */
    public function executeController($controller)
    {
        // $controller = "HomeController@index";
        $controllerData = explode('@', $controller);
        $controllerName = $controllerData[0];
        $controllerMethod = $controllerData[1];

        // $controllerName = "HomeController";
        // Le agregamos el namespace a la clase.
        $controllerName = "\\Millchat\\Controllers\\" . $controllerName;
        // Esto nos deja, ej:
        // \Millchat\Controllers\HomeController

        // Instanciamos el controller.
        // Ej: new \Millchat\Controllers\HomeController
        $controllerObject = new $controllerName();

        // Ejecutamos su método.
        $controllerObject->{$controllerMethod}();
    }

    /**
     * Redirecciona al $path indicado.
     *
     * @param string $path
     */
    public static function redirect($path = '')
    {
        header('Location: ' . self::getUrlPath() . $path);
        exit();
    }

    /**
     * Retorna una url absoluta para el $path indicado.
     *
     * @param string $path
     * @return string
     */
    public static function urlTo($path)
    {
        // Quitamos la barra de inicio de la ruta, de estar presente.
        if (strpos($path, '/') === 0) {
            $path = substr($path, 1);
        }

        return self::$urlPath . $path;
    }

    /**
     *
     * @return mixed
     */
    public static function getRootPath()
    {
        return self::$rootPath;
    }

    /**
     *
     * @return string
     */
    public static function getAppPath()
    {
        return self::$appPath;
    }

    /**
     *
     * @return string
     */
    public static function getPublicPath()
    {
        return self::$publicPath;
    }

    /**
     *
     * @return string
     */
    public static function getViewsPath()
    {
        return self::$viewsPath;
    }

    /**
     *
     * @return mixed
     */
    public static function getUrlPath()
    {
        return self::$urlPath;
    }

    /**
     * Retorna el valor de entorno asociado a la $key.
     *
     * @param string $key
     * @return array|false|string
     */
    public static function getEnv($key)
    {
        return self::$loader->getEnv($key);
    }
}
