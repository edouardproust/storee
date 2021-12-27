<?php namespace App\Controller\StaticPage;

use App\App\Service\AdminSettingService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    /** @var ProductRepository */
    private $productRepository;

    /** @var AdminSettingService */
    private $adminSettingService;

    public function __construct(ProductRepository $productRepository, AdminSettingService $adminSettingService)
    {
        $this->productRepository = $productRepository;
        $this->adminSettingService = $adminSettingService;
    }

    /**
     * @Route("/", name="home")
     */
    public function show(): Response  
    {
        $popularProductsSetting = $this->adminSettingService->getValue('homePopularProductsCriteria');
        $itemsNumber = $this->adminSettingService->getValue('homeCollectionItemsNumber');
        $test = $this->productRepository->findForCollection(null, $itemsNumber, 'purchases');
        foreach($test as $t) {
            dump($t);
        }

        return $this->render('staticPage/home.html.twig', [
            'lastProducts' => $this->productRepository->findForCollection(null, $itemsNumber, 'createdAt'),
            'popularProducts' => $this->productRepository->findForCollection(null, $itemsNumber, $popularProductsSetting),
            'itemColWidth' => $this->adminSettingService->getBoostrapColWidth()
        ]);
    }

}