<?php

namespace ZhiYouBao\SDK;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use ZhiYouBao\SDK\Requests\RequestInterface;

/**
 * 智游宝 SDK 客户端
 */
class Client
{
    private HttpClient $httpClient;
    private Config $config;

    /**
     * URL 映射表
     */
    private const URL_MAP = [
        'default' => 'https://ifdist.zhiyoubao.com/boss/service/code.htm',
    ];

    /**
     * @param Config $config 配置对象
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->httpClient = new HttpClient([
            'timeout' => 30,
        ]);
    }

    /**
     * 获取 URL
     *
     * @param string $key URL 键名
     * @return string URL 地址
     * @throws \InvalidArgumentException
     */
    private function getUrl(string $key): string
    {
        if (!isset(self::URL_MAP[$key])) {
            throw new \InvalidArgumentException("URL key '{$key}' not found in URL map");
        }

        return self::URL_MAP[$key];
    }

    /**
     * 发送请求
     *
     * @param RequestInterface $request 请求对象
     * @return array 响应数据
     * @throws GuzzleException
     */
    public function send(RequestInterface $request): array
    {
        return $this->sendWithRetry($request);
    }

    /**
     * 发送请求（带重试机制）
     *
     * @param RequestInterface $request 请求对象
     * @return array 响应数据
     * @throws GuzzleException
     */
    private function sendWithRetry(RequestInterface $request): array
    {
        $xmlTpl = $this->buildXml($request);
        $url = $this->getUrl($request->getUrlKey());
        $sign = $this->generateSign($xmlTpl);

        $maxRetries = $this->config->getMaxRetries();
        $attempt = 0;
        $lastException = null;

        while ($attempt <= $maxRetries) {
            try {
                if ($attempt > 0) {
                    $delay = $this->calculateDelay($attempt);
                    $this->log("重试第 {$attempt} 次，延迟 {$delay}ms");
                    usleep($delay * 1000); // 转换为微秒
                }

                $response = $this->httpClient->request(
                    $request->getMethod(),
                    $url,
                    [
                        'form_params' => [
                            'xmlMsg' => $xmlTpl,
                            'sign' => strtolower($sign),
                        ],
                    ]
                );

                return $this->parseXmlResponse($response->getBody()->getContents());

            } catch (\Exception $e) {
                $lastException = $e;

                // 判断是否应该重试
                if ($attempt >= $maxRetries || !$this->shouldRetry($e)) {
                    break;
                }

                $this->log("请求失败: {$e->getMessage()}");
                $attempt++;
            }
        }

        // 所有重试都失败，抛出最后一个异常
        if ($lastException !== null) {
            $this->log("所有重试均失败，最后错误: {$lastException->getMessage()}");
            throw $lastException;
        }

        throw new \RuntimeException('Unknown error occurred');
    }

    /**
     * 判断异常是否应该重试
     *
     * @param \Exception $exception 异常对象
     * @return bool 是否应该重试
     */
    private function shouldRetry(\Exception $exception): bool
    {
        // 连接异常应该重试
        if ($exception instanceof ConnectException) {
            return true;
        }

        // 请求异常，检查是否是超时
        if ($exception instanceof RequestException) {
            $response = $exception->getResponse();

            // 没有响应（超时等）
            if ($response === null && $this->config->shouldRetryOnTimeout()) {
                return true;
            }

            // 5xx 服务器错误应该重试
            if ($response !== null && $response->getStatusCode() >= 500) {
                return true;
            }
        }

        return false;
    }

    /**
     * 计算指数退避延迟
     *
     * @param int $attempt 当前重试次数（从1开始）
     * @return int 延迟时间（毫秒）
     */
    private function calculateDelay(int $attempt): int
    {
        $baseDelay = $this->config->getRetryDelay();
        $multiplier = $this->config->getRetryMultiplier();
        $maxDelay = $this->config->getMaxRetryDelay();

        // 指数退避：baseDelay * (multiplier ^ (attempt - 1))
        $delay = (int)($baseDelay * pow($multiplier, $attempt - 1));

        // 添加随机抖动，避免同时重试（±10%）
        $jitter = $delay * 0.1;
        $delay = $delay + rand((int)(-$jitter), (int)$jitter);

        // 确保不超过最大延迟
        return min($delay, $maxDelay);
    }

    /**
     * 记录日志
     *
     * @param string $message 日志消息
     */
    private function log(string $message): void
    {
        // 使用 error_log 记录，可以后续扩展为使用 PSR-3 Logger
        error_log("[ZhiYouBao SDK] {$message}");
    }

    /**
     * 生成签名
     *
     * @param string $xmlTpl XML 模板字符串
     * @return string 签名
     */
    private function generateSign(string $xmlTpl): string
    {
        return md5('xmlMsg=' . $xmlTpl . $this->config->getPrivateKey());
    }

    /**
     * 构建 XML 请求体
     *
     * @param RequestInterface $request 请求对象
     * @return string XML 字符串
     */
    private function buildXml(RequestInterface $request): string
    {
        $xmlTpl = '<PWBRequest>';
        $xmlTpl .= '<transactionName><![CDATA[' . $request->getApi() . ']]></transactionName>';
        $xmlTpl .= '<header>';
        $xmlTpl .= '<application><![CDATA[' . $this->config->getApplication() . ']]></application>';
        $xmlTpl .= '<requestTime><![CDATA[' . date('Y-m-d') . ']]></requestTime>';
        $xmlTpl .= '</header>';
        $xmlTpl .= '<identityInfo>';
        $xmlTpl .= '<corpCode><![CDATA[' . $this->config->getCorpCode() . ']]></corpCode>';
        $xmlTpl .= '<userName><![CDATA[' . $this->config->getUserName() . ']]></userName>';
        $xmlTpl .= '</identityInfo>';

        // 添加业务参数
        $params = $request->getParams();
        $xmlTpl .= $this->arrayToXml($params);

        $xmlTpl .= '</PWBRequest>';

        return $xmlTpl;
    }

    /**
     * 将数组转换为 XML 字符串
     *
     * @param array $data 数据数组
     * @return string XML 字符串
     */
    private function arrayToXml(array $data): string
    {
        $xml = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $xml .= '<' . $key . '>';
                $xml .= $this->arrayToXml($value);
                $xml .= '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
            }
        }
        return $xml;
    }

    /**
     * 解析 XML 响应
     *
     * @param string $xmlString XML 字符串
     * @return array 解析后的数据
     */
    private function parseXmlResponse(string $xmlString): array
    {
        $xml = simplexml_load_string($xmlString);
        return json_decode(json_encode($xml), true);
    }
}
