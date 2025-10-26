# 智游宝 SDK

智游宝接口 SDK，基于 PHP 8.0 开发。

## 特性

- ✅ 基于 PSR-4 规范的现代化 PHP SDK
- ✅ 支持命名参数，代码更清晰易读
- ✅ 类型化响应对象，提供便捷的访问方法
- ✅ 内置智能重试退避机制，自动处理临时故障
- ✅ 指数退避算法，避免服务器过载
- ✅ 完整的单元测试覆盖
- ✅ 详细的文档和示例代码

## 环境要求

- PHP >= 8.0
- Composer

## 安装

```bash
composer require zhiyoubao/zyb-sdk
```

## 使用方法

```php
<?php

require 'vendor/autoload.php';

use ZhiYouBao\SDK\Client;
use ZhiYouBao\SDK\Config;
use ZhiYouBao\SDK\Requests\QueryOrderStatusRequest;

// 初始化配置
$config = new Config(
    corpCode: '企业代码',
    userName: '用户名',
    privateKey: '私钥'
);

// 初始化客户端
$client = new Client($config);

// 查询订单状态
$request = new QueryOrderStatusRequest('订单号');

try {
    $response = $client->send($request);
    print_r($response);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### 使用响应类（推荐）

SDK 提供了响应类来更方便地处理 API 返回的数据：

```php
<?php

use ZhiYouBao\SDK\Responses\QueryOrderStatusResponse;

// 发送请求
$request = new QueryOrderStatusRequest('订单号');
$responseData = $client->send($request);

// 转换为响应对象
$response = QueryOrderStatusResponse::fromArray($responseData);

// 使用响应对象的方法
if ($response->isSuccess()) {
    echo "订单编号: {$response->getOrderCode()}\n";
    echo "联系人: {$response->getLinkName()}\n";
    echo "联系电话: {$response->getLinkMobile()}\n";
    echo "支付状态: {$response->getPayStatus()}\n";

    // 获取门票订单列表
    $ticketOrders = $response->getTicketOrders();
    foreach ($ticketOrders as $ticket) {
        echo "商品: {$ticket['goodsName']}\n";
        echo "数量: {$ticket['quantity']}\n";
    }
} else {
    echo "错误: {$response->getDescription()}\n";
}
```

## 重试机制

SDK 内置了智能重试退避机制，可以自动处理临时网络故障和服务器错误。

### 默认配置

SDK 默认启用重试机制，配置如下：

- **maxRetries**: 3（最大重试 3 次）
- **retryDelay**: 1000ms（初始重试延迟 1 秒）
- **retryMultiplier**: 2.0（指数退避乘数）
- **maxRetryDelay**: 30000ms（最大重试延迟 30 秒）
- **retryOnTimeout**: true（超时时自动重试）

### 自定义重试配置

可以在创建 Config 对象时自定义重试参数：

```php
<?php

$config = new Config(
    corpCode: '企业代码',
    userName: '用户名',
    privateKey: '私钥',
    application: 'SendCode',
    maxRetries: 5,              // 最大重试 5 次
    retryDelay: 500,            // 初始延迟 500ms
    retryMultiplier: 1.5,       // 退避乘数 1.5
    maxRetryDelay: 10000,       // 最大延迟 10 秒
    retryOnTimeout: true        // 超时时重试
);
```

### 禁用重试

如果需要禁用重试机制：

```php
<?php

$config = new Config(
    corpCode: '企业代码',
    userName: '用户名',
    privateKey: '私钥',
    application: 'SendCode',
    maxRetries: 0               // 禁用重试
);
```

### 重试策略

SDK 会在以下情况自动重试：

1. **连接错误**：网络连接失败、DNS 解析失败等
2. **超时错误**：请求超时（需要 `retryOnTimeout` 为 `true`）
3. **服务器错误**：5xx 状态码（服务器内部错误、网关错误等）

以下情况**不会**重试：

- 4xx 客户端错误（参数错误、认证失败等）
- 业务逻辑错误（API 返回的业务错误代码）

### 指数退避算法

重试延迟使用指数退避算法计算：

```
延迟 = min(retryDelay × retryMultiplier^(重试次数-1), maxRetryDelay) ± 10% 随机抖动
```

示例（默认配置）：
- 第 1 次重试：约 1000ms
- 第 2 次重试：约 2000ms
- 第 3 次重试：约 4000ms

随机抖动（±10%）可以避免多个客户端同时重试，减轻服务器压力。

更多示例请参考 `examples/retry_configuration.php`。

## URL 配置

SDK 内置了 URL 映射表，不同的接口会自动使用对应的 URL：

- `default`: https://ifdist.zhiyoubao.com/boss/service/code.htm

每个请求类通过 `getUrlKey()` 方法指定使用的 URL key，Client 会自动映射到对应的完整 URL。

## 开发

安装依赖：

```bash
composer install
```

### 运行测试

1. 配置测试环境变量：

```bash
export ZYB_USERNAME=your_username
export ZYB_CORP_CODE=your_corp_code
export ZYB_PRIVATE_KEY=your_private_key
```

或者创建 `.env` 文件（参考 `.env.example`）并在测试前加载。

2. 运行测试：

```bash
vendor/bin/phpunit
```

或使用 composer 脚本（如果配置）：

```bash
composer test
```

## 许可证

MIT License
