<?php
/**
 * @name SamplePlugin
 * @desc Yaf定义了如下的6个Hook,插件之间的执行顺序是先进先Call
 * @see http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 * @author {&$AUTHOR&}
 */
class BasePlugin extends Yaf_Plugin_Abstract {

	public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
			
			// parse cli
			if ($request->isCli ()) {
				global $argc, $argv;
				if ($argc > 1) {
					$module = '';
					$uri = $argv [1];
					if (preg_match ( '/^[^?]*%/i', $uri )) {
						list ( $module, $uri ) = explode ( '%', $uri, 2 );
					}
					$module = strtolower ( $module );
					$modules = Yaf_Application::app ()->getModules ();
					if (in_array ( ucfirst ( $module ), $modules )) {
						$request->setModuleName ( $module );
					}
					if (false === strpos ( $uri, '?' )) {
						$args = array ();
					} else {
						list ( $uri, $args ) = explode ( '?', $uri, 2 );
						parse_str ( $args, $args );
					}
					foreach ( $args as $k => $v ) {
						$request->setParam ( $k, $v );
					}
					$request->setRequestUri ( $uri );
					if ($request->isRouted () && ! empty ( $uri )) {
						if (false !== strpos ( $uri, '/' )) {
							list ( $controller, $action ) = explode ( '/', $uri );
							$request->setActionName ( $action );
						} else {
							$controller = $uri;
						}
						$request->setControllerName ( ucfirst ( strtolower ( $controller ) ) );
					}
				}
			}
      
	}

	public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
      
           
	}

	public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        
	}

	public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        
	}

	public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}

	public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}
}