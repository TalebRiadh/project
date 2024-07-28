<?php
namespace App\Document;

use App\Enum\CanalType;
use App\Validator\UniqueEmail;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;


#[MongoDB\Document(collection: 'subscriber')]
class Subscriber
{
    public function __construct(){
        $this->setSubscribedAt();
    }
     final public const CANAL_CHOICES = ["in", "out"];
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message:"L'email est obligatoire.")]
    #[Assert\Email(message: "l'email n'est pas validé")]
    #[UniqueEmail]
    private string $email;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    private string $nom;
    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    private string $prenom;

    #[MongoDB\Field(type: "string")]
    private ?string $tel;

    #[MongoDB\Field(type: "string")]
    #[Assert\Choice(choices: self::CANAL_CHOICES ,message: "le canal n'est pas valide.")]
    #[Assert\NotBlank(message: "le canal est obligatoire.")]
    private string $canal;


    #[MongoDB\Field(type: "date")]
    private \DateTime $subscribedAt;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): string
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

    public function setSubscribedAt(): self
    {
        $this->subscribedAt = new \DateTime('now');
        return $this;
    }

    public function getNom(): string
    {
        return $this->nom;
    }


    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }


    public function getCanal(): string
    {
        return $this->canal ;
    }

    public function setCanal(string $canal): self
    {
        $this->canal = $canal;
        return $this;
    }

    /**
     * @return string
     */
    public function getTel(): string
    {
        return $this->tel;
    }

    /**
     * @param null|string $tel
     */
    public function setTel(?string $tel): void
    {
        $this->tel = $tel;
    }
}
