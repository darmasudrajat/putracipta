<?php

namespace App\Form\Master;

use App\Entity\Master\DielineMillar;
use App\Entity\Master\DielineMillarDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DielineMillarType extends AbstractType
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
            ->add('version', null, ['label' => 'Revisi'])
            ->add('quantityUpPrinting', null, ['label' => 'Jmlh Up Cetak'])
            ->add('printingLayout', null, ['label' => 'Kris Layout Cetak'])
            ->add('date', null, ['widget' => 'single_text', 'label' => 'Tanggal Pembuatan'])
            ->add('note')
            ->add('isInactive')
            ->add('dielineMillarDetails', CollectionType::class, [
                'entry_type' => DielineMillarDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DielineMillarDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DielineMillar::class,
        ]);
    }
}
