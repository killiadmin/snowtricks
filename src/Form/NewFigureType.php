<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('pictureFigure', FileType::class, [
                'label' => 'Pictures',
                'multiple' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => $options['uniqueIdPicture']
                ]
            ])
            ->add('videoFigure', TextType::class, [
                'label' => 'Links videos',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => $options['uniqueIdVideo'],
                ],
            ]);
            /*->add('videoFigure', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'label' => 'Links videos',
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => $options['uniqueIdVideo'],
                    ],
                ],
            ]);*/
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
            /*'uniqueId' => null,*/
            'uniqueIdVideo' => null,
            'uniqueIdPicture' => null
        ]);
    }

    /*public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }*/
}
