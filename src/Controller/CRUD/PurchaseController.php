<?php

namespace App\Controller\CRUD;

use App\App\Entity\Collection;
use App\Form\PurchaseAdminType;
use App\Repository\PurchaseRepository;
use App\App\Service\AdminSettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseController extends AbstractController
{

    public function __construct(PurchaseRepository $purchaseRepository, AdminSettingService $adminSettingService, EntityManagerInterface $entityManager)
    {
        $this->purchaseRepository = $purchaseRepository;
        $this->adminSettingService = $adminSettingService;
        $this->entityManager = $entityManager;
    }

    /**
     * The order details and edition on the user side
     * 
     * @Route("/account/edit-order/{id}", name="account_edit_order")
     */
    public function userEdit($id): RedirectResponse
    {
        return $this->redirectToRoute('checkout_confirmation', [
            'id' => $id
        ]);
    }

    /**
     * The order details on the admin side
     * 
     * @Route("/order/{id}", name="purchase")
     */
    public function adminEdit($id, Request $request): Response
    {
        $purchase = $this->purchaseRepository->find($id);
        $form = $this->createForm(PurchaseAdminType::class, $purchase);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($purchase);
            $this->entityManager->flush();
            $this->addFlash('success', 'Order updated');
        }
        return $this->render('crud/purchase/show.html.twig', [
            'purchase' => $purchase,
            'purchaseForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/delete-order/{id}", name="purchase_delete")
     */
    public function delete($id): RedirectResponse
    {
        $purchase = $this->purchaseRepository->find($id);
        $this->entityManager->remove($purchase);
        $this->entityManager->flush();
        $this->addFlash('success', 'This order has been cancelled.');
        // redirect
        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_purchases');
        }
        return $this->redirectToRoute('account_show');
    }

    /**
     * Show list of purchases
     * 
     * @Route("/admin/orders/{page<\d+>?1}/{orderBy?}_{order?}", name="admin_purchases")
     */
    public function adminList($page, $orderBy, $order, Request $request): Response
    {
        $collection = new Collection(
            $this->purchaseRepository->findForCollection(null, $orderBy, $order),
            $this->adminSettingService->getValue('entitiesPerAdminListPage'),
            $this->generateUrl($request->get('_route')),
            $page,
            $orderBy ?? 'id',
            $order ?? 'desc'
        );
        if($collection->getRedirect()) return $this->redirectToRoute($request->get('_route'));
        // view
        return $this->render('crud/purchase/admin-list.html.twig', [
            'collection' => $collection
        ]);
    }

}
