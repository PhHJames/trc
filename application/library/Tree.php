<?php
/**
 * @Author: Awe
 * @Date:   2016-05-22 21:03:17
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-06-06 10:46:31
 */
class Tree{

    //格式化从数据库查询的树状数据，索引为自增的编号ID
    public static function formatData($data , $id = "id"){
        $temp = array();
        foreach($data as $key => $val ){
            $temp[$val[$id]] = $val ;
        }
        return $temp;
    }
    /**
     * 将数据格式化成树形结构
     * @author 王建
     * @param array $items
     * @return array
     */
    public static function genTree9($items,$id = 'id' ,$pid = 'pid' ,$child = 'children'){
        $tree = array(); //格式化好的树
        foreach ($items as $item)
            if (isset($items[$item[$pid]]))
            $items[$item[$pid]][$child][] = &$items[$item[$id]];
        else
            $tree[] = &$items[$item[$id]];
        return $tree;
    }
    /**
     * 格式化select
     * @author 王建
     * @param array $parent
     * @deep int 层级关系
     * @return array
    */
   public static function getChildren($parent,$deep=0 , $id = 'id' ,$pid = 'pid' , $children = 'children'    ) {
        $data = array();
        foreach($parent as $row) {
            $temp = $row ; 
            unset($temp[$children]);
            $temp['deep'] = $deep ;
            $data[] = $temp;
            if (isset($row['children']) && !empty($row['children'])) {
                $data = array_merge($data, self::getChildren($row[$children], $deep+1 , $id , $pid  , $children));
            }
        }
        return $data;
   }
    public static function tree_format(&$list,$pid=0,$level=0,$html='--',$pid_string = 'pid' ,$id_string = 'id'){
        static $tree = array();
        foreach($list as $v){
            if($v[$pid_string] == $pid){
                $v['sort'] = $level;
                $v['html'] = str_repeat($html,$level);
                $tree[] = $v;
                self::tree_format($list,$v[$id_string],$level+1,$html,$pid_string,$id_string);
            }
        }
        return $tree;
    }   

}