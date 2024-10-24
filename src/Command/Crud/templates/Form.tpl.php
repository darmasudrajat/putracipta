<?= "<?php\n" ?>

namespace App\Form<?php if ($vars['entityNamespace'] !== ''): ?>\<?php endif ?><?= $vars['entityNamespace'] ?>;

use App\Entity\<?= $vars['entityFullName'] ?>;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class <?= $vars['entityName'] ?>Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
<?php foreach ($vars['formFields'] as $formField): ?>
            ->add('<?= $formField ?>')
<?php endforeach ?>
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => <?= $vars['entityName'] ?>::class,
        ]);
    }
}
