<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Vehicle;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use DateTime;

class BookingType extends AbstractType
{
    private string $today;

    public function __construct()
    {
        $this->today = (new DateTime('today'))->format('Y-m-d');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateTimeType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
                'attr' => [
                    'max' => $this->today
                ]
            ])
            ->add('returnDate', DateTimeType::class, [
                'label' => 'Date de retour',
                'widget' => 'single_text',
                'attr' => [
                    'max' => $this->today
                ]
            ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'registration'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
