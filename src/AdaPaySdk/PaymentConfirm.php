<?php

namespace Dwc\AdaPay\AdaPaySdk;

use Dwc\AdaPay\AdaPayCore\AdaPay;

class PaymentConfirm extends AdaPay
{
    static private $instance;

    public string $endpoint = "/v1/payments";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 创建支付确认对象
     * @param array $params
     * @return void
     */
    public function create(array $params = []): void
    {
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint . "/confirm";
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, true);
    }

    /**
     * 查询支付确认对象
     * @param array $params
     * @return void
     */
    public function query(array $params = []): void
    {
        $request_params = $params;
        $req_url = $this->gateWayUrl . $this->endpoint . "/confirm/" . $params['payment_confirm_id'];
        $header = $this->get_request_header($req_url, http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . "?" . http_build_query($request_params), "", $header, false);
    }

    /**
     * 查询支付确认对象列表
     * @param array $params
     * @return void
     */
    public function queryList(array $params = []): void
    {
        ksort($params);
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint . "/confirm/list";
        $header = $this->get_request_header($req_url, http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . "?" . http_build_query($request_params), "", $header, false);
    }
}