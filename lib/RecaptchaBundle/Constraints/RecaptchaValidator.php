<?php

namespace Grafikart\RecaptchaBundle\Constraints;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RecaptchaValidator extends ConstraintValidator
{

    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var \ReCaptcha\ReCaptcha
     */
    private $reCaptcha;

    public function __construct(RequestStack $requestStack, \ReCaptcha\ReCaptcha $reCaptcha)
    {
        $this->requestStack = $requestStack;
        $this->reCaptcha = $reCaptcha;
    }
    /**
     * @param $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        $request = $this->requestStack->getCurrentRequest();
        $recaptchaResponse = $this->requestStack->getCurrentRequest()->request->get('g-recaptcha-response');
        if (empty($recaptchaResponse)) {
            $this->addViolation($constraint);
            return;
        }
        $response = $this->reCaptcha
                    ->setExpectedHostname($request->getHost())
                    ->verify($recaptchaResponse, $request->getClientIp());

        if(!$response->isSuccess()) {
            $this->addViolation($constraint);
        }

    }

    private function addViolation(Constraint $constraint)
    {
        $this->context->buildViolation($constraint->message)->addViolation();
    }
}