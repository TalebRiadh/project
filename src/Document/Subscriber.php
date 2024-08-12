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
    #[Assert\NotBlank(message: "Le nom et le prénom est obligatoire.")]
    private string $fullname;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(pattern: "/^(\+213|0)[1-9](\d{2}){4}$/", message: "Le numéro de téléphone n'est pas valide.")]
    private ?string $phone;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Le nom d'entreprise est obligatoire.")]
    private ?string $company;

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

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

    public function getFullname(): string
    {
        return $this->fullname;
    }


    public function setFullname(string $fullname): void
    {
        $this->fullname = $fullname;
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
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param null|string $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
}
