<?php

namespace ZhiYouBao\SDK;

use GuzzleHttp\Client as HttpClient;

/**
 * 智游宝 SDK 客户端
 */
class Client
{
    private HttpClient $httpClient;
    private string $baseUrl;
    private string $apiKey;

    /**
     * @param string $apiKey API 密钥
     * @param string $baseUrl API 基础 URL
     */
    public function __construct(string $apiKey, string $baseUrl = '')
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;

        $this->httpClient = new HttpClient([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
        ]);
    }
}
