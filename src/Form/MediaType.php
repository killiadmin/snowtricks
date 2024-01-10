<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array $options The form options.
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('med_video', TextType::class, [
                'label' => 'Link vidéo',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('med_image', FileType::class, [
                'label' => 'Pictures',
                'multiple' => false,
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
