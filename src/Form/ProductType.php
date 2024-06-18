<?php

namespace App\Form;
use App\Entity\Categories;
use App\Entity\Produits;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use  Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,['attr'=>['class'=>'form-control']])
            ->add('description',TextType::class,['attr'=>['class'=>'form-control']])
            ->add('prix',TextType::class,['attr'=>['class'=>'form-control']])
            ->add('couleur',TextType::class,['attr'=>['class'=>'form-control']])
            ->add('quantite',TextType::class,['attr'=>['class'=>'form-control']])
            ->add('image',FileType::class,['required'=>'false','mapped'=>false])
            ->add('categories',EntityType::class,['class'=>Categories::class],)
            ->add('Submit',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
