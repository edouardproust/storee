<?php

namespace App\Controller\CRUD;

use App\Entity\Category;
use App\Form\CategoryType;
use App\App\Entity\Collection;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\App\Service\AdminSettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{

    /** @var ProductRepository */
    private $productRepository;

    /** @var CollectionService */
    private $collectionService;

    public function __construct(
        EntityManagerInterface $em, 
        CategoryRepository $categoryRepository, 
        ProductRepository $productRepository,
        SluggerInterface $slugger,
        AdminSettingService $adminSettingService
    ){
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->slugger = $slugger;
        $this->adminSettingService = $adminSettingService;
    }

    /**
     * @Route("/category/{slug}/{page<\d+>?1}/{orderBy?}", name="category", priority=-1)
     */
    public function show($slug, $page, $orderBy, Request $request): Response
    {
        $category = $this->categoryRepository->findOneBy(['slug' => $slug]);
        if(!$category) {
            throw $this->createNotFoundException("The category does not exist.");
        }
        // collection & pagination
        $collection = new Collection(
            $this->productRepository->findForCollection($category, null, $orderBy),
            $this->adminSettingService->getValue('collectionItemsPerPage'), 
            $this->generateUrl($request->get('_route'), ['slug' => $slug]),
            $page,
            $orderBy
        );
        if($page = $collection->redirect()) {
            return $this->redirectToRoute($request->attributes->get('_route'), ['slug' => $slug, 'page' => $page]);
        }
        return $this->render('crud/category/show.html.twig', [
            'category' => $category,
            'collection' => $collection,
            'itemColWidth' => $this->adminSettingService->getBoostrapColWidth()
        ]);
    }

    /**
     * @Route("/category/create", name="category_create")
     */
    public function create(Request $request): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
            $this->em->persist($category);
            $this->em->flush();
            return $this->redirectToRoute("admin_categories");
        }
        return $this->render('crud/category/create.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     */
    public function edit($id, Request $request): Response
    {
        $category = $this->categoryRepository->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute(("admin_categories"));
        }
        return $this->render('crud/category/edit.html.twig', [
            'categoryForm' => $form->createView(),
            'category' => $category
        ]);
    }

    /**
     * @Route("/catagory/delete/{id}", name="category_delete")
     */
    public function delete($id): Response
    {
        $category = $this->categoryRepository->find($id);
        // Update category of products to "Undefined"
        $products = $category->getProducts();
        $undefinedCategory = $this->categoryRepository->findOneBy(['slug' => 'undefined']);
        foreach($products as $product) {
            $product->setCategory($undefinedCategory);
            $this->em->persist($product);
        }
        // remove category
        $this->em->remove($category);
        $this->em->flush();
        // redirect
        return $this->redirectToRoute("admin_categories");
    }

    /**
     * Show list of categories
     * 
     * @Route("/admin/categories", name="admin_categories")
     */
    public function adminList(): Response
    {
        $categories = $this->categoryRepository->findBy([], [
            'name' => 'ASC'
        ]);
        return $this->render('crud/category/admin-list.html.twig', [
            'categories' => $categories
        ]);
    }

}
