<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/7/16 0016
 * Time: 17:02
 */

class RSAUtil
{
    public static function privateKeyFormat($key)
    {
        $str = chunk_split($key, 64, "\n");

        return "-----BEGIN RSA PRIVATE KEY-----\n$str-----END RSA PRIVATE KEY-----\n";
    }

    public static function publicKeyFormat($key)
    {
        $str = chunk_split($key, 64, "\n");

        return "-----BEGIN PUBLIC KEY-----\n$str-----END PUBLIC KEY-----\n";
    }

    public static function getPublicKey()
    {
        $public_key = Mod::app()->params['money_system_config']['money_public_key'];

        return self::publicKeyFormat($public_key);
    }

    public static function getPrivateKey()
    {
        $private_key = Mod::app()->params['money_system_config']['money_private_key'];

        return self::privateKeyFormat($private_key);
    }

    /*
	 * 公钥加密
	 */
    public static function publicEncrypt($content, $public_key = '')
    {
        $public_key = $public_key ? self::publicKeyFormat($public_key) : self::getPublicKey();
        $split = str_split($content, 100);
        $encode_data = '';
        foreach ($split as $part)
        {
            $isOkay = openssl_public_encrypt($part, $en_data, $public_key, OPENSSL_ALGO_SHA1);
            if (!$isOkay)
            {
                return false;
            }
            $encode_data .= base64_encode($en_data);
        }

        return $encode_data;
    }


    /**
     * 私钥解密
     */
    public static function privateDecrypt($content, $private_key = '')
    {
        $private_key = $private_key ? self::privateKeyFormat($private_key) : self::getPrivateKey();
        $split = str_split($content, 172);
        $decode_data = '';
        foreach ($split as $part)
        {
            $isOkay = openssl_private_decrypt(base64_decode($part), $de_data, $private_key);
            $decode_data .= $de_data;
        }

        return $decode_data;
    }
}