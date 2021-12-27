<?php

namespace App\App\Entity;

/** 
 * In general, this Pagination is related to a Collection and instantiated by it (App\App\Entity\Collection::setPagination())
 */
class Pagination
{

    /** @var array */
    private $buttons = [];

    /**
     * @param string $collectionPath The collection path, without 'page' attribute
     * @param int $pages The total number of pages in the collection
     * @param int|string $currentPage Which page of the collection is being visited right now?
     * @return mixed 
     */
    public function __construct(string $collectionPath, int $pages, int $currentPage, ?string $orderBy)
    {
        $this->setButtons($collectionPath, $pages, $currentPage, $orderBy);
    }

    public function getButtons(): ?array
    {
        return $this->buttons;
    }

    public function setButtons(string $collectionPath, int $pages, int $currentPage, ?string $orderBy): void
    {
        if($pages > 1) {
            for($p = 1; $p <= $pages; $p++) {
                // set 'page'
                $this->buttons[$p]['page'] = $p;
                // set 'url'
                $url = null;
                if((int)$currentPage !== $p) {
                    $url = $collectionPath . '/' . $p;
                    if($orderBy) $url .= '/' . $orderBy;
                }
                $this->buttons[$p]['url'] = $url;
            }
        }
    }

}