<?php

namespace App\Repository\Product;

use App\Entity\Product\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginatorInterface
    ) {
        parent::__construct($registry, Product::class);
    }

    /**
     * Get all products
     *
     * @param int $page
     * @return array
     */
    public function findAllProducts(int $page): PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
            ->where('p.isVending = 1')
            ->getQuery()
            ->getResult();

        $products = $this->paginatorInterface->paginate($data, $page, 10);

        return $products;
    }

    /**
     * Get all products by category
     *
     * @param string $categoryName
     * @param int $page
     * @return PaginationInterface
     */
    public function findAllByCategory(string $categoryName, int $page): PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('p.isVending = 1', 'c.name = :categoryName')
            ->setParameter('categoryName', $categoryName)
            ->getQuery()
            ->getResult();

        $products = $this->paginatorInterface->paginate($data, $page, 10);

        return $products;
    }

    /**
     * Get last inserted products
     * @param int $page
     * @return array
     */
    public function findLastId(int $page): PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
            ->where('p.isVending = 1')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();

        $products = $this->paginatorInterface->paginate($data, $page, 10);

        return $products;
    }
}
