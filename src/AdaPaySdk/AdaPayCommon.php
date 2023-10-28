<?php

namespace Dwc\AdaPay\AdaPaySdk;


use Dwc\AdaPay\AdaPayCore\AdaPay;

class AdaPayCommon extends AdaPay
{
    /**
     * @param array $requestParams
     * @return string
     */
    function packageRequestUrl(array $requestParams = []): string
    {
        $adapayFuncCode = $requestParams["adapay_func_code"];
        if (empty($adapayFuncCode)) {
            try {
                throw new \Exception('adapay_func_code不能为空');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        $adapayApiVersion = $requestParams['adapay_api_version'] ?? 'v1';

        $this->getGateWayUrl($this->gateWayType);
        return $this->gateWayUrl . "/" . $adapayApiVersion . "/" . str_replace(".", "/", $adapayFuncCode);
    }

    /**
     * 通用请求接口 - POST - 多商户模式
     * @param array $params 请求参数
     * @param string $merchantKey 如果传了则为多商户，否则为单商户
     */
    public function requestAdapay(array $params = [], string $merchantKey = ""): void
    {
        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            $this->ada_tools->rsaPrivateKey = $merchantKey;
        }

        $request_params = $params;
        $req_url = $this->packageRequestUrl($request_params);
        $request_params = $this->format_request_params($request_params);

        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, true);
    }


    /**
     * 通用请求接口 - POST - 多商户模式
     * @param array $params
     * @param string $merchantKey
     */
    public function requestAdapayUits(array $params = [], string $merchantKey = ""): void
    {
        $this->gateWayType = "page";

        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            $this->ada_tools->rsaPrivateKey = $merchantKey;
        }

        $request_params = $params;
        $req_url = $this->packageRequestUrl($request_params);
        $request_params = $this->format_request_params($request_params);

        echo $req_url;

        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, true);
    }

    /**
     * 通用查询接口 - GET
     * @param array $params
     * @param string $merchantKey 传了则为多商户模式
     */
    public function queryAdapay(array $params = [], string $merchantKey = ""): void
    {
        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            $this->ada_tools->rsaPrivateKey = $merchantKey;
        }

        ksort($params);
        $request_params = $params;
        $req_url = $this->packageRequestUrl($request_params);
        $request_params = $this->format_request_params($request_params);

        $header = $this->get_request_header($req_url, http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . "?" . http_build_query($request_params), "", $header, false);
    }

    /**
     * @param array $params
     * @param string $merchantKey
     * @return void
     */
    public function queryAdapayUits(array $params = [], string $merchantKey = ""): void
    {
        $this->gateWayType = "page";

        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            $this->ada_tools->rsaPrivateKey = $merchantKey;
        }
        ksort($params);
        $request_params = $params;
        $req_url = $this->packageRequestUrl($request_params);
        $request_params = $this->format_request_params($request_params);

        $header = $this->get_request_header($req_url, http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . "?" . http_build_query($request_params), "", $header, false);
    }

    /**
     * @param array $arr
     * @param string $key
     * @return array
     */
    function array_remove(array $arr, string $key): array
    {
        if (!array_key_exists($key, $arr)) {
            return $arr;
        }

        $keys = array_keys($arr);
        $index = array_search($key, $keys);

        if ($index !== FALSE) {
            array_splice($arr, $index, 1);
        }

        return $arr;
    }

    /**
     * @param array $request_params
     * @return array
     */
    function format_request_params(array $request_params): array
    {
        $request_params = $this->array_remove($request_params, "adapay_func_code");
        $request_params = $this->array_remove($request_params, "adapay_api_version");
        return $this->do_empty_data($request_params);
    }
}