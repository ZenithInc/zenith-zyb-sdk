# 智游宝 SDK

智游宝接口 SDK，基于 PHP 8.0 开发。

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

// 初始化客户端
$client = new Client('your-api-key');

// TODO: 添加具体的 API 调用示例
```

## 开发

安装依赖：

```bash
composer install
```

运行测试：

```bash
composer test
```

## 许可证

MIT License
