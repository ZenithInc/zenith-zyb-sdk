<?php

namespace ZhiYouBao\SDK\Responses;

/**
 * 响应接口
 */
interface ResponseInterface
{
    /**
     * 从数组数据创建响应对象
     *
     * @param array $data 原始响应数据
     * @return static
     */
    public static function fromArray(array $data): static;

    /**
     * 将响应对象转换为数组
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * 获取响应代码
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * 获取响应描述信息
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * 判断请求是否成功
     *
     * @return bool
     */
    public function isSuccess(): bool;
}
