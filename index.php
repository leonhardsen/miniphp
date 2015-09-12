<?php
/**
 * Mini - micro php framework 
 * @author      Leonardo Ruiz
 * @version     1.0.0 
 */
require('fw/core.php');
$url = isset($_GET['url']) ? $_GET['url'] : $default_controller.'/'.$default_method;
$method = strtolower($_SERVER['REQUEST_METHOD']);
$routes = $route->find($url,$method);
$controller = new $routes['controller'];
$controller->$routes['method']($routes['params']);
?>