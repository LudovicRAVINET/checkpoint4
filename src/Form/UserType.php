<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use DateTime;

class UserType extends AbstractType
{
    private string $today;

    public function __construct()
    {
        $this->today = (new DateTime('today'))->format('Y-m-d');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true
            ])
            ->add('password', PasswordType::class)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'attr' => [
                    'max' => $this->today
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
