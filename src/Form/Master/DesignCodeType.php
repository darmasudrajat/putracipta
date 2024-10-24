<?php

namespace App\Form\Master;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\DesignCode;
use App\Entity\Master\DesignCodeCheckSheetDetail;
use App\Entity\Master\DesignCodeDistributionDetail;
use App\Entity\Master\DesignCodeProcessDetail;
use App\Entity\Master\DesignCodeProductDetail;
use App\Entity\Master\DiecutKnife;
use App\Entity\Master\DielineMillar;
use App\Entity\Master\Paper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesignCodeType extends AbstractType
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
//            ->add('name', HiddenType::class, ['label' => 'Nama Produk'])
            ->add('code', null, ['label' => 'Kode Produk'])
            ->add('variant', null, ['label' => false])
            ->add('version', null, ['label' => 'Revisi'])
            ->add('color', null, ['label' => 'Jlh Warna'])
            ->add('pantone', null, ['label' => 'Separasi Warna'])
            ->add('colorSpecial1', null, ['label' => 'Warna Khusus 1'])
            ->add('colorSpecial2', null, ['label' => 'Warna Khusus 2'])
            ->add('colorSpecial3', null, ['label' => 'Warna Khusus 3'])
            ->add('colorSpecial4', null, ['label' => 'Warna Khusus 4'])
            ->add('coating', null, ['label' => 'Coating'])
            ->add('printingUpQuantity', null, ['label' => 'Jumlah Mata (Up/s)'])
            ->add('printingKrisSize', null, ['label' => 'Uk. Kris Cetak (cm)'])
            ->add('paperCuttingLength', FormattedNumberType::class, ['label' => false, 'decimals' => 2])
            ->add('paperCuttingWidth', FormattedNumberType::class, ['label' => false, 'decimals' => 2])
            ->add('paperMountage', null, ['label' => 'Mountage Kertas (lbr/plano)'])
            ->add('paperPlanoLength', FormattedNumberType::class, ['label' => false, 'decimals' => 2])
            ->add('paperPlanoWidth', FormattedNumberType::class, ['label' => false, 'decimals' => 2])
            ->add('inkCyanPercentage', FormattedNumberType::class, ['label' => 'Cyan (%)', 'decimals' => 2])
            ->add('inkMagentaPercentage', FormattedNumberType::class, ['label' => 'Magenta (%)', 'decimals' => 2])
            ->add('inkYellowPercentage', FormattedNumberType::class, ['label' => 'Yellow (%)', 'decimals' => 2])
            ->add('inkBlackPercentage', FormattedNumberType::class, ['label' => 'Black (%)', 'decimals' => 2])
            ->add('inkOpvPercentage', FormattedNumberType::class, ['label' => 'OPV / WB / UV (%)', 'decimals' => 2])
            ->add('inkK1Percentage', FormattedNumberType::class, ['label' => 'Khusus 1 (%)', 'decimals' => 2])
            ->add('inkK2Percentage', FormattedNumberType::class, ['label' => 'Khusus 2 (%)', 'decimals' => 2])
            ->add('inkK3Percentage', FormattedNumberType::class, ['label' => 'Khusus 3 (%)', 'decimals' => 2])
            ->add('inkK4Percentage', FormattedNumberType::class, ['label' => 'Khusus 4 (%)', 'decimals' => 2])
            ->add('packagingGlueQuantity', FormattedNumberType::class, ['label' => 'Lem (cm/pcs)', 'decimals' => 2])
            ->add('packagingRubberQuantity', FormattedNumberType::class, ['label' => 'Karet (pcs/ikat)', 'decimals' => 2])
            ->add('packagingPaperQuantity', FormattedNumberType::class, ['label' => 'Kertas Packing (pcs/pack)', 'decimals' => 2])
            ->add('packagingBoxQuantity', FormattedNumberType::class, ['label' => 'Dus (pcs/dus)', 'decimals' => 2])
            ->add('packagingTapeLargeQuantity', FormattedNumberType::class, ['label' => 'Lakban Besar (cm/pack)', 'decimals' => 2])
            ->add('packagingTapeSmallQuantity', FormattedNumberType::class, ['label' => 'Lakban Kecil (cm/pack)', 'decimals' => 2])
            ->add('packagingPlasticQuantity', FormattedNumberType::class, ['label' => 'Plastik (cm2/pack)', 'decimals' => 2])
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('diecutKnife', EntityHiddenType::class, array('class' => DiecutKnife::class))
            ->add('dielineMillar', EntityHiddenType::class, array('class' => DielineMillar::class))
            ->add('glossiness')
            ->add('emboss', ChoiceType::class, ['choices' => [
                'Tidak Ada' => 'Tidak Ada',
                'Ada' => 'Ada',
            ]])
//            ->add('diecutKnife', null, [
//                'choice_label' => 'codeNumber',
//                'label' => 'Pisau Diecut',
//                'choice_attr' => function($choice) {
//                    return ['data-customer' => $choice->getCustomer()->getId()];
//                },
//                'query_builder' => function($repository) {
//                    return $repository->createQueryBuilder('e')
//                            ->andWhere("e.isInactive = false");
//                },
//            ])
//            ->add('dielineMillar', null, [
//                'choice_label' => 'codeNumber',
//                'label' => 'Millar',
//                'choice_attr' => function($choice) {
//                    return ['data-customer' => $choice->getCustomer()->getId()];
//                },
//                'query_builder' => function($repository) {
//                    return $repository->createQueryBuilder('e')
//                            ->andWhere("e.isInactive = false");
//                },
//            ])
            ->add('status', ChoiceType::class, ['label' => 'Status', 'choices' => [
                'FA' => DesignCode::STATUS_FA,
                'NA' => DesignCode::STATUS_NA,
            ]])
            ->add('note')
            ->add('isInactive')
            ->add('hotStamping', ChoiceType::class, ['label' => 'Hot Stamping', 'choices' => [
                '' => '',
                'GOLD' => DesignCode::HOT_STAMPING_GOLD,
                'SILVER' => DesignCode::HOT_STAMPING_SILVER,
            ]])
            ->add('designCodeProcessDetails', CollectionType::class, [
                'entry_type' => DesignCodeProcessDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DesignCodeProcessDetail(),
                'label' => false,
            ])
            ->add('designCodeDistributionDetails', CollectionType::class, [
                'entry_type' => DesignCodeDistributionDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DesignCodeDistributionDetail(),
                'label' => false,
            ])
            ->add('designCodeCheckSheetDetails', CollectionType::class, [
                'entry_type' => DesignCodeCheckSheetDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DesignCodeCheckSheetDetail(),
                'label' => false,
            ])
            ->add('designCodeProductDetails', CollectionType::class, [
                'entry_type' => DesignCodeProductDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DesignCodeProductDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DesignCode::class,
        ]);
    }
}
