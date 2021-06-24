<?php

namespace Core;

use \App\Auth;
use \App\Extra;
/**
 * Base controller
 *
 * PHP version 7.0
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }

    /**
     * Redirect to a different page
     *
     * @param string $url  The relative URL
     *
     * @return void
     */
    public function redirect($url)
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }

    /**
     * Show page not found 404
     * 
     * @return void
     */
    public function show404()
    {
        header('Location: /errors/404/'); 
        exit;
    }

    /**
     * Allow page access to admin only
     * 
     * @return void 
     */
    public function requireAdmin()
    {
        $currentRoute = $_SERVER['REQUEST_URI'];
        if(!Auth::isAuthenticated()){
            $this->redirect("/login/?next=".$currentRoute);
        }
        if(!Auth::isAdminAuthenticated()){
            $this->redirect("/");
        }
    }

    /**
     * Allow page access to trader only
     * 
     * @return void 
     */
    public function requireTrader()
    {
        $currentRoute = $_SERVER['REQUEST_URI'];
        if(!Auth::isAuthenticated()){
            $this->redirect("/login/?next=".$currentRoute);
        }
        if(!Auth::isTraderAuthenticated()){
            $this->redirect("/");
        }
    }

    /**
     * Require the user to be logged in before giving access to the requested page.
     * Remember the requested page for later, then redirect to the login page.
     *
     * @return void
     */
    public function requireLogin()
    {
        if (! Auth::getUser()) {

            $this->redirect('/');
        }
    }

    /**
     * Restrict for authenticated data
     * 
     * @return void
     */
    public function restrictForAuthenticated()
    {
        if(Auth::getUser()){
            $this->redirect('/');
        }
    }

    /**
     * Restrict path to users that has account
     * But the path does not belongs to them
     * 
     * @return void
     */
    public function restrictToPathAndSession()
    {
        $path = $this->routerPath();
        if (Auth::getUser()->username !== $path) {
            
            $this->redirect('/' . Auth::getUser()->username . '/');
        }
    }
}
