<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Trait TimestampTrait
 */
trait TimestampTrait
{
    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @return null|DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @param DateTime|null $finishedAt
     */
    public function setFinishedAt(?DateTime $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
    }
}
