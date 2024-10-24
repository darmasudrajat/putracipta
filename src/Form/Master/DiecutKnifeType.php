<?php

namespace App\Form\Master;

use App\Entity\Master\DiecutKnife;
use App\Entity\Master\DiecutKnifeDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiecutKnifeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', null, [
                'choice_label' => 'idNameLiteral',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.company', 'ASC');
                },
            ])
//            ->add('name', null, ['label' => 'Nama Produk'])
            ->add('version', null, ['label' => 'Revisi'])
//            ->add('code', null, ['label' => 'Kode'])
            ->add('upPerSecondKnife', null, ['label' => 'Up/s Pisau'])
            ->add('upPerSecondPrint', null, ['label' => 'Up/s Cetak'])
            ->add('printingSize', null, ['label' => 'Uk. Kris Cetak (cm)'])
//            ->add('product', EntityHiddenType::class, ['class' => Product::class])
            ->add('location', ChoiceType::class, ['label' => 'Location', 'choices' => [
                'BOBST' => DiecutKnife::LOCATION_BOBST,
                'PON' => DiecutKnife::LOCATION_PON,
            ]])
            ->add('note')
            ->add('isInactive')
            ->add('date', null, ['widget' => 'single_text', 'label' => 'Tanggal Pembuatan'])
            ->add('diecutKnifeDetails', CollectionType::class, [
                'entry_type' => DiecutKnifeDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DiecutKnifeDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DiecutKnife::class,
        ]);
    }
}
