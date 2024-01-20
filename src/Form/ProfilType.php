<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options for the form
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameIdentifier', TextType::class, [
                'label' => 'Lastname',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('firstnameIdentifier', TextType::class, [
                'label' => 'Firstname',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add(
                $builder->create('pictureIdentifier', FileType::class, [
                    'label' => 'Your avatar',
                    'required' => false,
                    'data_class' => null,
                    'mapped' => false,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
