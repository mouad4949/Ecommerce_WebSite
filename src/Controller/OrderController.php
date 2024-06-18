<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Produits;
use App\Entity\Commande;
use App\Repository\ProduitsRepository;
class OrderController extends AbstractController
{
    private $orderRepository;
    private $entityManager;
    private $productRepository;
    public function __construct(CommandeRepository $orderRepository,ManagerRegistry $doctrine,ProduitsRepository $productRepository){
       $this->orderRepository=$orderRepository;
       $this->entityManager=$doctrine->getManager();
       $this->productRepository=$productRepository;
    }

    #[Route('/orders', name: 'orders_list')]
    public function index(): Response
    {

        $orders =$this->orderRepository->findBy([
            'etat' => 'ordered'
        ]);
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }
    #[Route('/user/orders', name: 'user_order_list')]
    public function userOrders(): Response
    {    // Check if user is logged in
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Get EntityManager
        $entityManager = $this->entityManager;

        // Get cart items for the current user
        $cartItems = $entityManager->getRepository(Commande::class)
            ->findBy([
                'user' => $this->getUser(),
                'etat' => 'cart'
            ]);

        return $this->render('order/user.html.twig', [
            'cartItems' => $cartItems,
        ]);

    }

   
    #[Route('/store/order/{id}', name: 'order_store')]
public function store(Produits $product,$id): Response
{      
       $product=$this->productRepository->find($id);
       if(!$this->getUser()){
        return $this->redirectToRoute('app_login');
       }
       
       $order = new Commande(); // Create a new Product object
       $order->setPname($product->getName());
       $order->setPrice($product->getPrix());
       $order->setStatut('processing...');
       $order->setUser($this->getUser());
       $this->entityManager->persist($order);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        $this->addFlash(
            'succes','Your order was saved'
        );
        return $this->redirectToRoute('user_order_list');
}
//edit statue
#[Route('/update/order/{id}/{status}', name: 'order_status_update')]
public function updateOrderStatus(Commande $order,$status,$id): Response
{   

   $order=$this->orderRepository->find($id);
   $order->setStatut($status);

   $this->entityManager->persist($order);

    // actually executes the queries (i.e. the INSERT query)
    $this->entityManager->flush();
    $this->addFlash(
        'succes','Your order statuss was updated'
    );
    return $this->redirectToRoute('orders_list');
}

// delete order
#[Route('/update/order/{id}', name: 'order_delete')]
public function deleteOrder(Commande $order,$id): Response
{   
    $order=$this->orderRepository->find($id);
    $this->entityManager->remove($order);

    // actually executes the queries (i.e. the INSERT query)
    $this->entityManager->flush();
    $this->addFlash(
        'succes','Your order was deleted'
    );
    return $this->redirectToRoute('orders_list');
}


#[Route('/cart/checkout', name: 'cart_checkout')]
    public function checkout(): Response
    {
        // Check if user is logged in
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Get EntityManager
        $entityManager = $this->entityManager;

        // Get cart items for the current user
        $cartItems = $entityManager->getRepository(Commande::class)
            ->findBy([
                'user' => $this->getUser(),
                'etat' => 'cart'
            ]);

        if (empty($cartItems)) {
            $this->addFlash('warning', 'Your cart is empty.');
            return $this->redirectToRoute('home');
        }

        // Change the status of cart items to 'ordered' when checking out
        foreach ($cartItems as $cartItem) {
            $cartItem->setEtat('ordered');
            $cartItem->setStatut('Processing...');
        }

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Checkout successful!'
        );

        return $this->redirectToRoute('user_order_list');
    }
    #[Route('/order/history', name: 'order_history')]
    public function orderHistory(): Response
    {
        // Check if user is logged in
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Get EntityManager
        $entityManager = $this->entityManager;

        // Get order history for the current user
        $orderHistory = $entityManager->getRepository(Commande::class)
            ->findBy([
                'user' => $this->getUser(),
                'etat' => 'ordered'
            ]);

        return $this->render('order/orders.html.twig', [
            'orderHistory' => $orderHistory,
        ]);
    }


}