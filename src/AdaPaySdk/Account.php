<?php

namespace Dwc\AdaPay\AdaPaySdk;


use Dwc\AdaPay\AdaPayCore\AdaPay;

class Account extends AdaPay
{
    static private $instance;

    public string $endpoint = "/v1/account";

    public function __construct()
    {
        $this->gateWayType = "page";
        parent::__construct();
    }

    /**
     * 创建钱包支付对象
     * @param array $params
     * @return void
     */
    public function payment(array $params = []): void
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint . '/payment';
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, true);
    }
}