<?php

namespace Dwc\AdaPay\AdaPayCore;

use Dwc\AdaPay\AdaPayCore\utils\AdaRequests;
use Dwc\AdaPay\AdaPayCore\utils\AdaTools;

class AdaPay
{

    public static string $api_key = "";
    public static string $rsaPrivateKeyFilePath = "";
    public static string $rsaPrivateKey = "";
    # 不允许修改
    public static string $rsaPublicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCwN6xgd6Ad8v2hIIsQVnbt8a3JituR8o4Tc3B5WlcFR55bz4OMqrG/356Ur3cPbc2Fe8ArNd/0gZbC9q56Eb16JTkVNA/fye4SXznWxdyBPR7+guuJZHc/VW2fKH2lfZ2P3Tt0QkKZZoawYOGSMdIvO+WqK44updyax0ikK6JlNQIDAQAB";
    public static array $header = ['Content-Type:application/json'];
    public static array $headerText = ['Content-Type:text/html'];
    public static array $headerEmpty = ['Content-Type:multipart/form-data'];
    public string $gateWayUrl = "";
    public string $gateWayType = "api";
    public static string $mqttAddress = "post-cn-0pp18zowf0m.mqtt.aliyuncs.com:1883";
    public static string $mqttInstanceId = "post-cn-0pp18zowf0m";
    public static string $mqttGroupId = "GID_CRHS_ASYN";
    public static string $mqttAccessKey = "LTAIOP5RkeiuXieW";

    public static bool $isDebug;
    public static string $logDir = "";
    public string $postCharset = "utf-8";
    public string $signType = "RSA2";
    public AdaRequests|string $ada_request = "";
    public string|AdaTools $ada_tools = "";
    public int $statusCode = 200;
    public array $result = [];

    public function __construct()
    {
        $this->ada_request = new AdaRequests();
        $this->ada_tools = new AdaTools();
        $this->getGateWayUrl($this->gateWayType);
        $this->__init_params();
    }

    /**
     * @param array|string $config_info 配置
     * @param string $prod_mode 模式
     * @param bool $is_object 配置是否对象
     * @return void
     */
    public static function init(array|string $config_info, string $prod_mode = "live", bool $is_object = false): void
    {

        if (empty($config_info)) {
            try {
                throw new \Exception('缺少SDK配置信息');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        if ($is_object) {
            $config_obj = $config_info;
        } else {
            if (!file_exists($config_info)) {
                try {
                    throw new \Exception('SDK配置文件不存在');
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
            $cfg_file_str = file_get_contents($config_info);
            $config_obj = json_decode($cfg_file_str, true);
        }

        $sdk_version = defined("SDK_VERSION") ? SDK_VERSION : "v1.0.0";
        self::$header['sdk_version'] = $sdk_version;
        self::$headerText['sdk_version'] = $sdk_version;
        self::$headerEmpty['sdk_version'] = $sdk_version;
        self::$isDebug = defined("DEBUG") && DEBUG;
        self::$logDir = defined("DEBUG") ? LOG : dirname(__FILE__) . "/log";
        $project_env = defined("ENV") ? ENV : "prod";
        self::init_mqtt($project_env);

        if ($prod_mode == 'live') {
            self::$api_key = $config_obj['api_key_live'] ?? '';
        }
        if ($prod_mode == 'test') {
            self::$api_key = $config_obj['api_key_test'] ?? '';
        }

        if (isset($config_obj['rsa_public_key']) && $config_obj['rsa_public_key']) {
            self::$rsaPublicKey = $config_obj['rsa_public_key'];
        }

        if (isset($config_obj['rsa_private_key']) && $config_obj['rsa_private_key']) {
            self::$rsaPrivateKey = $config_obj['rsa_private_key'];
        }
    }

    public function getGateWayUrl($type): void
    {
        $this->gateWayUrl = defined("GATE_WAY_URL") ? sprintf(GATE_WAY_URL, $type) : "https://api.adapay.tech";
    }

    public static function setApiKey($api_key): void
    {
        self::$api_key = $api_key;
    }

    public static function setRsaPublicKey($pub_key): void
    {
        self::$rsaPublicKey = $pub_key;
    }

    protected function __init_params(): void
    {
        $this->ada_tools->rsaPrivateKey = self::$rsaPrivateKey;
        $this->ada_tools->rsaPublicKey = self::$rsaPublicKey;
    }

    protected function get_request_header($req_url, $post_data, $header = [])
    {
        $header[] = 'Authorization:' . self::$api_key;
        $header[] = 'Signature:' . $this->ada_tools->generateSignature($req_url, $post_data);
        return $header;
    }

    protected function handleResult()
    {
        $json_result_data = json_decode($this->result[1], true);
        if (isset($json_result_data['data'])) {
            return json_decode($json_result_data['data'], true);
        }
        return [];
    }


    protected function do_empty_data($req_params): array
    {
        return array_filter($req_params, function ($v) {
            if (!empty($v) || $v == '0') {
                return true;
            }
            return false;
        });
    }

    public static function writeLog($message, $level = "INFO"): void
    {
        if (self::$isDebug) {
            if (!is_dir(self::$logDir)) {
                mkdir(self::$logDir, 0777, true);
            }
            $log_file = self::$logDir . "/adapay_" . date("Ymd") . ".log";
            $message_format = "[" . $level . "] [" . gmdate("Y-m-d\TH:i:s\Z") . "] " . $message . "\n";
            $fp = fopen($log_file, "a+");
            fwrite($fp, $message_format);
            fclose($fp);
        }
    }

    public static function init_mqtt($project_env): void
    {
        if (isset($project_env) && $project_env == "test") {
            self::$mqttAddress = "post-cn-459180sgc02.mqtt.aliyuncs.com:1883";
            self::$mqttGroupId = "GID_CRHS_ASYN";
            self::$mqttInstanceId = "post-cn-459180sgc02";
            self::$mqttAccessKey = "LTAILQZEm73RcxhY";
        }
    }

    public function isError(): bool
    {
        if (empty($this->result)) {
            return true;
        }
        $this->statusCode = $this->result[0];
        $resp_str = $this->result[1];
        $resp_arr = json_decode($resp_str, true);
        $resp_data = $resp_arr['data'] ?? '';
        $resp_sign = $resp_arr['signature'] ?? '';
        $resp_data_decode = json_decode($resp_data, true);
        if ($resp_sign && $this->statusCode != 401) {
            if ($this->ada_tools->verifySign($resp_sign, $resp_data)) {
                if ($this->statusCode != 200) {
                    $this->result = $resp_data_decode;
                    return true;
                } else {
                    $this->result = $resp_data_decode;
                    return false;
                }
            } else {
                $this->result = [
                    'failure_code' => 'resp_sign_verify_failed',
                    'failure_msg' => '接口结果返回签名验证失败',
                    'status' => 'failed'
                ];
                return true;
            }
        } else {
            $this->result = $resp_arr;
            return true;
        }
    }
}