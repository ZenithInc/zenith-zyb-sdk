<?php

namespace ZhiYouBao\SDK\Requests;

/**
 * 请求接口
 */
interface RequestInterface
{
    /**
     * 获取请求方法
     *
     * @return string HTTP 方法（GET、POST 等）
     */
    public function getMethod(): string;

    /**
     * 获取 API 名称（transactionName）
     *
     * @return string API 名称
     */
    public function getApi(): string;

    /**
     * 获取 URL 键名
     *
     * @return string URL 键名，用于在 Client 中映射到对应的 URL
     */
    public function getUrlKey(): string;

    /**
     * 获取请求参数
     *
     * @return array 请求参数数组
     */
    public function getParams(): array;

    /**
     * 获取响应类名
     *
     * @return string 响应类的完全限定类名
     */
    public function getResponseClass(): string;
}
