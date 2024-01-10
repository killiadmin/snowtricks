<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewFigureType extends AbstractType
{
    /**
     * Builds the form for creating a new Figure.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array $options The form options.
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('contentFigure', TextareaType::class, [
                'label' => 'Your content',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Easy' => 'easy',
                    'Medium' => 'medium',
                    'Hard' => 'hard',
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('medias', CollectionType::class, [
                'label' => false,
                'entry_type' => MediaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [ 'label' => false ],
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__media__',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
