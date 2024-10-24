<?php

namespace App\Form\Master;

use App\Entity\Master\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('company')
            ->add('minimumTolerancePercentage', null, ['label' => 'Toleransi Bawah Quantity Order (%)'])
            ->add('maximumTolerancePercentage', null, ['label' => 'Toleransi Atas Quantity Order (%)'])
            ->add('name', null, ['label' => 'PIC 1'])
            ->add('name2', null, ['label' => 'PIC 2'])
            ->add('name3', null, ['label' => 'PIC 3'])
            ->add('name4', null, ['label' => 'PIC 4'])
            ->add('name5', null, ['label' => 'PIC 5'])
            ->add('name6', null, ['label' => 'PIC 6'])
            ->add('name7', null, ['label' => 'PIC 7'])
            ->add('name8', null, ['label' => 'PIC 8'])
            ->add('name9', null, ['label' => 'PIC 9'])
            ->add('name10', null, ['label' => 'PIC 10'])
            ->add('name11', null, ['label' => 'PIC 11'])
            ->add('name12', null, ['label' => 'PIC 12'])
            ->add('name13', null, ['label' => 'PIC 13'])
            ->add('name14', null, ['label' => 'PIC 14'])
            ->add('name15', null, ['label' => 'PIC 15'])
            ->add('addressInvoice', null, ['label' => 'Alamat Penagihan (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery1', null, ['label' => 'Alamat Kirim 1 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery2', null, ['label' => 'Alamat Kirim 2 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery3', null, ['label' => 'Alamat Kirim 3 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery4', null, ['label' => 'Alamat Kirim 4 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery5', null, ['label' => 'Alamat Kirim 5 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery6', null, ['label' => 'Alamat Kirim 6 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery7', null, ['label' => 'Alamat Kirim 7 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery8', null, ['label' => 'Alamat Kirim 8 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery9', null, ['label' => 'Alamat Kirim 9 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery10', null, ['label' => 'Alamat Kirim 10 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery11', null, ['label' => 'Alamat Kirim 11 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery12', null, ['label' => 'Alamat Kirim 12 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery13', null, ['label' => 'Alamat Kirim 13 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery14', null, ['label' => 'Alamat Kirim 14 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery15', null, ['label' => 'Alamat Kirim 15 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressTax1', null, ['label' => 'Alamat NPWP 1 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressTax2', null, ['label' => 'Alamat NPWP 2 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressTax3', null, ['label' => 'Alamat NPWP 3 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressTax4', null, ['label' => 'Alamat NPWP 4 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressTax5', null, ['label' => 'Alamat NPWP 5 (**Tekan ENTER untuk > 1 baris)'])
            ->add('phone')
            ->add('email')
            ->add('taxNumber', null, ['label' => 'NPWP'])
            ->add('paymentTerm', null, ['label' => 'TOP (hari)'])
            ->add('isBondedZone', null, ['label' => 'Berikat 070?'])
//            ->add('account', null, [
//                'choice_label' => 'name',
//                'query_builder' => function($repository) {
//                    return $repository->createQueryBuilder('e')
//                            ->andWhere("e.isInactive = false");
//                },
//            ])
            ->add('note')
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
