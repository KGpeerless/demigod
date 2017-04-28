<?php
namespace GKL\Message\Ali;

use GKL\Helper\HttpUtils;
use GKL\Message\MessageInterface;
use Psr\Log\LoggerInterface;

class Sms implements MessageInterface
{
    private $defaultConfig = [
        'app_key'         => '23494031',
        'secret_key'      => 'cc33b0a27634d8e64448229dd1aee647',
        'template_id'     => 'SMS_22565084',
        'extend'          => '',
        'sms_type'        => 'normal',
        'sign_name'       => '美创兄弟',
        'sign_method'     => 'md5',
        // api 版本
        'api_version'     => '2.0',
        // sdk 版本
        'sdk_version'     => 'top-sdk-php-20151012',
        // 返回数据格式
        'format'          => 'json',
        'gateway_url'     => 'http://gw.api.taobao.com/router/rest?',
        'read_timeout'    => '',
        'connect_timeout' => '',
        'api_method_name' => 'alibaba.aliqin.fc.sms.num.send'
    ];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, $config = [])
    {
        $this->logger        = $logger;
        $this->defaultConfig = array_merge($this->defaultConfig, $config);
    }

    /**
     * @param       $phone
     * @param       $templateId
     * @param array $content
     *
     * @return array|mixed
     */
    public function send($phone, $templateId, array $content)
    {
        // 业务数据
        $apiParams = [
            'extend'             => $this->defaultConfig['extend'],
            'sms_type'           => $this->defaultConfig['sms_type'],
            'sms_free_sign_name' => $this->defaultConfig['sign_name'],
            'sms_param'          => json_encode($content),
            'rec_num'            => $phone,
            'sms_template_code'  => $templateId,
        ];

        $this->logger->error('业务数据', $apiParams);

        //组装系统参数
        $sysParams = [
            'app_key'     => $this->defaultConfig['app_key'],
            'v'           => $this->defaultConfig['api_version'],
            'format'      => $this->defaultConfig['format'],
            'sign_method' => $this->defaultConfig['sign_method'],
            'method'      => $this->defaultConfig['api_method_name'],
            'timestamp'   => date("Y-m-d H:i:s"),
            'partner_id'  => $this->defaultConfig['sdk_version']
        ];


        $sysParams["sign"] = $this->buildSign($apiParams, $sysParams);

        foreach ($sysParams as $sysParamKey => $sysParamValue) {
            $this->defaultConfig['gateway_url'] .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
        }

        $fileFields = [];
        foreach ($apiParams as $key => $value) {
            if (is_array($value) && array_key_exists('type', $value) && array_key_exists('content', $value)) {
                $value['name']    = $key;
                $fileFields[$key] = $value;
                unset($apiParams[$key]);
            }
        }

        $requestUrl = substr($this->defaultConfig['gateway_url'], 0, -1);
        $this->logger->error('发送数据', $apiParams);
        $response   = json_decode(HttpUtils::post($requestUrl, $apiParams), true);

        if (isset($response['error_response'])) {
            $this->logger->error('message send response failure', $response);
            return $response;
        } else {
            $this->logger->error('message send response success', $response);
            return [
                'requestId' => $response
            ];
        }
    }

    // 生成签名
    private function buildSign($apiParams, $sysParams)
    {
        $params = array_merge($apiParams, $sysParams);
        ksort($params);
        $stringToBeSigned = $this->defaultConfig['secret_key'];
        foreach ($params as $k => $v) {
            if (is_string($v) && "@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $this->defaultConfig['secret_key'];
        return strtoupper(md5($stringToBeSigned));
    }
}