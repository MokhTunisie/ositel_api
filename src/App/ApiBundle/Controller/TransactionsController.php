<?php

namespace App\ApiBundle\Controller;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\ApiBundle\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionsController extends Controller
{
    /**
     * @Route("/api/monthly-transactions", name="monthly_transactions", defaults={"_api_resource_class"="App\ApiBundle\Entity\Transaction", "_api_collection_operation_name"="mounthly"})
     */
    public function monthlyTransactionsAction(Request $request): Paginator
    {
        $em = $this->getDoctrine()->getManager();

        $date = new \DateTime('now');
        $month = $request->query->get('month', $date->format('m'));
        $year = $request->query->get('year', $date->format('Y'));
        $page = $request->query->get('page', 1);
        $perPage = $this->getParameter('api_platform.collection.pagination.items_per_page');

        $date->setDate((int) $year, (int) $month, 1);

        $dateRange = $date->format('d/m/Y').' - '.$date->format('t/m/Y');

        return $em->getRepository(Transaction::class)->filterBy('createdAt', $dateRange, $request->query->get('sort', 'id'), $request->query->get('direction', 'asc'), true, $perPage, $page);
    }

    /**
     * @Route("/api/monthly-stats", name="monthly_stats", defaults={"_api_resource_class"="App\ApiBundle\Entity\Transaction"})
     */
    public function monthlyStatsAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $date = new \DateTime('now');
        $month = $request->query->get('month', $date->format('m'));
        $year = $request->query->get('year', $date->format('Y'));

        return $this->json([
            'input' => $em->getRepository(Transaction::class)->getTotalPerMonth($year, $month, 1),
            'output' => $em->getRepository(Transaction::class)->getTotalPerMonth($year, $month, 0),
        ]);
    }

    /**
     * @Route("/api/monthly-treasury", name="monthly_treasury", defaults={"_api_resource_class"="App\ApiBundle\Entity\Transaction"})
     */
    public function monthlyTreasuryAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $date = new \DateTime('now');
        $month = $request->query->get('month', $date->format('m'));
        $year = $request->query->get('year', $date->format('Y'));

        return $this->json(
            $em->getRepository(Transaction::class)->calculateTreasury($year, $month)
        );
    }
}
