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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('med_video', TextType::class, [
                'label' => 'Links videos',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => $options['uniqueIdVideo']
                ]
            ])
            ->add('med_image', FileType::class, [
                'label' => 'Pictures',
                'multiple' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => $options['uniqueIdPicture']
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'uniqueIdVideo' => null,
            'uniqueIdPicture' => null
        ]);
    }
}
