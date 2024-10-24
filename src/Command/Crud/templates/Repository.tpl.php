<?= "<?php\n" ?>

namespace App\Repository<?php if ($vars['entityNamespace'] !== ''): ?>\<?php endif ?><?= $vars['entityNamespace'] ?>;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\<?= $vars['entityFullName'] ?>;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class <?= $vars['entityName'] ?>Repository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, <?= $vars['entityName'] ?>::class);
    }
}
