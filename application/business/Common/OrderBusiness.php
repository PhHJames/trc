<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-04-26 22:59:32
 * @des 公共的订单业务文件
 */
class OrderBusiness extends AbstractModel {
    /**
     * @des 今日订单个数数据统计
     */
    public function getTodayOrderNum($where = '' ){
        $today = date("Y-m-d",time() );
        $today_total = 0 ;
        $today_success_num = 0 ;
        $today_error_num = 0 ;
        //今日总订单数
        $sql = "select count(*) as c from orders where DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' {$where}  limit 1 ";
        $info = $this->db(0)->find($sql);
        $today_total = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        //成功总订单数
        $sql = "select count(*) as c from orders where DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 2   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $today_success_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;

        //失败总订单数
        $sql = "select count(*) as c from orders where DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 3   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $today_error_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        return [
            'today_total'=>$today_total,
            'today_success_num'=>$today_success_num,
            'today_error_num'=>$today_error_num,
        ];
    }

    /**
     * @des 昨日订单个数数据统计
     */
    public function getYestadyOrderNum($where = '' ){
        $today = date("Y-m-d", strtotime("-1 days") );
        $today_total = 0 ;
        $today_success_num = 0 ;
        $today_error_num = 0 ;
        //今日总订单数
        $sql = "select count(*) as c from orders where DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' {$where}  limit 1 ";
        $info = $this->db(0)->find($sql);
        $today_total = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        //成功总订单数
        $sql = "select count(*) as c from orders where DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 2   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $today_success_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;

        //失败总订单数
        $sql = "select count(*) as c from orders where DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 3   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $today_error_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        return [
            'today_total'=>$today_total,
            'today_success_num'=>$today_success_num,
            'today_error_num'=>$today_error_num,
        ];
    }

    /**
     * @des 累计订单数统计
     */
    public function getAllOrderNum($where = ''){
        $total = 0 ;
        $success_num = 0 ;
        $error_num = 0 ;
        //总订单
        $sql = "select count(*) as c from orders where  1 = 1  {$where}  limit 1 ";
        $info = $this->db(0)->find($sql);
        $total = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        //成功总订单数
        $sql = "select count(*) as c from orders where  1 = 1  and status = 2   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $success_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;

        //失败总订单数
        $sql = "select count(*) as c from orders where 1 = 1 and status = 3   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $error_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        return [
            'total'=>$total,
            'success_num'=>$success_num,
            'error_num'=>$error_num,
        ];
    }

    /**
     * @des 今日订单金额统计
     */
    public function getTodayOrderMoney($where = '' ){
        $today = date("Y-m-d",time() );
        $total_money = 0 ;
        $success_money = 0 ;
        $error_money = 0 ;
        $merch_money = 0 ;
        //总订单金额
        $sql = "select sum(money) as c  from orders where  1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' {$where}  limit 1 ";
        $info = $this->db(0)->find($sql);
        $total = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        //成功总订单金额
        $sql = "select sum(money) as c from orders where  1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 2   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $success_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;

        //失败总订单金额
        $sql = "select sum(money) as c from orders where 1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 3   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $error_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;

        //成功到商户的金额
        $sql = "select sum(real_money) as c from orders where 1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 2   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $merch_money = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        return [
            'total'=> Tools::number_format($total , NUMBER_FORMAT),
            'success_num'=>Tools::number_format($success_num, NUMBER_FORMAT),
            'error_num'=> Tools::number_format($error_num, NUMBER_FORMAT),
            'merch_money'=>Tools::number_format($merch_money, NUMBER_FORMAT)
        ];
    }


    /**
     * @des 昨日订单金额统计
     */
    public function getYestadyOrderMoney($where = '' ){
        $today = date("Y-m-d",strtotime("-1 days") );
        $total_money = 0 ;
        $success_money = 0 ;
        $error_money = 0 ;
        $merch_money = 0 ;
        //总订单金额
        $sql = "select sum(money) as c  from orders where  1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' {$where}  limit 1 ";
        $info = $this->db(0)->find($sql);
        $total = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        //成功总订单金额
        $sql = "select sum(money) as c from orders where  1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 2   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $success_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;

        //失败总订单金额
        $sql = "select sum(money) as c from orders where 1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 3   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $error_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;

        //成功到商户的金额
        $sql = "select sum(real_money) as c from orders where 1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 2   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $merch_money = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        return [
            'total'=> Tools::number_format($total, NUMBER_FORMAT),
            'success_num'=>Tools::number_format($success_num, NUMBER_FORMAT),
            'error_num'=> Tools::number_format($error_num, NUMBER_FORMAT),
            'merch_money'=>Tools::number_format($merch_money, NUMBER_FORMAT)
        ];
    }
    /**
     * @des 累计订单金额
     */
    public function getAllOrderMoney($where = '' ){
        $total_money = 0 ;
        $success_money = 0 ;
        $error_money = 0 ;
        $merch_money = 0 ;
        //总订单金额
        $sql = "select sum(money) as c  from orders where  1 = 1  {$where}  limit 1 ";
        $info = $this->db(0)->find($sql);
        $total = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        //成功总订单金额
        $sql = "select sum(money)  as c from orders where  1 = 1  and status = 2   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $success_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;

        //失败总订单金额
        $sql = "select sum(money) as c from orders where 1 = 1  and status = 3   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $error_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;

        //成功到商户的金额
        $sql = "select sum(real_money)  as c from orders where 1 = 1 and status = 2   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $merch_money = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        return [
            'total'=>Tools::number_format($total, NUMBER_FORMAT),
            'success_num'=>Tools::number_format($success_num, NUMBER_FORMAT),
            'error_num'=>Tools::number_format($error_num, NUMBER_FORMAT),
            'merch_money'=>Tools::number_format($merch_money, NUMBER_FORMAT)
        ];
    }

    /**
     * @des 今日成功率统计
     */
    public function todaySuccessRate( $where = '' ){
        $today = date("Y-m-d",time() );
        //总订单金额
        $sql = "select sum(money) as c  from orders where  1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' {$where}  limit 1 ";
        $info = $this->db(0)->find($sql);
        $total = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        if( $total <= 0 ){
            return 100 ;
        }
        //成功总订单金额
        $sql = "select sum(money) as c from orders where  1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$today}' and status = 2   {$where} limit 1 ";
        $info = $this->db(0)->find($sql);
        $success_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
        return Common::foramtNumber($success_num / $total , 2 ) * 100 ;
    }

    /**
     * @des 根据日期查询数据统计
     */
    public function getStatisByDate( $begin_date = '' , $end_date = '' , $where = ''  ){
        if( empty($begin_date) or empty($end_date )){
            return [];
        }
        //订单金额
        $date_where = " and  (DATE_FORMAT(create_date,'%Y-%m-%d' ) between '{$begin_date}' and '{$end_date}')   ";
        $where .= $date_where ;
        $group_by = " group by DATE_FORMAT(create_date,'%Y-%m-%d' )";
        //总订单金额
        $sql = "select DATE_FORMAT(create_date,'%Y-%m-%d' ) as c_date , sum(money) as c  from orders where  1 = 1     {$where}  {$group_by}  ";
        $total_order_money = $this->db(0)->find($sql);
        $total_order_money = $this->formatStatisDateData($total_order_money);

        //成功总订单金额
        $sql = "select DATE_FORMAT(create_date,'%Y-%m-%d' )  as c_date ,sum(money) as c from orders where  1 = 1 and status = 2   {$where} {$group_by} ";
        $success_order_money = $this->db(0)->find($sql);
        $success_order_money = $this->formatStatisDateData($success_order_money);
        //失败总订单金额
        $sql = "select DATE_FORMAT(create_date,'%Y-%m-%d' ) as c_date ,sum(money) as c from orders where 1 = 1  and status = 3   {$where} {$group_by}  ";
        $error_order_money = $this->db(0)->find($sql);
        $error_order_money = $this->formatStatisDateData($error_order_money);
        //成功到商户的金额
        $sql = "select DATE_FORMAT(create_date,'%Y-%m-%d' ) as c_date ,sum(real_money) as c from orders where 1 = 1  and status = 2   {$where} {$group_by}  ";
        $success_merch_money = $this->db(0)->find($sql);
        $success_merch_money = $this->formatStatisDateData($success_merch_money);

        //订单数统计

        //总订单
        $sql = "select DATE_FORMAT(create_date,'%Y-%m-%d' ) as c_date ,  count(*) as c from orders where  1 = 1  {$where} {$group_by} ";
        $total_order_num = $this->db(0)->find($sql);
        $total_order_num = $this->formatStatisDateData($total_order_num);
        //成功总订单数
        $sql = "select DATE_FORMAT(create_date,'%Y-%m-%d' ) as c_date , count(*) as c from orders where  1 = 1  and status = 2   {$where} {$group_by}  ";
        $success_order_num = $this->db(0)->find($sql);
        $success_order_num = $this->formatStatisDateData($success_order_num);
        //失败总订单数
        $sql = "select DATE_FORMAT(create_date,'%Y-%m-%d' ) as c_date , count(*) as c from orders where 1 = 1 and status = 3   {$where} {$group_by} ";
        $error_order_num = $this->db(0)->find($sql);
        $error_order_num = $this->formatStatisDateData($error_order_num);

        /*$res =  [
            'total_order_money'=> $total_order_money,
            'success_order_money'=>$success_order_money,
            'error_order_money'=> $error_order_money,
            'success_merch_money'=>$success_merch_money,
            'total_order_num'=>$total_order_num,
            'success_order_num'=>$success_order_num,
            'error_order_num'=>$error_order_num,
        ];*/
        /*echo "<pre>";
        print_r($res);*/
        $hash = [];
        $days =  Tools::getDayArrBetweenTwoDays( $begin_date , $end_date );
        foreach ($days as $item  ){
            $hash[] = [
                'day' => $item ,
                'total_order_money' =>  isset($total_order_money[$item])?$total_order_money[$item]:0,
                'success_order_money' =>  isset($success_order_money[$item])?$success_order_money[$item]:0,
                'error_order_money' =>  isset($error_order_money[$item])?$error_order_money[$item]:0,
                'success_merch_money' =>  isset($success_merch_money[$item])?$success_merch_money[$item]:0,
                'total_order_num' =>  isset($total_order_num[$item])?$total_order_num[$item]:0,
                'success_order_num' => isset($success_order_num[$item])?$success_order_num[$item]:0,
                'error_order_num' =>  isset($error_order_num[$item])?$error_order_num[$item]:0,
            ];
        }
        return $hash ;
    }

    /**
     * @des 格式化日期数据统计数据 , 返回日期为key的数组
     */
    public function formatStatisDateData( $res = [] ){
        $hash = [] ;
        foreach ($res as $key => $val ){
            $hash[$val['c_date']] = $val['c'] ;
        }
        return $hash ;
    }
    
  


}