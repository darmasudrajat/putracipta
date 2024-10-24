<?php

namespace App\Form\Master;

use App\Entity\Master\MachinePrinting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MachinePrintingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type', ChoiceType::class, ['label' => 'Jenis', 'choices' => [
                'Cetak' => MachinePrinting::TYPE_PRINTING,
                'Diecut' => MachinePrinting::TYPE_DIECUT,
            ]])
            ->add('note')
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MachinePrinting::class,
        ]);
    }
}
