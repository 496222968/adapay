<?php

namespace Dwc\AdaPay\AdaPaySdk;

use Dwc\AdaPay\AdaPayCore\AdaPay;

class AdapayTools extends AdaPay
{
    static private $instance;

    public string $endpoint = "/v1/bill/download";
    public string $union_endpoint = "/v1/union/user_identity";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $bill_date
     * @return void
     */
    public function download(string $bill_date): void
    {
        $params['bill_date'] = $bill_date;
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        $header = $this->get_request_header($req_url, $request_params, AdaPay::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, true);
    }

    /**
     * @param array $params
     * @return void
     */
    public function unionUserId(array $params = []): void
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->union_endpoint;
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, true);
    }

    /**
     * HTTP 验签
     * @param string $params_str
     * @param string $sign
     * @return bool
     */
    public function verifySign(string $params_str = "", string $sign = ""): bool
    {
        return $this->ada_tools->verifySign($sign, $params_str);
    }


}