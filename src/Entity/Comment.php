<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    shortName: 'commentaire',
    operations: [
        new Get(),
        new Post(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(SearchFilter::class, properties: ['author' => 'ipartial'])]
#[ApiFilter(RangeFilter::class, properties: ['note'])]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['read', 'write'])]
    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $note = null;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Conference $conference = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['read', 'write'])]
    #[ORM\Column(length: 255)]
    private ?string $email = null;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getConference(): ?Conference
    {
        return $this->conference;
    }

    public function setConference(?Conference $conference): static
    {
        $this->conference = $conference;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    #[Groups(["read"])]
    public function getShortText(): ?string
    {
        if (strlen($this->text) < 20) {
            return $this->text;
        }
        return substr($this->text, 0, 20) . '...';
    }

    #[Groups(["read"])]
    public function getAge(): ?string
    {
        $intvl = $this->createdAt->diff(new \DateTimeImmutable());
        $age = 'Créé il y a';
        if ($intvl->days > 0) {
            $age .= ' ' . $intvl->days . ' jour' . ($intvl->days > 1 ? 's' : '');
        }
        if ($intvl->h > 0) {
            if ($intvl->days > 0) {
                if ($intvl->i == 0) {
                    $age .= ' et';
                } else {
                    $age .= ',';
                }
            }
            $age .= ' ' . $intvl->h . ' heure' . ($intvl->h > 1 ? 's' : '');
        }
        if ($intvl->i > 0) {
            if ($intvl->days + $intvl->h > 0) {
                $age .= ' et';
            }
            $age .= ' ' . $intvl->i . ' minute' . ($intvl->i > 1 ? 's' : '');
        }

        return $age;
    }


}
