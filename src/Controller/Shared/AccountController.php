<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Grid\Shared\AccountGridType;
use App\Repository\Master\AccountRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/account')]
class AccountController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_account__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, AccountRepository $accountRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'code' => SortAscending::class,
        ]);
        $form = $this->createForm(AccountGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $accounts) = $accountRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.accountCategory", 's');
            if (isset($request->request->get('account_grid')['sort']['accountCategory:name'])) {
                $add['sort']($qb, 's', 'name', $request->request->get('account_grid')['sort']['accountCategory:name']);
            }
            $qb->andWhere("{$alias}.isInactive = false");
        });

        return $this->renderForm("shared/account/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'accounts' => $accounts,
        ]);
    }
}
