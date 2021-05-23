<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2021/1/20 0020
 * Time: 15:05
 */

require_once  dirname(__FILE__)  . '/Base58Check.php' ;
require_once dirname(__FILE__)  . '/Base58.php' ;
require_once  dirname(__FILE__)  .'/Hash.php' ;
require_once  dirname(__FILE__)  .'/Crypto.php' ;
class Tron_Address_Address{
    /**
     * Convert from Hex
     *
     * @param $string
     * @return string
     */

    public function fromHex($string)
    {
        if(strlen($string) == 42 && mb_substr($string,0,2) === '41') {
            return $this->hexString2Address($string);
        }

        return $this->hexString2Utf8($string);
    }

    /**
     * Check Hex address before converting to Base58
     *
     * @param $sHexString
     * @return string
     */
    public function hexString2Address($sHexString)
    {
        if(!ctype_xdigit($sHexString)) {
            return $sHexString;
        }

        if(strlen($sHexString) < 2 || (strlen($sHexString) & 1) != 0) {
            return '';
        }

        return Base58Check::encode($sHexString,0,false);
    }

    /**
     * Convert hex to string
     *
     * @param $sHexString
     * @return string
     */
    public function hexString2Utf8($sHexString)
    {
        return hex2bin($sHexString);
    }
}