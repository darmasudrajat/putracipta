<?= "<?php\n" ?>

namespace App\Grid<?php if ($vars['entityNamespace'] !== ''): ?>\<?php endif ?><?= $vars['entityNamespace'] ?>;

use App\Common\Data\Criteria\DataCriteria;
<?php if (in_array(true, $vars['dataFieldSearchableMap'], true)): ?>
use App\Common\Data\Operator\FilterContain;
<?php endif ?>
<?php if (in_array(false, $vars['dataFieldSearchableMap'], true)): ?>
use App\Common\Data\Operator\FilterEqual;
<?php endif ?>
<?php if (in_array(true, $vars['dataFieldSearchableMap'], true)): ?>
use App\Common\Data\Operator\FilterNotContain;
<?php endif ?>
<?php if (in_array(false, $vars['dataFieldSearchableMap'], true)): ?>
use App\Common\Data\Operator\FilterNotEqual;
<?php endif ?>
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class <?= $vars['entityName'] ?>GridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [<?= implode(', ', array_map(fn($name) => "'{$name}'", $vars['dataFieldNames'])) ?>],
                'field_operators_list' => [
<?php foreach ($vars['dataFieldNames'] as $dataFieldName): ?>
<?php if ($vars['dataFieldSearchableMap'][$dataFieldName]): ?>
                    '<?= $dataFieldName ?>' => [FilterContain::class, FilterNotContain::class],
<?php else: ?>
                    '<?= $dataFieldName ?>' => [FilterEqual::class, FilterNotEqual::class],
<?php endif ?>
<?php endforeach ?>
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [<?= implode(', ', array_map(fn($name) => "'{$name}'", $vars['dataFieldNames'])) ?>],
                'field_operators_list' => [
<?php foreach ($vars['dataFieldNames'] as $dataFieldName): ?>
                    '<?= $dataFieldName ?>' => [SortAscending::class, SortDescending::class],
<?php endforeach ?>
                ],
            ])
            ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DataCriteria::class,
            'csrf_protection' => false,
        ]);
    }
}
