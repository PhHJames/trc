<?php
//这个分页主要是普通的分页类
class NormalPage {
    
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow    ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    protected $config  = array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
    // 默认分页变量名
    protected $varPage;

    protected $url ;
    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     */
    public function __construct($totalRows,$listRows='') {
        $this->totalRows    =   $totalRows;
        $this->varPage      =   'page';
        if(!empty($listRows)) {
            $this->listRows =   intval($listRows);
        }
        $this->totalPages   =   ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages    =   ceil($this->totalPages/$this->rollPage);
        $this->nowPage      =   !empty($_REQUEST[$this->varPage])?intval($_REQUEST[$this->varPage]):1;
        if($this->nowPage<1){
            $this->nowPage  =   1;
        }elseif(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage  =   $this->totalPages;
        }
        $this->firstRow     =   $this->listRows*($this->nowPage-1);
        $this->getUrl();
    }
    private function getUrl(){
        $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];
        $dispatcher = Yaf_Dispatcher::getInstance();
        $request = $dispatcher->getRequest() ;
        $url.="/".strtolower($request->controller)."/".$request->action."?";
        $parameter = ($_GET) ? $_GET : array();
        if($parameter){
            unset($parameter[$this->varPage]);
            $parameter = http_build_query($parameter);
            $url .= '&' . $parameter;
        }
        $this->url = $url;
    }
    function get_page_url($page = 1 ){
        return $this->url."&{$this->varPage}={$page}";
    }
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     * 分页显示输出
     * @access public
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);
        //上下翻页字符串
        $upRow          =   $this->nowPage-1;
        $downRow        =   $this->nowPage+1;
        $upPage = '';
        if ($upRow>0){
            $upPage.="<li class=\"page-number\"><a href='{$this->get_page_url($upRow)}' >{$this->config['prev']}</a></li>";
        }else{
            $upPage     =   '';
        }

        if ($downRow <= $this->totalPages){
            
            $downPage = "<li class=\"page-next\"><a href='{$this->get_page_url($downRow)}' >{$this->config['next']}</a></li>";
          
        }else{
            $downPage   =   '';
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst   =   '';
            $prePage    =   '';
        }else{
            $preRow     =   $this->nowPage-$this->rollPage;
            $prePage = "<li class=\"page-number\"><a href='{$this->get_page_url($preRow)}' >上{$this->rollPage}页</a></li>";
            
            $theFirst = "<li class=\"page-number\"><a href='{$this->get_page_url(1)}' >{$this->config['first']}</a></li>";
          //  $theFirst   =   "<a href='".str_replace('__PAGE__',1,$url)."' >".$this->config['first']."</a>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage   =   '';
            $theEnd     =   '';
        }else{
            $nextRow    =   $this->nowPage+$this->rollPage;
            $theEndRow  =   $this->totalPages;
            
            $nextPage   = "<li class=\"page-next\"><a href='{$this->get_page_url($nextRow)}' >下{$this->rollPage}页</a></li>";
            
            $theEnd = "<li class=\"page-last\"><a href='{$this->get_page_url($theEndRow)}' >{$this->config['last']}</a></li>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page       =   ($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage.="<li class=\"page-number\"><a href='{$this->get_page_url($page)}'>{$page}</a></li>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    //$linkPage .= "<span class='current'>".$page."</span>";
                    $linkPage .="<li class=\"page-number active\"><a href=\"javascript:void(0)\" >{$page}</a></li>";
                }
            }
        }
        $pageStr ="";
        $pageStr     .=   str_replace(
            array('%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
        $pageStr .="<li  class=\"page-number active\"><a><code>每一页显示{$this->listRows}条数据,共{$this->totalRows}条数据,当前{$this->nowPage}/{$this->totalPages}页</code></a></li>";
        return $pageStr;
    }

}
