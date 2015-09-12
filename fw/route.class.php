<?php
/**
 * route
 * Route class for MVC applications
 * @author  Leonardo Ruiz 
 */
class route{
	/**
     * @var array Routes to watch
     */
	private $routes;

	/**
     * @var string Search directory for controllers
     */
	private $controller_path = "app/controller/";

	/**
     * @var string Attachment for controller name
     */
	private $name_attachment = "Controller";

	/**
     * @var string Default method to return
     */
	private $default_method = "index";

	/**
     * Add a new route
     * @param string $request Request method (get or post) 
     * @param string $url URL to be redirected
     * @param string $controller controller to be called
     * @param string $method method to be called
     */
	public function add($request, $url, $controller, $method){
		$route = array("request" => $request, "url" => $url, "controller" => $controller, "method" => $method);
		$this->routes[] = $route;
	}

	/**
     * Find a route (controller and method) for a url     
     * @param string $url URL to search     
     * @param string $method Request method (get or post)
     * @return array With controller, method and params
     */
	public function find($url,$method){	
		$url = explode("/",$url);
		foreach($this->routes as $route){
			$different = 0;
			$route_url = explode("/",$route['url']);
			$segment = 0;
			foreach($route_url as $ru){
				if(isset($url[$segment])){
					if($ru != $url[$segment] && $ru != '{any}'){
						$different++;
					}
				}else{
					$different++;
				}
				$segment++;
			}
			if($different == 0){
				if($method == $route["request"] || $route["request"] == 'both'){
					$return = array("controller" => $route['controller'], "method" => $route['method']);				
					break;
				}				
			}else{
				$controller = $url[0].$this->name_attachment;
				$controller_path = $this->controller_path.$controller.".class.php";
				if(file_exists($controller_path)){					
					if(method_exists(new $controller, $url[1])){
						$return = array("controller" => $controller, "method" => $url[1]);
						
					}else{
						$return = array("controller" => $controller, "method" => $this->default_method);
						
					}
				}				
			}
		}
		if(isset($return)){
			$params = array();
			foreach($url as $u){
				$controller_name = str_replace('Controller', '', $return['controller']);
				if($u != $controller_name && $u != $return['method'] && $u != ''){
					$params[] = $u;
				}
			}
			$return['params'] = $params;
		}
		return $return;
	}

}

?>