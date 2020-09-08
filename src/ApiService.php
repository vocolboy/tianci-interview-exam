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
     * 請參閱文件 『支付接口』
     *
     * 提交參數
     * amount = 10000,
     * callback_url = https://example.com
     * out_trade_no = 2014072300007148
     */
    public function createNewTransaction()
    {

    }

    /**
     * 驗證傳入資料是否合法 並回傳 true or false
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

    }
}