<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    /**
     * Builds a form using the FormBuilderInterface.
     *
     * This method adds a field with the name 'content_comment' to the form using the TextType class.
     *
     * @param FormBuilderInterface $builder The form builder interface used to build the form
     * @param array $options An array of options passed to the form builder
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content_comment', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
