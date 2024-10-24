<?php

namespace App\Form\Sale;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Sale\SaleInvoiceHeader;
use App\Entity\Sale\SalePaymentDetail;
use App\Repository\Admin\LiteralConfigRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalePaymentDetailType extends AbstractType
{
    private LiteralConfigRepository $literalConfigRepository;
    
    public function __construct(LiteralConfigRepository $literalConfigRepository)
    {
        $this->literalConfigRepository = $literalConfigRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $serviceTaxPercentage = $this->literalConfigRepository->findLiteralValue('serviceTaxPercentage');
        $builder
            ->add('amount', FormattedNumberType::class, ['decimals' => 2])
            ->add('memo')
            ->add('serviceTaxMode', ChoiceType::class, ['choices' => [
                '0.00%' => SalePaymentDetail::SERVICE_TAX_MODE_NON_TAX,
                "{$serviceTaxPercentage}%" => SalePaymentDetail::SERVICE_TAX_MODE_TAX,
            ]])
//            ->add('serviceTaxNominal')
            ->add('isCanceled')
            ->add('account', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('saleInvoiceHeader', EntityHiddenType::class, ['class' => SaleInvoiceHeader::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SalePaymentDetail::class,
        ]);
    }
}
