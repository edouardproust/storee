<?php

namespace App\Controller;

use App\Form\UserAdminType;
use App\Repository\CategoryRepository;
use App\Repository\DeliveryCountryRepository;
use App\Repository\DeliveryMethodRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    /**
     * Show list of products
     * 
     * @Route("/admin/products", name="admin_products")
     */
    public function products(ProductRepository $productRepo): Response
    {
        $products = $productRepo->findBy([], [
            'createdAt' => "DESC"
        ]);
        return $this->render('admin/products.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * Show list of categories
     * 
     * @Route("/admin/categories", name="admin_categories")
     */
    public function categories(CategoryRepository $categoryRepo): Response
    {
        $this->addFlash("success", "Test");
        $categories = $categoryRepo->findBy([], [
            'name' => 'ASC'
        ]);
        return $this->render('admin/categories.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Show list of users
     * 
     * @Route("/admin/users", name="admin_users")
     */
    public function users(UserRepository $usersRepo): Response
    {
        $users = $usersRepo->findBy([], [
            'id' => 'ASC'
        ]);
        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Modify a user on the admin side (more options than for a user to modify his own account)
     * 
     * @Route("/admin/user/{id}", name="admin_user")
     */
    public function user($id, UserRepository $userRepo, Request $request): Response
    {
        $user = $userRepo->find($id);
        if(!$user) {
            throw $this->createNotFoundException("User #$id does not exist.");
        }
        $form = $this->createForm(UserAdminType::class, $user);
        
        // set value of "is_admin" field
        $form->get('is_admin')->setData(in_array('ROLE_ADMIN', $user->getRoles()));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            // update 'roles' field
            $isAdmin = $form['is_admin']->getData();
            if($isAdmin) {
                $user->addRole('ROLE_ADMIN');
            } else {
                $user->removeRole('ROLE_ADMIN');
            }
            // push to database
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'User profile updated successfully.');
        }
        
        return $this->render('admin/user.html.twig', [
            'user' => $user,
            'userForm' =>$form->createView()
        ]);
    }

    /**
     * Show delivery options
     * 
     * @Route("/admin/users", name="admin_delivery")
     */
    public function deliveryOptions(DeliveryMethodRepository $methodRepository, DeliveryCountryRepository $countriesRepository): Response
    {
        $methods = $methodRepository->findAll();
        $countries = $countriesRepository->findBy([], [
            'name' => 'ASC'
        ]);
        return $this->render('admin/delivery.html.twig', [
            'methods' => $methods,
            'countries' => $countries
        ]);
    }

}
