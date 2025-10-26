<?php

/**
 * 订单查询示例（使用响应类）
 *
 * 此示例展示了如何使用响应类来处理 API 返回的数据
 * 注意：目前 Client::send() 方法仍返回数组，需要手动转换为响应对象
 * 未来可能会自动序列化为响应类
 */

require __DIR__ . '/../vendor/autoload.php';

use ZhiYouBao\SDK\Client;
use ZhiYouBao\SDK\Config;
use ZhiYouBao\SDK\Requests\QueryOrderStatusRequest;
use ZhiYouBao\SDK\Responses\QueryOrderStatusResponse;

// 初始化配置
$config = new Config(
    corpCode: getenv('ZYB_CORP_CODE') ?: 'your_corp_code',
    userName: getenv('ZYB_USERNAME') ?: 'your_username',
    privateKey: getenv('ZYB_PRIVATE_KEY') ?: 'your_private_key'
);

// 初始化客户端
$client = new Client($config);

// 创建查询订单请求
$request = new QueryOrderStatusRequest('2920250718152822883089402');

try {
    // 发送请求（目前返回数组）
    $responseData = $client->send($request);

    // 手动转换为响应对象
    $response = QueryOrderStatusResponse::fromArray($responseData);

    // 使用响应对象的方法
    echo "请求是否成功: " . ($response->isSuccess() ? '是' : '否') . "\n";
    echo "响应代码: {$response->getCode()}\n";
    echo "响应描述: {$response->getDescription()}\n";

    if ($response->isSuccess()) {
        echo "\n订单信息：\n";
        echo "订单编号: {$response->getOrderCode()}\n";
        echo "联系人: {$response->getLinkName()}\n";
        echo "联系电话: {$response->getLinkMobile()}\n";
        echo "支付状态: {$response->getPayStatus()}\n";

        echo "\n门票订单：\n";
        $ticketOrders = $response->getTicketOrders();
        foreach ($ticketOrders as $index => $ticket) {
            echo "门票 " . ($index + 1) . ":\n";
            echo "  商品名称: {$ticket['goodsName']}\n";
            echo "  数量: {$ticket['quantity']}\n";
            echo "  单价: " . ($ticket['price'] / 100) . " 元\n";
            echo "  总价: " . ($ticket['totalPrice'] / 100) . " 元\n";
            echo "  已检票: {$ticket['alreadyCheckNum']}\n";
            echo "  已退票: {$ticket['returnNum']}\n";
        }
    }

    // 也可以获取原始数据
    echo "\n原始响应数据：\n";
    print_r($response->getRawData());

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
