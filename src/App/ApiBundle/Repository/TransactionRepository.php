<?php

namespace App\ApiBundle\Repository;

use App\ApiBundle\Entity\Transaction;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Doctrine\Common\Collections\Criteria;

/**
 * TransactionRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $fieldName
     * @param $fieldValue
     * @param $sort
     * @param string $direction
     * @param        $onlyValid
     * @param        $perPage
     * @param        $page
     *
     * @return Paginator
     *
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function filterBy($fieldName, $fieldValue, $sort, $direction = 'asc', bool $onlyValid = false, int $perPage = 5, int $page = 1): Paginator
    {
        $qb = $this->createQb();
        if ($onlyValid) {
            $qb->andWhere('t.isValid = :isValid')
                ->setParameter('isValid', true);
        }
        if ($fieldName && $fieldValue) {
            if (in_array($fieldName, ['createdAt', 'updatedAt'], true)) {
                $qb->andWhere('t.'.$fieldName.' BETWEEN :begin AND :end');
                list($begin, $end) = explode(' - ', $fieldValue);
                $qb->setParameter('begin', \DateTime::createFromFormat('d/m/Y', $begin)->setTime(0, 0, 0)->format('Y-m-d H:i:s'));
                $qb->setParameter('end', \DateTime::createFromFormat('d/m/Y', $end)->setTime(23, 59, 59)->format('Y-m-d H:i:s'));
            } elseif ('tags' == $fieldName) {
                $qb->andWhere('tags.name LIKE :fieldValue');
                $qb->setParameter('fieldValue', '%'.$fieldValue.'%');
            } elseif ('category' == $fieldName) {
                $qb->andWhere('c.title LIKE :fieldValue');
                $qb->setParameter('fieldValue', '%'.$fieldValue.'%');
            } else {
                $qb->andWhere('t.'.$fieldName.' LIKE :fieldValue');
                $qb->setParameter('fieldValue', '%'.$fieldValue.'%');
            }
        }
        if (!is_null($sort)) {
            $qb->orderBy('t.'.$sort, $direction);
        }
        $firstResult = ($page - 1) * $perPage;
        $qb->groupBy('t.id');

        $criteria = Criteria::create()
            ->setFirstResult($firstResult)
            ->setMaxResults($perPage);
        $qb->addCriteria($criteria);

        $doctrinePaginator = new DoctrinePaginator($qb);
        $paginator = new Paginator($doctrinePaginator);

        return $paginator;
    }

    /**
     * @param string $year
     * @param string $month
     * @param bool   $isInput
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalPerMonth(string $year, string $month, $isInput = true): float
    {
        if (!$this->validateMonthYear($year, $month)) {
            return 0;
        }
        $date = new \DateTime($year.'-'.$month.'-01');
        $qb = $this->_em->createQueryBuilder()
            ->select('SUM(t.amount)')
            ->from(Transaction::class, 't')
            ->where('t.isValid = :isValid')
            ->setParameter('isValid', true)
            ->andWhere('t.createdAt BETWEEN :debut AND :fin')
            ->setParameter('debut', $date->format('Y-m-d 00:00:00'))
            ->setParameter('fin', $date->format('Y-m-t 23:59:59'))
            ->andWhere('t.isInput = :isInput')
            ->setParameter('isInput', $isInput);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param string $year
     * @param string $month
     *
     * @return array
     *
     * @throws \Exception
     */
    public function calculateTreasury(string $year, string $month): array
    {
        if (!$this->validateMonthYear($year, $month)) {
            return [
                'start' => 0,
                'end' => 0,
            ];
        }
        $treasuryStart = $this->calculateTreasuryStartEnd($year, $month, 'start');
        $treasuryEnd = $this->calculateTreasuryStartEnd($year, $month, 'end');

        return [
            'start' => ($treasuryStart[1]['total'] ?? 0) - ($treasuryStart[0]['total'] ?? 0),
            'end' => ($treasuryEnd[1]['total'] ?? 0) - ($treasuryEnd[0]['total'] ?? 0),
        ];
    }

    /**
     * @param string $year
     * @param string $month
     * @param string $type
     *
     * @return array
     *
     * @throws \Exception
     */
    public function calculateTreasuryStartEnd(string $year, string $month, string $type): array
    {
        $date = new \DateTime($year.'-'.$month.'-01');
        $qb = $this->_em->createQueryBuilder()
            ->select('SUM(t.amount) AS total')
            ->from(Transaction::class, 't')
            ->where('t.isValid = :isValid')
            ->setParameter('isValid', true)
            ->andWhere('t.createdAt <= :date')
            ->setParameter('date', 'start' == $type ? $date->format('Y-m-d 00:00:00') : $date->format('Y-m-t 23:59:59'))
            ->groupBy('t.isInput');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createQb()
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('t')
            ->from(Transaction::class, 't')
            ->leftJoin('t.tags', 'tags')
            ->leftJoin('t.category', 'c')
            ->where('t.id > 0');

        return $qb;
    }

    /**
     * @param string $year
     * @param string $month
     *
     * @return bool
     */
    private function validateMonthYear(string $year, string $month): bool
    {
        if ((int) $month < 1 || (int) $month > 12 || (false === strtotime($year))) {
            return false;
        }

        return true;
    }
}
