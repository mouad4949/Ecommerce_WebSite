<?php

namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CategoriesRepository;
use App\Entity\Categories;
class HomeController extends AbstractController
{
    private $productRepository;
    private $categoryRepository;
    private $entityManager;
    public function __construct(ProduitsRepository $productRepository,ManagerRegistry $doctrine,CategoriesRepository $categoryRepository){
       $this->productRepository=$productRepository;
       $this->categoryRepository=$categoryRepository;
       $this->entityManager=$doctrine->getManager();
    }
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $products=$this->productRepository->findAll();
        $categories =$this->categoryRepository->findAll();
        
        
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categories'=>$categories
        ]);
    }
    
    #[Route('/product/{id}', name: 'product_category')]
    public function ProductShow(Categories $category,$id): Response
        {   
 
            $category=$this->categoryRepository->find($id);
            
            $products = $category->getProduits();
            $prod= $this->productRepository->findAllProductsByCategory($category);
            $categories =$this->categoryRepository->findAll();
            return $this->render('home/index.html.twig', [
                'products' => $prod,
                'categories'=>$categories
            ]);
        }
}
