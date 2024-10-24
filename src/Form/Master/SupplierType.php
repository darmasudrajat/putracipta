<?php

namespace App\Form\Master;

//use App\Entity\Master\MaterialSubCategory;
use App\Entity\Master\Supplier;
use App\Repository\Master\MaterialCategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
//use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplierType extends AbstractType
{
    private MaterialCategoryRepository $materialCategoryRepository;

    public function __construct(MaterialCategoryRepository $materialCategoryRepository)
    {
        $this->materialCategoryRepository = $materialCategoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('code')
            ->add('company')
            ->add('name', null, ['label' => 'PIC 1'])
            ->add('name2', null, ['label' => 'PIC 2'])
            ->add('name3', null, ['label' => 'PIC 3'])
            ->add('name4', null, ['label' => 'PIC 4'])
            ->add('name5', null, ['label' => 'PIC 5'])
            ->add('address', null, ['label' => 'Alamat (**Tekan ENTER untuk > 1 baris)'])
            ->add('phone')
            ->add('fax')
            ->add('email')
            ->add('taxNumber', null, ['label' => 'NPWP'])
            ->add('paymentTerm', null, ['label' => 'TOP (hari)'])
            ->add('currency', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('account', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('categoryList', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => array_map(fn($materialCategory) => $materialCategory->getName(), $this->materialCategoryRepository->findByIsInactive(false)),
                'choice_label' => fn($choice) => $choice,
            ])
            ->add('note')
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplier::class,
        ]);
    }
}
