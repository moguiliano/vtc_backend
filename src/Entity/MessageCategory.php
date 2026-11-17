<?php

namespace App\Entity;

use App\Enum\MessageTemplateType;
use App\Repository\MessageCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Catégorie de messages créée par l'admin (D-029) : « Rappel », « Remerciement + promo »…
 * Chaque catégorie est rangée dans un des 3 onglets de « Partager la course » (type).
 */
#[ORM\Entity(repositoryClass: MessageCategoryRepository::class)]
class MessageCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private string $nom = '';

    #[ORM\Column(length: 20)]
    #[Assert\Choice(callback: [self::class, 'typeValues'])]
    private string $type = 'client';

    #[ORM\Column]
    private int $ordre = 0;

    /** @var Collection<int, MessageTemplate> */
    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: MessageTemplate::class)]
    private Collection $modeles;

    public function __construct()
    {
        $this->modeles = new ArrayCollection();
    }

    public static function typeValues(): array
    {
        return array_map(fn(MessageTemplateType $t) => $t->value, MessageTemplateType::cases());
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }
    public function getTypeEnum(): MessageTemplateType { return MessageTemplateType::from($this->type); }

    public function getOrdre(): int { return $this->ordre; }
    public function setOrdre(int $ordre): static { $this->ordre = $ordre; return $this; }

    /** @return Collection<int, MessageTemplate> */
    public function getModeles(): Collection { return $this->modeles; }
    public function getNombreModeles(): int { return $this->modeles->count(); }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->nom, $this->getTypeEnum()->label());
    }
}
