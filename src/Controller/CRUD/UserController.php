<?php

namespace App\Controller\CRUD;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserAdminType;
use App\App\Entity\Collection;
use App\Repository\UserRepository;
use App\App\Service\AdminSettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /** @var AuthenticationUtils */
    private $authenticationUtils;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, AdminSettingService $adminSettingService, AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $hasher)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->adminSettingService = $adminSettingService;
        $this->authenticationUtils = $authenticationUtils;
        $this->hasher = $hasher;
    }

    /**
     * @Route("/register", name="user_create")
     */
    public function create(Request $request): Response
    {
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            if($form->isValid()) {
                $user->setPassword($this->hasher->hashPassword($user, $form->get('password')->getData()));
                try {
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                } catch(UniqueConstraintViolationException $e) {
                    $this->addFlash('danger', 'An account exists with this email.');
                    return $this->redirectToRoute('user_create');
                }
                // redirect with flash
                $this->addFlash('success', 'Thank you for creating an account. You are now registered and able to log in below.');
                return $this->redirectToRoute('security_login');
            } else {
                $this->addFlash('danger', 'Your submission was not correct. Please correct errors below.');
            }
        }
        return $this->render('crud/user/create.html.twig', [
            'userForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function delete($id): Response
    {
        $user = $this->userRepository->find($id);
        // security
        if(!$user || in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('dansger', 'This user can not be deleted.');
            return $this->redirectToRoute('admin_users');
        }
        // set Purchases 'user_id' to null
        $purchases = $user->getPurchases();
        foreach($purchases as $purchase) {
            $purchase->setUser(null);
            $this->entityManager->persist($purchase);
        }
        // delete user & flush
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        // redirect
        $this->addFlash('success', 'User "'.$user->getFirstname().' '.$user->getLastname().'" has been removed');
        return $this->redirectToRoute("admin_users");
    }

    /**
     * Show list of users
     * 
     * @Route("/admin/users/{page<\d+>?1}/{orderBy?}_{order?}", name="admin_users")
     */
    public function adminList($page, $orderBy, $order, Request $request): Response
    {
        $collection = new Collection(
            $this->userRepository->findForCollection(null, $orderBy, $order),
            $this->adminSettingService->getValue('entitiesPerAdminListPage'),
            $this->generateUrl($request->get('_route')),
            $page,
            $orderBy ?? 'id',
            $order ?? 'desc'
        );
        if($collection->getRedirect()) return $this->redirectToRoute($request->get('_route'));
        return $this->render('crud/user/admin-list.html.twig', [
            'collection' => $collection
        ]);
    }

    /**
     * Modify a user on the admin side (more options than for a user to modify his own account)
     * 
     * @Route("/admin/user/{id}", name="admin_user_edit")
     */
    public function adminEdit($id, Request $request): Response
    {
        $user = $this->userRepository->find($id);
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
        
        return $this->render('crud/user/admin-edit.html.twig', [
            'user' => $user,
            'userForm' =>$form->createView()
        ]);
    }

}
