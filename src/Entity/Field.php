<?php


namespace App\Entity;

use App\Traits\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Field
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Field
{
    use TimestampTrait;

    const HORIZONTAL = 10;
    const VERTICAL = 10;
    const BOMBS = 10;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $horizontal = self::HORIZONTAL;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $vertical = self::VERTICAL;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $bombAmount = self::BOMBS;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $data;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="fields")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHorizontal()
    {
        return $this->horizontal;
    }

    /**
     * @param string $horizontal
     */
    public function setHorizontal(string $horizontal): void
    {
        $this->horizontal = $horizontal;
    }

    /**
     * @return string
     */
    public function getVertical()
    {
        return $this->vertical;
    }

    /**
     * @param string $vertical
     */
    public function setVertical(string $vertical): void
    {
        $this->vertical = $vertical;
    }

    /**
     * @return string
     */
    public function getBombAmount()
    {
        return $this->bombAmount;
    }

    /**
     * @param string $bombAmount
     */
    public function setBombAmount(string $bombAmount): void
    {
        $this->bombAmount = $bombAmount;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}