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
     * @param FormBuilderInterface $builder
     * @param array $options
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
                'entry_type' => MediaType::class,
                'label' => 'Medias',
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [ 'label' => false ],
                'by_reference' => false
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
