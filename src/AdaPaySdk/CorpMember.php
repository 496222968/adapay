<?php

namespace Dwc\AdaPay\AdaPaySdk;

use Dwc\AdaPay\AdaPayCore\AdaPay;

class CorpMember extends AdaPay
{
    static private $instance;

    public string $endpoint = "/v1/corp_members";
    public $corp = NULL;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $params
     * @return void
     */
    public function create(array $params = []): void
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        ksort($request_params);
        $sign_request_params = $request_params;
        unset($sign_request_params['attach_file']);
        ksort($sign_request_params);
        $sign_str = $this->ada_tools->createLinkstring($sign_request_params);

        $header = $this->get_request_header($req_url, $sign_str, self::$headerEmpty);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header);
    }

    /**
     * @param array $params
     * @return void
     */
    public function update(array $params = []): void
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint . "/update";
        ksort($request_params);
        $sign_request_params = $request_params;
        unset($sign_request_params['attach_file']);
        ksort($sign_request_params);
        $sign_str = $this->ada_tools->createLinkstring($sign_request_params);

        $header = $this->get_request_header($req_url, $sign_str, self::$headerEmpty);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header);
    }


    /**
     * @param array $params
     * @return void
     */
    public function query(array $params = []): void
    {
        ksort($params);
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint . "/" . $params['member_id'];
        $header = $this->get_request_header($req_url, http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . "?" . http_build_query($request_params), "", $header, false);
    }
}