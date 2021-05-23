<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-06-20 20:57:56
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-04-09 20:28:22
 * @des 模版自定义方法类库
 */
class Twig_Extension_Function extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('getu', 'twig_getu'),
        );
    }
    public function getName()
    {
        return 'shixiba_function';
    }
}
//获取当前访问的url地址 ，这个一般是模版来调用的
//url地址
//@vars 需要替换的数据 必须是类似 tid=10&fid=100这样的格式
//@extra 其他参数 数组形式 例如 hits=1&ff=rrr
/*function twig_getu($url = '',  $vars  = ''  , $extra = ''){
        if($vars and !empty($vars)){
            parse_str($vars , $output );
            $vars = $output  ;
        }
        global $_G;
        static $urlMapData = 0;
        if(empty($urlMapData)){
            $urlMapData = include APP_PATH."/conf/urlMap.php" ;
        }
        $lastUrl = '' ;
        $tempUrl = '' ;
        //替换数据
        if(isset($urlMapData[$url])){
            if($vars AND is_array($vars)){
                foreach($vars as $k => $v) {
                    $searchs[] = '{'.$k.'}';
                    $replaces[] = $v;
                }
                $tempUrl = str_replace($searchs, $replaces, $urlMapData[$url] );

                return $_G['config']['domain']['domain']['PC_HOST'] . $tempUrl . ( (!empty($extra) && $extra != '?' ) ? ('?'.$extra) : '')  ;
            }else{
                return $_G['config']['domain']['domain']['PC_HOST'] . $urlMapData[$url] . ( (!empty($extra) && $extra != '?' ) ? ('?'.$extra) : '')  ;
            }
        }
        //不在urlMap里面
        $info = parse_url($url);
        list($module , $action) = explode("/" , $info['path']);
        $extra = (!empty($extra)) ? ('&'.$extra) : '' ;
        return $_G['config']['domain']['domain']['PC_HOST'] .$_G['config']['product']['application']['index_page'] . "?c={$module}&a={$action}".$extra ;
}
*/


//获取当前访问的url地址 ，这个一般是模版来调用的
//url地址
//@vars 需要替换的数据 必须是类似 tid=10&fid=100这样的格式
//@extra 其他参数 数组形式 例如 hits=1&ff=rrr
function twig_getu($url = '',   $extra = ''){
        global $_G; 
        //不在urlMap里面
        $info = parse_url($url);
        list($module , $action) = explode("/" , $info['path']);
        //$extra = (!empty($extra)) ? ('?'.$extra) : '' ;
        $extra = (!empty($extra)) ? ('?'.$extra) : '' ;
        return $_G['config']['domain']['domain']['ADMIN_DOMAIN'] .$module ."/" . $action . $extra;
        //return $_G['config']['domain']['domain']['H5_HOST'] .$_G['config']['product']['application']['index_page'] . "?c={$module}&a={$action}".$extra ;
}



