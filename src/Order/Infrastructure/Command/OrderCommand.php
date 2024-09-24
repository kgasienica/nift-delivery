<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Command;

use App\Order\Application\Orderer\DeliveryOrderer;
use App\Order\Domain\Entity\DeliveryOrder;
use App\Order\Domain\Repository\DeliveryOrderRepository;
use App\Order\Infrastructure\Command\DTO\OrderCommandDTO;
use App\Quote\Domain\Entity\Quote;
use App\Quote\Infrastructure\Query\QuoteQuery;

final class OrderCommand
{
    private DeliveryOrderer $deliveryOrderer;
    private QuoteQuery $quoteQuery;
    private DeliveryOrderRepository $orderRepository;

    public function __construct(
        DeliveryOrderer $deliveryOrderer,
        QuoteQuery $quoteQuery,
        DeliveryOrderRepository $orderRepository
    ) {
        $this->deliveryOrderer = $deliveryOrderer;
        $this->quoteQuery = $quoteQuery;
        $this->orderRepository = $orderRepository;
    }

    public function order(OrderCommandDTO $orderCommandDTO): void
    {
        $quotes = $this->quoteQuery->query(
            $orderCommandDTO->getStreet(),
            $orderCommandDTO->getPostalCode(),
            $orderCommandDTO->getCity()
        );

        $processedLocations = [];

        foreach ($quotes as $quote) {
            if ($quote->getType() === $orderCommandDTO->getQuoteTypeEnum()) {
                if (in_array($quote->getLocation()->getId(), $processedLocations)) {
                    continue;
                }

                $externalOrderId = $this->deliveryOrderer->order($quote->getExternalId(), $orderCommandDTO);
                $this->createOrder($quote, $externalOrderId, $orderCommandDTO);

                $processedLocations[] = $quote->getLocation()->getId();
            }
        }
    }

    public function createOrder(Quote $quote, string $externalOrderId, OrderCommandDTO $orderCommandDTO): void
    {
        $order = new DeliveryOrder($quote, $externalOrderId);

        $order->setShipmentRecipientName($orderCommandDTO->getContactName());

        if ($orderCommandDTO->getContactPhone()) {
            $order->setShipmentContactPhone($orderCommandDTO->getContactPhone());
        }

        if ($orderCommandDTO->getContactEmail()) {
            $order->setShipmentContactEmail($orderCommandDTO->getContactEmail());
        }

        $this->orderRepository->save($order);
    }
}
