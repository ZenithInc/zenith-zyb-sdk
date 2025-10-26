<?php

/**
 * 重试机制配置示例
 *
 * 此示例展示了如何配置 SDK 的重试退避机制
 */

require __DIR__ . '/../vendor/autoload.php';

use ZhiYouBao\SDK\Client;
use ZhiYouBao\SDK\Config;
use ZhiYouBao\SDK\Requests\QueryOrderStatusRequest;

// 示例 1: 使用默认重试配置
echo "=== 示例 1: 使用默认重试配置 ===\n";
$configDefault = new Config(
    corpCode: getenv('ZYB_CORP_CODE') ?: 'your_corp_code',
    userName: getenv('ZYB_USERNAME') ?: 'your_username',
    privateKey: getenv('ZYB_PRIVATE_KEY') ?: 'your_private_key'
    // 默认值：
    // maxRetries: 3
    // retryDelay: 1000ms (1秒)
    // retryMultiplier: 2.0
    // maxRetryDelay: 30000ms (30秒)
    // retryOnTimeout: true
);

$clientDefault = new Client($configDefault);
echo "默认配置: 最大重试 3 次，初始延迟 1 秒，退避乘数 2.0\n\n";

// 示例 2: 自定义重试配置 - 更激进的重试
echo "=== 示例 2: 激进的重试配置 ===\n";
$configAggressive = new Config(
    corpCode: getenv('ZYB_CORP_CODE') ?: 'your_corp_code',
    userName: getenv('ZYB_USERNAME') ?: 'your_username',
    privateKey: getenv('ZYB_PRIVATE_KEY') ?: 'your_private_key',
    application: 'SendCode',
    maxRetries: 5,              // 最大重试 5 次
    retryDelay: 500,            // 初始延迟 500ms
    retryMultiplier: 1.5,       // 退避乘数 1.5
    maxRetryDelay: 10000,       // 最大延迟 10 秒
    retryOnTimeout: true        // 超时时重试
);

$clientAggressive = new Client($configAggressive);
echo "激进配置: 最大重试 5 次，初始延迟 0.5 秒，退避乘数 1.5\n";
echo "重试延迟序列: 500ms, 750ms, 1125ms, 1687ms, 2531ms\n\n";

// 示例 3: 保守的重试配置
echo "=== 示例 3: 保守的重试配置 ===\n";
$configConservative = new Config(
    corpCode: getenv('ZYB_CORP_CODE') ?: 'your_corp_code',
    userName: getenv('ZYB_USERNAME') ?: 'your_username',
    privateKey: getenv('ZYB_PRIVATE_KEY') ?: 'your_private_key',
    application: 'SendCode',
    maxRetries: 2,              // 最大重试 2 次
    retryDelay: 2000,           // 初始延迟 2 秒
    retryMultiplier: 3.0,       // 退避乘数 3.0
    maxRetryDelay: 60000,       // 最大延迟 60 秒
    retryOnTimeout: true
);

$clientConservative = new Client($configConservative);
echo "保守配置: 最大重试 2 次，初始延迟 2 秒，退避乘数 3.0\n";
echo "重试延迟序列: 2000ms, 6000ms\n\n";

// 示例 4: 禁用重试
echo "=== 示例 4: 禁用重试 ===\n";
$configNoRetry = new Config(
    corpCode: getenv('ZYB_CORP_CODE') ?: 'your_corp_code',
    userName: getenv('ZYB_USERNAME') ?: 'your_username',
    privateKey: getenv('ZYB_PRIVATE_KEY') ?: 'your_private_key',
    application: 'SendCode',
    maxRetries: 0,              // 禁用重试
    retryDelay: 0,
    retryMultiplier: 1.0,
    maxRetryDelay: 0,
    retryOnTimeout: false       // 超时时不重试
);

$clientNoRetry = new Client($configNoRetry);
echo "无重试配置: 请求失败立即返回错误\n\n";

// 实际使用示例
echo "=== 实际使用示例 ===\n";
$request = new QueryOrderStatusRequest('2920250718152822883089402');

try {
    // 使用默认配置的客户端
    $response = $clientDefault->send($request);
    echo "请求成功！\n";
    echo "订单编号: {$response['orderResponse']['order']['orderCode']}\n";
} catch (\Exception $e) {
    echo "请求失败: {$e->getMessage()}\n";
}

echo "\n=== 重试机制说明 ===\n";
echo "1. 连接错误会自动重试\n";
echo "2. 超时错误会自动重试（如果 retryOnTimeout 为 true）\n";
echo "3. 5xx 服务器错误会自动重试\n";
echo "4. 4xx 客户端错误不会重试\n";
echo "5. 重试使用指数退避算法，避免过载服务器\n";
echo "6. 每次重试会添加 ±10% 的随机抖动，避免同时重试\n";
