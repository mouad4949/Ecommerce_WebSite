<?php

namespace App\Controller;

use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request; 
use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class ProductController extends AbstractController
{   private $productRepository;
    private $entityManager;
    public function __construct(ProduitsRepository $productRepository,ManagerRegistry $doctrine){
       $this->productRepository=$productRepository;
       $this->entityManager=$doctrine->getManager();
    }

    //list products
    #[Route('/product_list', name: 'product_list')]
    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(): Response
    {   $products = $this->productRepository->findAll();
        
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }


    //create
#[Route('/store/product', name: 'product_store')]
public function store(Request $request): Response
{
    $product = new Produits(); // Create a new Product object
    $form = $this->createForm(ProductType::class, $product);

    // Handle form submission if needed...
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
        
     $product=$form->getData();
     if($request->files->get('product')['image']){
        $image=$request->files->get('product')['image'];
        $image_name=time().'_'.$image->getClientOriginalName();
        $image->move($this->getParameter('image_directory'),$image_name);
        $product->setImage($image_name);
     }
       $this->entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        $this->addFlash(
            'succes','Your product was saved'
        );
        return $this->redirectToRoute('product_list');

     
    }
    return $this->renderForm('product/create.html.twig', [
        'form' => $form, // Pass the form object to the template
         // Pass the Product object to the template (optional)
    ]);
}


//show
#[Route('/product/details/{id}', name: 'product_show')]
public function show(Produits $product,$id): Response
{
    $product=$this->productRepository->find($id);
    return $this->render('product/show.html.twig', [
        'product' => $product,
    ]);
}



//edit

#[Route('/product/edit/{id}', name: 'product_edit')]
public function editProduct(Produits $product,Request $request,$id): Response
{
    
    
    $product=$this->productRepository->find($id);
    $form = $this->createForm(ProductType::class, $product);

    // Handle form submission if needed...
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
        
     $product=$form->getData();
     if($request->files->get('product')['image']){
        $image=$request->files->get('product')['image'];
        $image_name=time().'_'.$image->getClientOriginalName();
        $image->move($this->getParameter('image_directory'),$image_name);
        $product->setImage($image_name);
     }
       $this->entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        $this->addFlash(
            'succes','Your product was updated'
        );
        return $this->redirectToRoute('product_list');

     
    }
    return $this->renderForm('product/edit.html.twig', [
        'form' => $form, // Pass the form object to the template
         // Pass the Product object to the template (optional)
    ]);
}


//delete
#[Route('/product/delete/{id}', name: 'product_delete')]
public function delete(Produits $product,$id): Response
{   
    $product=$this->productRepository->find($id);
    $filesystem=new Filesystem();
    $imagePath='./uploads/'.$product->getImage();
    if($filesystem->exists($imagePath)){
        $filesystem->remove($imagePath);
    }

    $this->entityManager->remove($product);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        $this->addFlash(
            'succes',
            'Your product was removed'
        );
        
        return $this->redirectToRoute('product_list');

}

}