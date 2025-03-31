<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\News;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a title',
                    ]),
                ],
            ])
            ->add('shortDescription', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a short description',
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter content',
                    ]),
                ],
                'attr' => ['rows' => 10],
            ])
            ->add('pictureFile', VichImageType::class, [
                'delete_label' => 'Remove Image',
                'download_label' => 'Download Image',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'image_uri' => false,
                'asset_helper' => true,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}