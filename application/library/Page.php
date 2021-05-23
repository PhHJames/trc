<?php
//这个分页主要是为了ajax分页使用的
class Page {
    
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页URL地址
    public $url     =   '';
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
    protected $config  =    array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
    // 默认分页变量名
    protected $varPage;
	
    //ajax 分页的方法名称
    public $ajax_data_function = 'ajax_data' ;
    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     * @param string $ajax_data_function  分页跳转的参数
     */
    public function __construct($totalRows,$listRows='',$parameter='',$url='' , $ajax_data_function = 'ajax_data' ) {
        $this->totalRows    =   $totalRows;
        $this->parameter    =   $parameter;
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
        if(!empty($url))    $this->url  =   $url; 
        $this->ajax_data_function = $ajax_data_function ;
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
        	
            $upPage.="<li class=\"page-number\"><a href=\"javascript:void(0)\" onclick='{$this->ajax_data_function}($upRow)'>{$this->config['prev']}</a></li>";
        }else{
            $upPage     =   '';
        }

        if ($downRow <= $this->totalPages){
        	
            $downPage = "<li class=\"page-next\"><a href=\"javascript:void(0)\" onclick='{$this->ajax_data_function}($downRow)'>{$this->config['next']}</a></li>";
          
        }else{
            $downPage   =   '';
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst   =   '';
            $prePage    =   '';
        }else{
            $preRow     =   $this->nowPage-$this->rollPage;
            $prePage = "<li class=\"page-number\"><a href=\"javascript:void(0)\" onclick='{$this->ajax_data_function}($preRow)'>上{$this->rollPage}页</a></li>";
            
            $theFirst = "<li class=\"page-number\"><a href=\"javascript:void(0)\" onclick='{$this->ajax_data_function}(1)'>{$this->config['first']}</a></li>";
          //  $theFirst   =   "<a href='".str_replace('__PAGE__',1,$url)."' >".$this->config['first']."</a>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage   =   '';
            $theEnd     =   '';
        }else{
            $nextRow    =   $this->nowPage+$this->rollPage;
            $theEndRow  =   $this->totalPages;
            
            $nextPage   = "<li class=\"page-next\"><a href=\"javascript:void(0)\" onclick='{$this->ajax_data_function}($nextRow)'>下{$this->rollPage}页</a></li>";
            
            $theEnd = "<li class=\"page-last\"><a href=\"javascript:void(0)\" onclick='{$this->ajax_data_function}($theEndRow)'>{$this->config['last']}</a></li>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page       =   ($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage.="<li class=\"page-number\"><a href=\"javascript:void(0)\" onclick='{$this->ajax_data_function}($page)'>{$page}</a></li>";
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
        $pageStr ="<span class=\"pagination-info\">每一页显示{$this->listRows}条数据";
        $pageStr     .=   str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header']."</span>",$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
        return $pageStr;
    }

}
