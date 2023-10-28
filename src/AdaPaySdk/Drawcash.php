<?php

namespace Dwc\AdaPay\AdaPaySdk;


use Dwc\AdaPay\AdaPayCore\AdaPay;

class Drawcash extends AdaPay
{
    static private $instance;

    public $refundOrder = NULL;
    public $refundOrderQuery = NULL;

    public string $endpoint = "/v1/cashs";

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 创建取现对象
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
     * 查询取现对象
     * @param array $params
     * @return void
     */
    public function query(array $params = []): void
    {
        $request_params = $params;
        ksort($request_params);
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint . "/stat";
        $header = $this->get_request_header($req_url, http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . "?" . http_build_query($request_params), "", $header, false);
    }


}