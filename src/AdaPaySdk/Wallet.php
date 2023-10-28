<?php

namespace Dwc\AdaPay\AdaPaySdk;

use Dwc\AdaPay\AdaPayCore\AdaPay;


class Wallet extends AdaPay
{
    static private $instance;

    public string $endpoint = "/v1/walletLogin";

    public function __construct()
    {
        $this->gateWayType = "page";
        parent::__construct();
    }

    /**
     * 钱包登录
     * @param array $params
     * @return void
     */
    public function login(array $params = []): void
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, true);
    }

}