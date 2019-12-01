<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="currencies")
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRepository")
 * @UniqueEntity(
 *     fields={"name","symbol"},
 *     message="entity.field.unique"
 * )
 */
class Currency
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
	 * @Assert\NotBlank(message="entity.field.not_blank")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
	 * @Assert\NotBlank(message="entity.field.not_blank")
     */
    private $symbol;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=6)
     */
    private $ratio;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" = true})
     */
    private $isAfterPos;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(?string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getRatio()
    {
        return $this->ratio;
    }

    public function setRatio($ratio): self
    {
        $this->ratio = $ratio;

        return $this;
    }

    public function getIsAfterPos(): ?bool
    {
        return $this->isAfterPos;
    }

    public function setIsAfterPos(?bool $isAfterPos): self
    {
        $this->isAfterPos = $isAfterPos;

        return $this;
    }
}
