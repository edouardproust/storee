<?php

namespace App\App\Service;

use App\App\Entity\Collection;
use App\App\Service\AdminSettingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CollectionService
{

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getCollectionAdminListThLink(string $label, string $orderBy, Request $request, Collection $collection): string{
        $html = '<a href="' . $this->getCollectionAdminListThUrl($request, $orderBy, $collection) . '">';
        $html .= $label . ' ' . $this->getCollectionAdminListThArrow($collection, $orderBy);
        $html .= '</a>';
        return $html;
    }

    private function getCollectionAdminListThUrl(Request $request, string $orderBy, Collection $collection): string
    {
        $route = $request->attributes->get('_route');
        $page = 1; // alway go back to page 1 after changing order criteria
        $orderBy = $orderBy;
        $order = $collection->getOrder();

        // if click on another criteria than current: order = 'asc'
        if($orderBy !== $collection->getFilter()) {
            $order = 'asc';
        } 
        // else (if click on the same criteria): reverse order
        else { 
            if(!$order || !in_array($order, ['ASC', 'asc'])) {
                $order = 'asc';
            } else {
                $order = 'desc';
            }
        }

        return $this->urlGenerator->generate($route, [
            'page' => $page,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }

    private function getCollectionAdminListThArrow(Collection $collection, $orderBy): ?string
    {
        $order = $collection->getOrder();
        if($collection->getFilter() === $orderBy) {
            if(!$order || !in_array($order, ['ASC', 'asc'])) {
                return '<i class="fas fa-caret-down">';
            }
            return '</i> <i class="fas fa-caret-up"></i>';
        }
        return null;
    }

}