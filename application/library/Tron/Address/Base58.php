<?php



class Base58
{
    /**
     * Encodes the passed whole string to base58.
     *
     * @param $num
     * @param int $length
     *
     * @return string
     */
    public static function encode($num, $length = 58)
    {
        return Crypto::dec2base($num, $length, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }

    /**
     * Base58 decodes a large integer to a string.
     *
     * @param string $addr
     * @param int $length
     *
     * @return string
     */
    public static function decode( $addr,  $length = 58)
    {
        return Crypto::base2dec($addr, $length, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }
}
