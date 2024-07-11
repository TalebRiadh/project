<?php
namespace App\Validator;

use App\Document\Subscriber;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ODM\MongoDB\DocumentManager;

class UniqueEmailValidator extends ConstraintValidator
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function validate($value, Constraint $constraint)
    {

        if (null === $value || '' === $value) {
            return;
        }

        // Vérifier si l'email existe déjà dans la base de données
        $existingEmail = $this->documentManager->getRepository(Subscriber::class)->findOneBy(['email' => $value]);

        if ($existingEmail) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
