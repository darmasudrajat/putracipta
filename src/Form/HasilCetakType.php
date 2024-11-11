<?php

namespace App\Form;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedDateType;
use App\Entity\Master\Supplier;
use App\Entity\Master\Customer; 

use App\Entity\HasilCetak;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


use Symfony\Component\Form\FormEvents;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;



class HasilCetakType extends AbstractType





{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        
        $builder
        // ->add('nomo', EntityType::class, [
        //     'class' => Category::class,
        //     'choice_label' => 'name',
        //     'placeholder' => 'Select a nomo',
        // ])
        //  ->add('nomo')
        
                ->add('nomo')
            ->add('tanggal', null, ['label' => 'Tanggal ', 'widget' => 'single_text'])
            
            ->add('operator', ChoiceType::class, [ 'choices' => [
                'Operator1'=> 'Operator1',
                'Operator2'=> 'Operator2',
                'Operator3'=> 'Operator3' ,
          ]])
    
        

        ->add('mesin', ButtonType::class, [
            'label' => 'Click Me',
            'attr' => [
                'onclick' => 'handleClick()',
                'class' => 'btn btn-primary',
            ],
        ])
        ->add('good', TextType::class, [
            'attr' => [
                'id' => 'good', // Assigning an ID for JavaScript access
                'onclick' => 'handleClick()', // Adding the onclick event
            ],
            'label' => 'Good',
        ])
        

        ->add('ng', TextType::class, [
            'attr' => [
                'class' => 'my-textbox', // Add a class for JavaScript targeting
                'onclick' => 'openModal()' // Inline JavaScript to call the modal
            ],
            'label' => 'NG',
        ])

        ->add('supplier', EntityHiddenType::class, ['class' => Supplier::class])



           
        //   ->add('mesin', ChoiceType::class, ['choices' => $this->entityManager->getRepository(customer::class)->findAll(),'customer' => 'company','customer'])
        //     ->add('mesin')
            // ->add('mesin', SubmitType::class, [
            //     'label' => 'mesin',
            //     'attr' => [
            //         'onclick' => 'handleClick(event)', // Adding the onclick event
            //     ],
            // ])
            //  ->add('mesin')
        // ->add('good')
        // ->add('ng')
        //      ->add('ng', 'ng', array(
        //         'label' => 'Field',
        //         'empty_data' => 'Default value'
        //    ))
        
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HasilCetak::class,
        ]);
    }
}
