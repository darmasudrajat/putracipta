<?php

namespace App\Form\Sale;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedDateType;
use App\Entity\Master\Customer;
use App\Entity\Sale\DeliveryDetail;
use App\Entity\Sale\DeliveryHeader;
use App\Repository\Master\DivisionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryHeaderType extends AbstractType
{
    private DivisionRepository $divisionRepository;

    public function __construct(DivisionRepository $divisionRepository)
    {
        $this->divisionRepository = $divisionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', FormattedDateType::class)
            ->add('warehouse', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.name', 'ASC');
                },
            ])
//            ->add('employee', null, [
//                'choice_label' => 'name',
//                'query_builder' => function($repository) {
//                    return $repository->createQueryBuilder('e')
//                        ->andWhere("e.division = :division")->setParameter('division', $this->divisionRepository->findTransportationRecord())
//                        ->andWhere("e.isInactive = false");
//                },
//            ])
            ->add('transportation', null, [
                'choice_label' => 'nameAndPlateNumber',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('note')
            ->add('vehicleName')
            ->add('vehiclePlateNumber')
            ->add('vehicleDriverName')
            ->add('customerName', TextType::class)
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('isUsingOutsourceDelivery', ChoiceType::class, ['choices' => [
                'Internal' => false,
                'Expedisi' => true,
            ]])
            ->add('deliveryDetails', CollectionType::class, [
                'entry_type' => DeliveryDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DeliveryDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeliveryHeader::class,
        ]);
    }
}
