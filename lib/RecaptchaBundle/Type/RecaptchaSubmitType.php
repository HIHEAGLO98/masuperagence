<?php

namespace Grafikart\RecaptchaBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecaptchaSubmitType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'recaptcha_submit';
    }

    public function getParent(): string
    {
        return TextType::class;
    }

}