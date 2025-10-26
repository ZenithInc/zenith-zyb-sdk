<?php

namespace ZhiYouBao\SDK;

/**
 * SDK 配置类
 */
class Config
{
    private string $corpCode;
    private string $userName;
    private string $application;
    private string $privateKey;
    private int $maxRetries;
    private int $retryDelay;
    private float $retryMultiplier;
    private int $maxRetryDelay;
    private bool $retryOnTimeout;

    /**
     * @param string $corpCode 企业代码
     * @param string $userName 用户名
     * @param string $privateKey 私钥，用于签名
     * @param string $application 应用标识，默认为 SendCode
     * @param int $maxRetries 最大重试次数，默认为 3
     * @param int $retryDelay 初始重试延迟（毫秒），默认为 1000
     * @param float $retryMultiplier 退避乘数，默认为 2.0
     * @param int $maxRetryDelay 最大重试延迟（毫秒），默认为 30000
     * @param bool $retryOnTimeout 是否在超时时重试，默认为 true
     */
    public function __construct(
        string $corpCode,
        string $userName,
        string $privateKey,
        string $application = 'SendCode',
        int $maxRetries = 3,
        int $retryDelay = 1000,
        float $retryMultiplier = 2.0,
        int $maxRetryDelay = 30000,
        bool $retryOnTimeout = true
    ) {
        $this->corpCode = $corpCode;
        $this->userName = $userName;
        $this->privateKey = $privateKey;
        $this->application = $application;
        $this->maxRetries = $maxRetries;
        $this->retryDelay = $retryDelay;
        $this->retryMultiplier = $retryMultiplier;
        $this->maxRetryDelay = $maxRetryDelay;
        $this->retryOnTimeout = $retryOnTimeout;
    }

    /**
     * 获取企业代码
     *
     * @return string
     */
    public function getCorpCode(): string
    {
        return $this->corpCode;
    }

    /**
     * 获取用户名
     *
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * 获取应用标识
     *
     * @return string
     */
    public function getApplication(): string
    {
        return $this->application;
    }

    /**
     * 获取私钥
     *
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * 获取最大重试次数
     *
     * @return int
     */
    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    /**
     * 获取初始重试延迟（毫秒）
     *
     * @return int
     */
    public function getRetryDelay(): int
    {
        return $this->retryDelay;
    }

    /**
     * 获取退避乘数
     *
     * @return float
     */
    public function getRetryMultiplier(): float
    {
        return $this->retryMultiplier;
    }

    /**
     * 获取最大重试延迟（毫秒）
     *
     * @return int
     */
    public function getMaxRetryDelay(): int
    {
        return $this->maxRetryDelay;
    }

    /**
     * 是否在超时时重试
     *
     * @return bool
     */
    public function shouldRetryOnTimeout(): bool
    {
        return $this->retryOnTimeout;
    }
}
