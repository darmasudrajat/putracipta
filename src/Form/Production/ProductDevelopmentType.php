<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedDateType;
use App\Entity\Production\ProductDevelopment;
use App\Entity\Production\ProductDevelopmentDetail;
use App\Entity\Production\ProductPrototype;
use App\Repository\Master\DivisionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductDevelopmentType extends AbstractType
{
    private DivisionRepository $divisionRepository;

    public function __construct(DivisionRepository $divisionRepository)
    {
        $this->divisionRepository = $divisionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('epArtworkFileDate', FormattedDateType::class)
            ->add('epCustomerReviewDate', FormattedDateType::class)
            ->add('epSubconDiecutDate', FormattedDateType::class)
            ->add('epDielineDevelopmentDate', FormattedDateType::class)
            ->add('epImageDeliveryProductionDate', FormattedDateType::class)
            ->add('epDiecutDeliveryProductionDate', FormattedDateType::class)
//            ->add('epDielineDeliveryProductionDate', FormattedDateType::class)
            ->add('fepArtworkFileDate', FormattedDateType::class)
            ->add('fepCustomerReviewDate', FormattedDateType::class)
            ->add('fepSubconDiecutDate', FormattedDateType::class)
            ->add('fepDielineDevelopmentDate', FormattedDateType::class)
            ->add('fepImageDeliveryProductionDate', FormattedDateType::class)
            ->add('fepDiecutDeliveryProductionDate', FormattedDateType::class)
//            ->add('fepDielineDeliveryProductionDate', FormattedDateType::class)
            ->add('psArtworkFileDate', FormattedDateType::class)
            ->add('psCustomerReviewDate', FormattedDateType::class)
            ->add('psSubconDiecutDate', FormattedDateType::class)
            ->add('psDielineDevelopmentDate', FormattedDateType::class)
            ->add('psImageDeliveryProductionDate', FormattedDateType::class)
            ->add('psDiecutDeliveryProductionDate', FormattedDateType::class)
//            ->add('psDielineDeliveryProductionDate', FormattedDateType::class)
            ->add('transactionDate', FormattedDateType::class)
            ->add('note')
            ->add('productPrototype', EntityHiddenType::class, ['class' => ProductPrototype::class])
            ->add('employeeDesigner', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                        ->andWhere("e.division = :division")->setParameter('division', $this->divisionRepository->findDevelopmentRecord())
                        ->andWhere("e.isInactive = false");
                },
            ])
            ->add('productDevelopmentDetails', CollectionType::class, [
                'entry_type' => ProductDevelopmentDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new ProductDevelopmentDetail(),
                'label' => false,
            ])
            ->add('transactionFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '12000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG or PDF',
                        'maxSizeMessage' => 'Please upload file size smaller than 10MB',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductDevelopment::class,
        ]);
    }
}
