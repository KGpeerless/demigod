<?php
namespace GKL\Helper\Des;

use GKL\Helper\Des\Adapter\AesEncrypt;
use GKL\Helper\Des\Adapter\Des3Encrypt;

class Des
{
    const MODE_AES = 1;
    const MODE_DES = 2;
    const MODE_3DES = 3;

    private static $_HANDLE_ARRAY = [];

    /**
     * @desc 获取句柄标识
     *
     * @param $params
     *
     * @return mixed
     */
    private static function _getHandleKey($params)
    {
        ksort($params);

        return md5(implode('_', $params));
    }

    /**
     * @desc 创建一个加密对象
     *
     * @param      $mode         加密类型
     * @param      $secretKey    密钥
     * @param null $iv           IV
     *
     * @return mixed
     * @throws Exception
     */

    public static function getInstance($mode, $secretKey, $iv = null)
    {
        if (empty($secretKey)) {
            throw new Exception(sprintf("Fail, aesKey不能为空."));
        }

        $handle_key = self::_getHandleKey([
            'mode'      => $mode,
            'secretKey' => $secretKey
        ]);

        if (!isset(self::$_HANDLE_ARRAY[$handle_key])) {
            switch ($mode) {
                case self::MODE_DES:
                    $obj = new DesEncrypt($secretKey);
                    break;
                case self::MODE_3DES:
                    $obj = new Des3Encrypt($secretKey, $iv);
                    break;
                case self::MODE_AES:
                default:
                    $obj = new AesEncrypt($secretKey);
                    break;
            }

            self::$_HANDLE_ARRAY[$handle_key] = $obj;
        }

        return self::$_HANDLE_ARRAY[$handle_key];
    }
}
