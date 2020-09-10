<?php

namespace Src;

use GuzzleHttp\Client;

class ApiService
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * ApiService constructor.
     *
     * @param  \GuzzleHttp\Client  $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * 創建訂單
     * 使用 $this->client 去實作
     * 請參閱文件 『支付接口』
     *
     * 提交參數
     * amount = 10000,
     * callback_url = https://example.com
     * out_trade_no = 2014072300007148
     */
    public function createNewTransaction()
    {
        $this->client->post('https://vn.tianci2020.com/api/transaction', [
            'headers' => [
                'content-type' => 'application/json',
                'authorization' => 'Bearer InK5dzc9cDxLjJxp8AOdFqU1yv8tiIiKcJJSqQoaML2nI1nzVhQBhXSq2a9C',
            ],
            'json' => [
                'amount' => 10000,
                'callback_url' => 'https://example.com',
                'out_trade_no' => '2014072300007148',
            ],
        ]);
    }

    /**
     * 驗證傳入資料 sign 是否合法 並回傳 true or false
     * 請參閱文件 『回調說明』
     *
     * 傳入字串範例
     * "{"trade_no": "fdd49c43-e1c1-49da-9d23-8027f5412fe6","amount": "300.00","out_trade_no": "lt0085DB4AE5444B_60509043_112159","state": "completed","sign": "c2a55303f5d680937015d08446ca763b"}"
     *
     * @param  string  $jsonString
     * @return boolean
     */
    public function callbackDataVerify(string $jsonString)
    {
        $apiToken = 'InK5dzc9cDxLjJxp8AOdFqU1yv8tiIiKcJJSqQoaML2nI1nzVhQBhXSq2a9C';
        $notifyToken = 'PbB3MVLoWn6Ldw38mC9R1Q6fSKSRwQurJdipnAYPjMcmEifhwQJhotPKGK8S';

        $params = json_decode($jsonString, true);
        $sign = $params['sign'];
        unset($params['sign']);
        ksort($params);

        $preSignContent = '';
        foreach ($params as $key => $value) {
            $preSignContent .= "$key=$value&";
        }

        $preSignContent = substr($preSignContent, 0, -1);

        return md5($preSignContent.$apiToken.$notifyToken) == $sign;
    }
}