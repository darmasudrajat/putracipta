<?php

namespace App\Form;

use App\Entity\HasilCetak;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HasilCetakType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomo')
            ->add('tanggal')
            ->add('operator')
            ->add('good')
            ->add('ng')
            ->add('mesin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HasilCetak::class,
        ]);
    }
}
