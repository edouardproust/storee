<?php 

namespace App\App\Component;

use RuntimeException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Display collections of items cards with pagination
 */
class Collection
{

    private $repository;
    private $itemsCriteria;

    private $currentPage;
    private $pages;

    /**
     * @param ServiceEntityRepository $repository The Repository of items you want to display
     * @param array $itemsCriteria Criteria to delimit collection items (eg; ['categoryId' => 6] or ['categorySlug' => 'slug-example']). Default: [].
     * @param UrlGeneratorInterface|null $urlGenerator Loaded by autowiring in services.yaml (don't precise this parameter in instance). Default: null.
     * @return void 
     */
    public function __construct($repository, array $itemsCriteria = [])
    {
        $this->repository = $repository;
        $this->itemsCriteria = $itemsCriteria;
    }

    /**
     * Returns an array containing both collection items and pagination data
     * 
     * @param int $itemsPerPage How many items are displayed per collection page?
     * @param string $collectionPath The collection path, without 'page' attribute
     * @param int|string $currentPage Which page of the collection is being visited right now?
     * @return array $collection Collection. Array structure: $collection[ $items[ item1, item2,... ], $pagination[ button1, button2,... ] ]
     * @throws RuntimeException 
     */
    public function build(int $itemsPerPage, string $collectionPath, $currentPage = 1, ?array $itemsOrderBy = null)
    {
        // variables
        $collection = [];
        $allItemsCount = count($this->repository->findBy($this->itemsCriteria));
        $this->pages = ceil($allItemsCount / $itemsPerPage);
        $this->currentPage = $currentPage;
        // items
        $collection['items'] = $this->getItems($itemsPerPage, $itemsOrderBy);
        // pagination
        $collection['pagination'] = $this->getPagination($collectionPath);
        // return
        return $collection;
    }

    private function getItems(int $itemsPerPage, ?array $itemsOrderBy)
    {
        if($this->currentPage > $this->pages || $this->currentPage <= 0) {
            if($this->currentPage > $this->pages) $this->currentPage = $this->pages;
            if($this->currentPage <= 0) $this->currentPage = 1;
            $collection['redirectToPage'] = $this->currentPage;
            // next 2 lines are here to prevent twig errors
            $collection['items'] = null;
            $collection['pagination'] = null;
            return $collection;
        }
        $offset = ((int)$this->currentPage - 1) * $itemsPerPage;
        return $this->repository->findBy($this->itemsCriteria, $itemsOrderBy, $itemsPerPage, $offset);
    }

    private function getPagination(string $collectionPath)
    {
        $buttons= [];
        if($this->pages > 1) {
            for($p = 1; $p <= $this->pages; $p++) {
                if((int)$this->currentPage === $p) {
                    $buttons[$p] = null;
                } else {
                    $buttons[$p] = $collectionPath . '/' . $p;
                }
            }
        }
        return $buttons;
    }

}