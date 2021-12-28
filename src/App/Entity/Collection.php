<?php 

namespace App\App\Entity;

use App\App\Service\AdminSettingService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Display collections of items cards with pagination
 */
class Collection
{

    private $items = [];
    private $pagination = null;
    private $allItemsCount;
    private $pages;

    private $collectionItems = [];
    private $itemsPerPage;
    private $collectionPath;
    private $currentPage;
    private $filter = null;
    private $order = null;
    private $redirect = false;

    /**
     * @param array array $collectionItems The collection items before being filtered
     * @param int $itemsPerPage How many items are displayed per collection page?
     * @param string $collectionPath The collection path, without 'page' attribute
     * @param int|string $currentPage Which page of the collection is being visited right now?
     * @param string|null $filter Filter to apply to the collection. If you use this property, you also need to use the following code in the controller to apply the filter: $collectionItems = $this->productRepository->findOnFilter($orderBy, $category);
     * @return array $collection Collection. Array structure: $collection[ $items[ item1, item2,... ], $pagination[ button1, button2,... ] ]
     */
    public function __construct(array $collectionItems, int $itemsPerPage, string $collectionPath, $currentPage = 1, ?string $filter = null, ?string $order = null)
    {
        $this->collectionItems = $collectionItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->collectionPath = $collectionPath;
        $this->currentPage = $currentPage;
        $this->filter = $filter;
        $this->order = $order;
        
        $this->allItemsCount = count($collectionItems);
        $this->pages = ceil($this->allItemsCount / $itemsPerPage);
        
        $this->setItems();
        $this->setPagination();

        return $this;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(?array $items = null): void
    {
        if(!empty($items)) {
            $this->items = $items;
        } else {
            if($this->currentPage > $this->pages || $this->currentPage <= 0) {
                $this->setRedirect(true);
            }
            $offset = ((int)$this->currentPage - 1) * $this->itemsPerPage;
            foreach($this->collectionItems as $i => $item) {
                if(count($this->items) < $this->itemsPerPage && $i >= $offset) {
                    $this->addItem($item);
                }
            }
        }
    }

    private function addItem($item): void
    {
        $this->items[] = $item;
    }

    public function getPagination(): ?Pagination
    {
        return $this->pagination;
    }

    private function setPagination()
    {
        $this->pagination = new Pagination($this->collectionPath, $this->pages, $this->currentPage, $this->filter, $this->order);
    }

    public function getFilter(): ?string
    {
        return $this->filter;
    }

    public function setFilter(?string $filter = null): void
    {
        $this->filter = $filter;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function setOrder(?string $order = null): void
    {
        $this->order = $order;
    }

    public function getRedirect(): ?bool
    {
        return $this->redirect;
    }

    public function setRedirect(bool $yesNo): void
    {
        $this->redirect = $yesNo;
    }

}