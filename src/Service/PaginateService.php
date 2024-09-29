<?php

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use JetBrains\PhpStorm\ArrayShape;

class PaginateService
{

    #[ArrayShape(['data' => "array", 'pagesCount' => "float"])]
    public function getPaginated($repository, ?int $page = 1, int $limit = 5, string $alias = 'alias', $customQuery = null): array
    {
        if ($customQuery) {
            $query = $customQuery;
        } else {
            $query = $repository->createQueryBuilder($alias)->getQuery();
        }


        $paginator = new Paginator($query);
        $totalItems = count($paginator); // Общее кол-во строк
        $pagesCount = ceil($totalItems / $limit); // Общее кол-во страниц

        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return [
            'data' => $paginator,
            'pagesCount' => $pagesCount
        ];
    }

}
