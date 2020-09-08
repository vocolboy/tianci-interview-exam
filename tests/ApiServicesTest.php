<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Src\ApiService;

class ApiServicesTest extends TestCase
{
    /**
     * @var \Src\ApiService
     */
    private ApiService $apiService;

    /**
     * @var array $container
     */
    private array $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = [];
        $history = Middleware::history($this->container);

        $handlerStack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                "data" => [
                    "trade_no" => "5ec94526-18b3-43e3-966a-02bf0b7b2b37",
                    "out_trade_no" => "2014072300007148",
                    "amount" => "50000",
                    "uri" => "https://test.com/xxx",
                    "qrcode" => "",
                ],
                "success" => true,
            ])),
        ]));

        $handlerStack->push($history);

        $client = new Client(['handler' => $handlerStack]);

        $this->apiService = new ApiService($client);
    }

    public function test_api_send_success()
    {
        $this->apiService->createNewTransaction();

        $this->assertCount(1, $this->container);

        foreach ($this->container as $transaction) {
            /** @var Request $request */
            $request = $transaction['request'];
            $uri = $request->getUri();
            $this->assertEquals([
                'method' => 'POST',
                'uri' => 'https://vn.tianci2020.com/api/transactions',
                'headers' => [
                    'content-type' => 'application/json',
                    'authorization' => 'Bearer InK5dzc9cDxLjJxp8AOdFqU1yv8tiIiKcJJSqQoaML2nI1nzVhQBhXSq2a9C',
                ],
                'json' => [
                    'amount' => 10000,
                    'callback_url' => 'https://example.com',
                    'out_trade_no' => '2014072300007148',
                ],
            ], [
                'method' => $request->getMethod(),
                'uri' => sprintf('%s://%s%s', $uri->getScheme(), $uri->getHost(), $uri->getPath()),
                'headers' => [
                    'content-type' => $request->getHeader('Content-Type')[0] ?? '',
                    'authorization' => $request->getHeader('Authorization')[0] ?? '',
                ],
                'json' => json_decode($request->getBody()->getContents(), JSON_UNESCAPED_SLASHES),
            ]);
        }
    }

    /**
     * @dataProvider callbackDataProvider
     * @param  string  $data
     * @param  bool  $state
     */
    public function test_api_callback_data(string $data, bool $state)
    {
        $this->assertEquals($state, $this->apiService->callbackDataVerify($data));
    }

    public function callbackDataProvider()
    {
        return [
            [
                json_encode([
                    "trade_no" => "fdd49c43-e1c1-49da-9d23-8027f5412fe6",
                    "amount" => "300.00",
                    "out_trade_no" => "lt0085DB4AE5444B_60509043_112159",
                    "state" => "completed",
                    "sign" => "a74497ea751692f829fd139ebb39111e",
                ]),
                true,
            ],
            [
                json_encode([
                    "trade_no" => "fdd49c43-e1c1-49da-9d23-8027f5412fe5",
                    "amount" => "300.00",
                    "out_trade_no" => "lt0085DB4AE5444B_60509043_112159",
                    "state" => "completed",
                    "sign" => "a74497ea751692f829fd139ebb39111e",
                ]),
                false,
            ],
        ];
    }
}
