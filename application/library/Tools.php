<?php
class Tools {
    /*
     * 判定用是否是手机
     * @return boolean
    */
   public static function isMobile($mobile){
        $mobile = strtolower($mobile);
        if (preg_match('/^[1-9]\d{10}$/', $mobile)) {
            return true;
        }
        return false ;
   }
   /*
     * 判定用是否是邮箱
     * @return array
    */
   public static function isEmail($email){
        $email = strtolower($email);
        if (preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email)) {
            return true;
        }
        return false;
   }
    public static function build_order_no( $len = 16 ){
       $d  = array_map('ord', str_split(substr(uniqid(), 7, 13), 1));
       $a1 = count($d);
       if( ($a1 + 2 ) >$len ){
           return "";
       }
       $a2 = $len - $a1 - 2  ;
       return date('Ymd').substr(implode(NULL, $d ), 0, $a2);
    }

    /*银行卡Luhm 校验
    16-19 位卡号校验位采用 Luhm 校验方法计算：
    1，将未带校验位的 15 位卡号从右依次编号 1 到 15，位于奇数位号上的数字乘以 2
    2，将奇位乘积的个十位全部相加，再加上所有偶数位上的数字
    3，将加法和加上校验位能被 10 整除。
    */
    public static function bank_luhm($s){
        if(!is_numeric($s)){
            return false;
        }
        $n = 0;
        $ns = strrev($s); // 倒序
        for ($i=0; $i <strlen($s) ; $i++) {
            if ($i % 2 ==0) {
                $n += $ns[$i]; // 偶数位，包含校验码
            }else{
                $t = $ns[$i] * 2;
                if ($t >=10) {
                    $t = $t - 9;
                }
                $n += $t;
            }
        }
        return ( $n % 10 ) == 0;
    }

    public static function number_format( $string ,int  $number =  8   ){
        return ( sprintf("%.{$number}f",$string) );
    }

    /**
     * 求两个日期之间相差的天数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $day1
     * @param string $day2
     * @return number
     */
    public static function diffBetweenTwoDays ($day1, $day2){
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);
        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }

    /**
     * 计算2个时间段的时间 返回数组
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $day1
     * @param string $day2
     * @return array
     */
    public static function getDayArrBetweenTwoDays ($day1, $day2){
        $days = self::diffBetweenTwoDays( $day1 , $day2 );
        $hash = [] ;
        for( $index = 0 ; $index <= $days ; $index ++  ){
            $hash[] = date( "Y-m-d" , strtotime($day1 ) + $index * 86400 ) ;
        }
        return $hash ;
    }
    public static function arraySort($array,$keys,$sort='asc') {
        $newArr = $valArr = array();
        foreach ($array as $key=>$value) {
            $valArr[$key] = $value[$keys];
        }
        ($sort == 'asc') ?  asort($valArr) : arsort($valArr);
        reset($valArr);
        foreach($valArr as $key=>$value) {
            $newArr[$key] = $array[$key];
        }
        return $newArr;
    }
}
