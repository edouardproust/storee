<?php

namespace App\Controller\CRUD;

use App\Form\UserAdminType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/register", name="user_create")
     */
    public function create(): Response
    {
        return $this->render('crud/user/create.html.twig');
    }

    /**
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function delete($id): Response
    {
        $user = $this->userRepository->find($id);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->redirectToRoute("admin_products");
        return $this->redirectToRoute("home");
    }

    /**
     * Show list of users
     * 
     * @Route("/admin/users", name="admin_users")
     */
    public function adminList(): Response
    {
        $users = $this->userRepository->findBy([], [
            'id' => 'ASC'
        ]);
        return $this->render('crud/user/admin-list.html.twig', [
            'users' => $users
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
