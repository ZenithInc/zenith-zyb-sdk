<?php

namespace ZhiYouBao\SDK\Responses;

/**
 * 查询订单状态响应
 */
class QueryOrderStatusResponse implements ResponseInterface
{
    /**
     * @param string $transactionName 交易名称
     * @param string $code 响应代码，0 表示成功
     * @param string $description 响应描述
     * @param array|null $orderResponse 订单响应数据
     * @param array $rawData 原始响应数据
     */
    public function __construct(
        private string $transactionName,
        private string $code,
        private string $description,
        private ?array $orderResponse = null,
        private array $rawData = []
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $data): static
    {
        return new self(
            transactionName: $data['transactionName'] ?? '',
            code: (string)($data['code'] ?? ''),
            description: $data['description'] ?? '',
            orderResponse: $data['orderResponse'] ?? null,
            rawData: $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->rawData;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccess(): bool
    {
        return $this->code === '0';
    }

    /**
     * 获取交易名称
     *
     * @return string
     */
    public function getTransactionName(): string
    {
        return $this->transactionName;
    }

    /**
     * 获取订单数据
     *
     * @return array|null
     */
    public function getOrder(): ?array
    {
        return $this->orderResponse['order'] ?? null;
    }

    /**
     * 获取订单编号
     *
     * @return string|null
     */
    public function getOrderCode(): ?string
    {
        return $this->getOrder()['orderCode'] ?? null;
    }

    /**
     * 获取联系人姓名
     *
     * @return string|null
     */
    public function getLinkName(): ?string
    {
        return $this->getOrder()['linkName'] ?? null;
    }

    /**
     * 获取联系人电话
     *
     * @return string|null
     */
    public function getLinkMobile(): ?string
    {
        return $this->getOrder()['linkMobile'] ?? null;
    }

    /**
     * 获取支付状态
     *
     * @return string|null
     */
    public function getPayStatus(): ?string
    {
        return $this->getOrder()['payStatus'] ?? null;
    }

    /**
     * 获取门票订单列表
     *
     * @return array
     */
    public function getTicketOrders(): array
    {
        $ticketOrders = $this->getOrder()['ticketOrders']['ticketOrder'] ?? [];

        // 如果是单个订单（关联数组），转换为数组格式
        if (!empty($ticketOrders) && !isset($ticketOrders[0])) {
            return [$ticketOrders];
        }

        return $ticketOrders;
    }

    /**
     * 获取原始响应数据
     *
     * @return array
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }
}
