<?php

namespace Dwc\AdaPay\AdaPaySdk;

use Dwc\AdaPay\AdaPayCore\AdaPay;

class Refund extends AdaPay
{
    static private $instance;

    public string $endpoint = "/v1/payments";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 创建退款对象
     * @param array $params
     * @return void
     */
    public function create(array $params = []): void
    {
        $request_params = $params;
        $charge_id = $params['payment_id'] ?? '';
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint . "/" . $charge_id . "/refunds";
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, true);
    }

    /**
     * 查询退款对象
     * @param array $params
     * @return void
     */
    public function query(array $params = [])
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint . "/refunds";
        $header = $this->get_request_header($req_url, http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . "?" . http_build_query($request_params), "", $header, false);
    }
}