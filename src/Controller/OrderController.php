<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'order')]
    public function index(OrderRepository $orderRepository): Response
    {

        $orders = $orderRepository->findBy(
            ['desk' => 'deskToAssignADish']
        );
        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/ordering/{id}', name: 'ordering')]
    public function ordering(Dish $dish){

        $newOrder = new Order();
        $newOrder->setDesk("deskToAssignADish");
        $newOrder->setName($dish->getName());
        $newOrder->setOrderNr($dish->getId());
        $newOrder->setPrice($dish->getPrice());
        $newOrder->setStatus("opened");

        $em = $this->getDoctrine()->getManager();
        $em->persist($newOrder);
        $em->flush();

        $this->addFlash('order', $newOrder->getName(). ' has been added');

        return $this->redirect($this->generateUrl('menu'));
    }

    #[Route('/status/{id},{status}', name: 'status')]
    public function status($id, $status){

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find($id);

        $order->setStatus($status);
        $em->flush();

        return $this->redirect($this->generateUrl('order'));
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function removeOrder($id, OrderRepository $orderRepository){

        $em = $this->getDoctrine()->getManager();
        $orderToRemove = $orderRepository->find($id);
        $em->remove($orderToRemove);
        $em->flush();
        
        return $this->redirect($this->generateUrl('order'));
    }
}
