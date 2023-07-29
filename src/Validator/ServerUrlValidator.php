<?php

namespace App\Validator;

use OutlineManagerClient\OutlineClient;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ServerUrlValidator extends ConstraintValidator
{
    /**
     * @param string    $value
     * @param ServerUrl $constraint
     */
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof ServerUrl) {
            throw new UnexpectedTypeException($constraint, ServerUrl::class);
        }

        $outlineClient = new OutlineClient($value);

        try {
            $outlineClient->getServer();
        } catch (\Exception) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
