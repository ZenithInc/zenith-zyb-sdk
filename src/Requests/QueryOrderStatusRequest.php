<?php

namespace ZhiYouBao\SDK\Requests;

use ZhiYouBao\SDK\Responses\QueryOrderStatusResponse;

/**
 * 查询订单状态请求
 */
class QueryOrderStatusRequest implements RequestInterface
{
    private string $orderCode;

    /**
     * @param string $orderCode 订单号
     */
    public function __construct(string $orderCode)
    {
        $this->orderCode = $orderCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return 'POST';
    }

    /**
     * {@inheritdoc}
     */
    public function getApi(): string
    {
        return 'NEW_QUERY_ORDER_REQ';
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlKey(): string
    {
        return 'default';
    }

    /**
     * {@inheritdoc}
     */
    public function getParams(): array
    {
        return [
            'orderRequest' => [
                'order' => [
                    'orderCode' => $this->orderCode,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseClass(): string
    {
        return QueryOrderStatusResponse::class;
    }
}
