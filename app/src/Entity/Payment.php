<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="payments")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Customer $customer;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="payments")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Order $invoice;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getInvoice(): ?Order
    {
        return $this->invoice;
    }

    public function setInvoice(?Order $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function createdAt(): void
    {
        if (!$this->getCreatedAt() || $this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
}
