<?php 

namespace App\App\Entity;

use App\App\Service\AdminSettingService;

/**
 * Display collections of items cards with pagination
 */
class Collection
{

    private $items = [];
    private $pagination = null;
    private $redirectPage = null;
    private $collectionItems = [];
    private $itemsPerPage;
    private $collectionPath;
    private $currentPage;
    private $allItemsCount;
    private $pages;
    private $filter = null;
    private $order = null;

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
        $this->collectionPath= $collectionPath;
        $this->currentPage = $currentPage;
        $this->allItemsCount = count($collectionItems);
        $this->pages = ceil($this->allItemsCount / $itemsPerPage);
        $this->filter = $filter;
        $this->order = $order;
        
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
                if($this->currentPage > $this->pages) $this->currentPage = $this->pages;
                if($this->currentPage <= 0) $this->currentPage = 1;
                $this->setRedirectPage($this->currentPage);
                return;
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
        $this->pagination = new Pagination($this->collectionPath, $this->pages, $this->currentPage, $this->filter);
    }

    public function getRedirectPage(): ?string
    {
        return $this->redirectPage;
    }

    /**
     * @param int|string $pageNumber 
     * @return void 
     */
    public function setRedirectPage($pageNumber): void
    {
        $this->redirectPage = (int)$pageNumber;
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

    /**
     * Tells if the page must be redirected or not
     * @return null|int NULL if no need to redirect / The Page Number to redirect to (integer) otherwise
     */
    public function redirect(): ?int
    {
        if($this->getRedirectPage() && !empty($this->collectionItems)) {
            return $this->getRedirectPage();
        } else {
            return null;
        }
        
    }

}