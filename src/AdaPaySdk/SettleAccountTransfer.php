<?php

namespace Dwc\AdaPay\AdaPaySdk;

use Dwc\AdaPay\AdaPayCore\AdaPay;

class SettleAccountTransfer extends AdaPay
{
    static private $instance;

    public string $endpoint = "/v1/settle_accounts/transfer";
    public $settle = NULL;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 创建账户转账对象
     * @param array $params
     * @return void
     */
    public function create(array $params = []): void
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, true);
    }

    /**
     * @param array $params
     * @return void
     */
    public function queryList(array $params = []): void
    {
        ksort($params);
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint . "/list";
        $header = $this->get_request_header($req_url, http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . "?" . http_build_query($request_params), "", $header, false);
    }


}