<?php

namespace App\Entity;

use App\Repository\TransporterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransporterRepository::class)]
class Transporter
{
      #[ORM\Id]
      #[ORM\GeneratedValue]
      #[ORM\Column]
      private ?int $id = null;

      #[ORM\Column(length: 255)]
      private ?string $title = null;

      #[ORM\Column(type: Types::TEXT)]
      private ?string $content = null;

      #[ORM\Column(type: 'decimal', scale: 2)]
      private $price;

      public function __toString(): string
      {
            return
                  $this->title . '[-br]' .
                  number_format($this->price / 100, 2, ',', ' ') . ' €';
      }

      public function getId(): ?int
      {
            return $this->id;
      }

      public function getTitle(): ?string
      {
            return $this->title;
      }

      public function setTitle(string $title): self
      {
            $this->title = $title;

            return $this;
      }

      public function getContent(): ?string
      {
            return $this->content;
      }

      public function setContent(string $content): self
      {
            $this->content = $content;

            return $this;
      }

      public function getPrice()
      {
            return $this->price;
      }

      public function setPrice($price): self
      {
            $this->price = $price;

            return $this;
      }
}
