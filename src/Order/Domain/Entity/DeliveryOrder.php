<?php

declare(strict_types=1);

namespace App\Order\Domain\Entity;

use App\Quote\Domain\Entity\Quote;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class DeliveryOrder
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'text')]
    private string $externalDeliveryId;

    #[ORM\Column(type: 'text')]
    private string $externalPurchaseId;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $shipmentRecipientName;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $shipmentContactPhone;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $shipmentContactEmail;

    #[ORM\Column(type: 'datetimetz_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToOne(targetEntity: Quote::class, mappedBy: 'deliveryOrder')]
    private Quote $quote;

    public function __construct(Quote $quote, string $externalDeliveryId, string $externalPurchaseId)
    {
        $this->createdAt = new \DateTimeImmutable();
        $quote->setDeliveryOrder($this);
        $this->quote = $quote;
        $this->externalDeliveryId = $externalDeliveryId;
        $this->externalPurchaseId = $externalPurchaseId;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExternalDeliveryId(): string
    {
        return $this->externalDeliveryId;
    }

    public function getQuote(): Quote
    {
        return $this->quote;
    }

    public function getShipmentRecipientName(): ?string
    {
        return $this->shipmentRecipientName;
    }

    public function setShipmentRecipientName(string $shipmentRecipientName): self
    {
        $this->shipmentRecipientName = $shipmentRecipientName;
        return $this;
    }

    public function getShipmentContactPhone(): ?string
    {
        return $this->shipmentContactPhone;
    }

    public function setShipmentContactPhone(string $shipmentContactPhone): self
    {
        $this->shipmentContactPhone = $shipmentContactPhone;
        return $this;
    }

    public function getShipmentContactEmail(): ?string
    {
        return $this->shipmentContactEmail;
    }

    public function setShipmentContactEmail(string $shipmentContactEmail): self
    {
        $this->shipmentContactEmail = $shipmentContactEmail;
        return $this;
    }

    public function getExternalPurchaseId(): string
    {
        return $this->externalPurchaseId;
    }
}
