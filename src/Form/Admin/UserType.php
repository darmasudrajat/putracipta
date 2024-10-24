<?php

namespace App\Form\Admin;

use App\Entity\Admin\User;
use App\Util\RoleReference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    private RoleReference $roleReference;

    public function __construct(RoleReference $roleReference)
    {
        $this->roleReference = $roleReference;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => array_keys($this->roleReference->getData()),
                'choice_label' => fn($choice, $key, $value) => $value,
            ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();
            if (empty($user->getId())) {
                $form->add('username');
                $form->add('plainPassword', RepeatedType::class, [
                    'constraints' => [new NotBlank(), new Length(['min' => '1'])],
                    'mapped' => false,
                    'type' => PasswordType::class,
                    'first_options'  => ['label' => 'New Password'],
                    'second_options' => ['label' => 'Confirm Password'],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
