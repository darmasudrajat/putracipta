<?php

namespace App\Form\Master;

use App\Entity\Master\Material;
use App\Entity\Master\MaterialCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('materialCategory', EntityType::class, [
                'mapped' => false,
                'class' => MaterialCategory::class,
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->andWhere("e.id <> 1")
                            ->addOrderBy('e.name', 'ASC');
                },
            ])
            ->add('materialSubCategory', null, [
                'choice_label' => 'name',
                'choice_attr' => function($choice) {
                    return ['data-material-category' => $choice->getMaterialCategory()->getId()];
                },
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->andWhere("IDENTITY(e.materialCategory) <> 1")
                            ->addOrderBy('e.name', 'ASC');
                },
            ])
            ->add('code')
            ->add('name')
            ->add('thickness', null, ['label' => 'Ketebalan'])
            ->add('variant', null, ['label' => 'Varian'])
            ->add('density')
            ->add('viscosity', null, ['label' => 'Viskositas'])
            ->add('unit', null, [
                'choice_label' => 'name', 
                'label' => 'Satuan',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.name', 'ASC');
                },
            ])
            ->add('note')
            ->add('isInactive')
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($options) {
                $material = $event->getData();
                if ($material->getId() !== null) {
                    $materialSubCategory = $material->getMaterialSubCategory();
                    if ($materialSubCategory !== null) {
                        $event->getForm()->get('materialCategory')->setData($materialSubCategory->getMaterialCategory());
                    }
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Material::class,
        ]);
    }
}
