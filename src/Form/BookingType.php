<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $property = $options['property'];
        $today = new \DateTime();
        
        $builder
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'min' => $today->format('Y-m-d'),
                    'class' => 'booking-date-input',
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => $today,
                        'message' => 'The start date cannot be in the past',
                    ]),
                ],
                'label' => 'Start Date',
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'min' => $today->format('Y-m-d'),
                    'class' => 'booking-date-input',
                ],
                'constraints' => [
                    new GreaterThan([
                        'propertyPath' => 'parent.all[startDate].data',
                        'message' => 'The end date must be after the start date',
                    ]),
                    new Callback([
                        'callback' => function ($endDate, ExecutionContextInterface $context) use ($property) {
                            $form = $context->getRoot();
                            $startDate = $form->get('startDate')->getData();
                            
                            if ($startDate && $endDate) {
                                if (!$property->isAvailable($startDate, $endDate)) {
                                    $context->buildViolation('The property is not available for these dates')
                                        ->addViolation();
                                }
                            }
                        },
                    ]),
                ],
                'label' => 'End Date',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
        
        $resolver->setRequired('property');
        $resolver->setAllowedTypes('property', Property::class);
    }
}