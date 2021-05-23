<?php
/**
 * @name Bootstrap
 * @author {&$AUTHOR&}
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{
    public function _initConfig() {
		//把配置保存起来,保存到超全局变量里面
		$config = new Yaf_Config_Ini(APP_PATH.'/conf/application.ini');
		$arrayData = $config->toArray();
		$GLOBALS['_G']['config'] = $arrayData ;
		Yaf_Dispatcher::getInstance()->autoRender(FALSE);  // 关闭自动加载模板
	}

	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		//注册一个插件
		$objSamplePlugin = new BasePlugin();
		$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(Yaf_Dispatcher $dispatcher) {
      	
	}
	public function _initView(Yaf_Dispatcher $dispatcher){
		$version = include_once APP_PATH . "/conf/version.php";
		global $_G ;
		$_G['config']['version'] = $version ;
		//在这里注册自己的view控制器，例如smarty,firekylin
		$view = new Twig_Adapter(APP_PATH . DIRECTORY_SEPARATOR."application".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR);
		$dispatcher->setView($view);
		$viewObject = $dispatcher->initView($_G['config']['product']['application']['directory']."views/");
		$_G['viewObject'] = $viewObject;//设置一个模版的对象
		$_G['viewObject']->assign("siteName" , $_G['config']['setting']['setting']['name']);
		$_G['viewObject']->assign("version" ,$version);
		foreach ($_G['config']['domain']['domain'] as $key => $value) {
			$_G['viewObject']->assign($key ,  $value);
		}
	}
}