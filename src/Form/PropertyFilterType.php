<?php

namespace App\Form;

use App\Entity\PropertyType;
use App\Entity\Equipment;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'City',
                    'class' => 'form-control',
                ],
                'label' => 'City',
            ])
            ->add('minPrice', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Min price',
                    'class' => 'form-control',
                ],
                'label' => 'Min Price',
            ])
            ->add('maxPrice', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Max price',
                    'class' => 'form-control',
                ],
                'label' => 'Max Price',
            ])
            ->add('propertyType', EntityType::class, [
                'class' => PropertyType::class,
                'choices' => $options['propertyTypes'],
                'choice_label' => 'name',
                'placeholder' => 'All types',
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Property Type',
            ])
            ->add('equipment', EntityType::class, [
                'class' => Equipment::class,
                'choices' => $options['equipments'],
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'label' => 'Equipment',
            ])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'choices' => $options['services'],
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'label' => 'Services',
            ])
            ->add('search', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Search by name',
                    'class' => 'form-control',
                ],
                'label' => 'Search',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
            'propertyTypes' => [],
            'equipments' => [],
            'services' => [],
        ]);
    }
}