<?php

namespace Dwc\AdaPay\AdaPayCore\utils;

use Dwc\AdaPay\AdaPayCore\AdaPay;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Utils\ApplicationContext;

class AdaRequests
{

    public string $postCharset = "utf-8";

    /**
     * @param $url
     * @param $postFields
     * @param $headers
     * @param $is_json
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function curl_request($url, $postFields = null, $headers = null, $is_json = false): array
    {
        $clientFactory = ApplicationContext::getContainer()->get(ClientFactory::class);
        $client = $clientFactory->create();
        if (is_array($postFields) && 0 < count($postFields)) {
            if ($headers) {
                $n_headers = [];
                foreach ($headers as $k => $header) {
                    if (!is_numeric($k)) {
                        $n_headers[$k] = $header;
                    } else {
                        $n_headers[trim(explode(':', $header)[0])] = trim(explode(':', $header)[1]);
                    }
                }
                $headers = $n_headers;
            }
            if ($is_json) {
                $json_data = json_encode($postFields);
                $headers['Content-Length'] = strlen($json_data);
                AdaPay::writeLog("请求头:" . json_encode($headers, JSON_UNESCAPED_UNICODE), "INFO");
                AdaPay::writeLog("post-json请求参数:" . json_encode($postFields, JSON_UNESCAPED_UNICODE), "INFO");
                $res = $client->post($url, ['json' => $postFields, 'headers' => $headers]);
            } else {
                AdaPay::writeLog("请求头:" . json_encode($headers, JSON_UNESCAPED_UNICODE), "INFO");
                AdaPay::writeLog("post-form请求参数:" . json_encode($postFields, JSON_UNESCAPED_UNICODE), "INFO");
                $res = $client->post($url, ['form_params' => $postFields, 'headers' => $headers]);
            }
        } else {
            if (empty($headers)) {
                $headers = ['Content-type' => 'application/x-www-form-urlencoded'];
            }
            AdaPay::writeLog("请求头:" . json_encode($headers, JSON_UNESCAPED_UNICODE), "INFO");
            $res = $client->get($url, ['headers' => $headers]);
        }
        return [$res->getStatusCode(), $res->getBody()->getContents()];
    }

    function characet($data, $targetCharset)
    {

        if (!empty($data)) {
            $fileType = $this->postCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }
}