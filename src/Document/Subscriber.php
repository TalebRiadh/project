<?php
namespace App\Document;

use App\Enum\CanalType;
use App\Validator\UniqueEmail;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;


#[MongoDB\Document(collection: 'subscriber')]
class Subscriber
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank]
    #[Assert\Email(message: "l'email n'est pas validé")]
    #[UniqueEmail]
    private $email;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    private $nom;
    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    private $prenom;

    #[MongoDB\Field(type: "string")]
    private $tel;

    #[MongoDB\Field(type: "string")]
    #[Assert\Choice(callback: [CanalType::class, 'values'])]
    #[Assert\NotBlank(message: "le canal est obligatoire.")]
    private $canal;


    #[MongoDB\Field(type: "date")]
    private $subscribedAt;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getSubscribedAt(): ?\DateTime
    {
        return $this->subscribedAt;
    }

    public function setSubscribedAt(\DateTime $subscribedAt): self
    {
        $this->subscribedAt = $subscribedAt;
        return $this;
    }

    public function getNom()
    {
        return $this->nom;
    }


    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }


    public function getCanal(): ?CanalType
    {
        return CanalType::tryFrom($this->canal);
    }

    public function setCanal(CanalType $canal): self
    {
        $this->canal = $canal->value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel): void
    {
        $this->tel = $tel;
    }
}
