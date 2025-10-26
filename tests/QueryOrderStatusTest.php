<?php

namespace ZhiYouBao\SDK\Tests;

use PHPUnit\Framework\TestCase;
use ZhiYouBao\SDK\Client;
use ZhiYouBao\SDK\Config;
use ZhiYouBao\SDK\Requests\QueryOrderStatusRequest;

/**
 * 订单状态查询测试
 */
class QueryOrderStatusTest extends TestCase
{
    private Client $client;
    private const TEST_ORDER_CODE = '2920250718152822883089402';

    /**
     * 设置测试环境
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 从环境变量读取配置
        $username = getenv('ZYB_USERNAME');
        $corpCode = getenv('ZYB_CORP_CODE');
        $privateKey = getenv('ZYB_PRIVATE_KEY');

        // 验证环境变量是否已设置
        if (empty($username) || empty($corpCode) || empty($privateKey)) {
            $this->markTestSkipped(
                '请设置环境变量：ZYB_USERNAME, ZYB_CORP_CODE, ZYB_PRIVATE_KEY'
            );
        }

        // 创建配置和客户端
        $config = new Config(
            corpCode: $corpCode,
            userName: $username,
            privateKey: $privateKey
        );

        $this->client = new Client($config);
    }

    /**
     * 测试查询订单状态
     */
    public function testQueryOrderStatus(): void
    {
        $request = new QueryOrderStatusRequest(self::TEST_ORDER_CODE);

        $response = $this->client->send($request);

        // 输出响应信息
        echo "\n\n订单查询结果：\n";
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "\n\n";

        // 基本断言
        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    /**
     * 测试查询订单状态并验证结构
     */
    public function testQueryOrderStatusWithStructure(): void
    {
        $request = new QueryOrderStatusRequest(self::TEST_ORDER_CODE);

        $response = $this->client->send($request);

        // 验证响应结构
        $this->assertIsArray($response);

        // 如果有特定的响应字段，可以在这里添加更多断言
        // 例如：
        // $this->assertArrayHasKey('transactionName', $response);
        // $this->assertArrayHasKey('header', $response);
    }
}
