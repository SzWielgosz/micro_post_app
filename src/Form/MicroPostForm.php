<?php

namespace App\Form;

use App\Entity\MicroPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MicroPostForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter your post title...',
                ]
            ])
            ->add('text', TextareaType::class, [
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Write your text here...'
                ]
            ])
            ->add('extraPrivacy')
            ->add('save', SubmitType::class, [
                'label' => 'Save',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MicroPost::class,
        ]);
    }
}
