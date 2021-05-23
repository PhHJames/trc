<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author wangjian\administrator
 */
class ErrorController extends Yaf_Controller_Abstract {
	public function errorAction($exception) {
		/* error occurs */
		/*switch ($exception->getCode()) {
			case YAF_ERR_NOTFOUND_MODULE:
			case YAF_ERR_NOTFOUND_CONTROLLER:
			case YAF_ERR_NOTFOUND_ACTION:
			case YAF_ERR_NOTFOUND_VIEW:
				//echo 404, ":", $exception->getMessage();
				Common::EchoResult(-4 , $exception->getMessage());
				//header( "HTTP/1.1 404 Not Found" );
				//header( "Status: 404 Not Found" );
				break;
			default :
				//$message = $exception->getMessage();
				Common::EchoResult(-4 , $exception->getMessage());
				//echo 0, ":", $exception->getMessage();
				break;
		}*/
		if($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1 ){
			Common::EchoResult(-4 , $exception->getMessage());
		}else{
			global $_G;
			$args['head'] = "系统提示";
			$args['message'] = $exception->getMessage();

			exit( $exception->getMessage() );
			$_G['viewObject']->assign("args" , $args);
        	$this->displayTemplate("public/show_error" , array(
        		'head' => $args['head'],
        		'message'=>$exception->getMessage()
        	) );
		}
	}
}
