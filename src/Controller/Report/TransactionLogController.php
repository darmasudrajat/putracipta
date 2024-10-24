<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Support\TransactionLog;
use App\Grid\Report\TransactionLogGridType;
use App\Repository\Support\TransactionLogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/transaction_log')]
class TransactionLogController extends AbstractController
{
    #[Route('/_list', name: 'app_report_transaction_log__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function _list(Request $request, TransactionLogRepository $transactionLogRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(TransactionLogGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $transactionLogs) = $transactionLogRepository->fetchData($criteria);

        return $this->renderForm("report/transaction_log/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'transactionLogs' => $transactionLogs,
        ]);
    }

    #[Route('/{id}/_info', name: 'app_report_transaction_log__info', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function _info(TransactionLog $transactionLog): Response
    {
        return $this->render("report/transaction_log/_info.html.twig", [
            'transactionLog' => $transactionLog,
            'newData' => $this->getFlattenedNewData($transactionLog->getNewData()),
        ]);
    }

    #[Route('/', name: 'app_report_transaction_log_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render("report/transaction_log/index.html.twig");
    }

    private function getFlattenedNewData($list): array
    {
        $result = [];
        $names = [];
        $this->flattenNewData($list, $result, $names);
        return $result;
    }

    private function flattenNewData(array &$src, array &$dest, array &$names): void
    {
        $curr = array_reduce($names, fn($current, $name) => $current[$name], $src);
        if (is_array($curr)) {
            foreach ($curr as $k => $v) {
                array_push($names, $k);
                $dest[implode('.', $names)] = is_array($v) ? '' : $v;
                $this->flattenNewData($src, $dest, $names);
            }
        }
        array_pop($names);
    }
}
