<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Order;
use App\Form\NoteType;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Services\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrdersController extends AbstractController
{
    private $orderRepository;
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Route("/orders", name="orders")
     */
    public function index(): Response
    {
        $orders = $this->orderRepository->findBy(array(), array('id' => 'DESC'));

        return $this->render('orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/orders/new", name="order_new")
     */
    public function newOrder(Request $request): Response {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            $this->addFlash('info','Submitted Successfully!');
            die($this->redirect('/orders'));
            return $this->redirect('/orders');
        }

        die(
            $this->renderView('orders/new.html.twig',[
                'form' => $form->createView()
            ])
        );

        /*return $this->render('orders/new.html.twig',[
            'form' => $form->createView()
        ]);*/
    }

    /**
     * @Route("/orders/update/{orderNumber}", name="order_update")
     */
    public function updateOrder(Request $request, $orderNumber): Response {
        $order = $this->orderRepository->findOneBy(array('order_number' => $orderNumber));
        if (!$order) {
            $this->addFlash(
                'danger',
                'Order not found'
            );

            return $this->redirectToRoute('orders');
        }

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('info','Updated Successfully!');
            return $this->redirect('/orders');
        }

        /*die(
            $this->renderView('orders/update.html.twig',[
                'form' => $form->createView(),
                'notes' => $order->getNotes(),
                'orderNumber' => $order->getOrderNumber(),
            ])
        );*/

       return $this->render('orders/update.html.twig',[
            'form' => $form->createView(),
            'notes' => $order->getNotes(),
            'orderNumber' => $order->getOrderNumber(),
        ]);
    }

    /**
     * @Route("/orders/update2/{id}", name="order_update_byid")
     */
    public function updateOrderByID(Request $request, $id): Response {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            $this->addFlash(
                'danger',
                'Order not found'
            );

            return $this->redirectToRoute('orders');
        }

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('info','Updated Successfully!');
            return $this->redirect('/orders');
        }

        return $this->render('orders/update.html.twig',[
            'form' => $form->createView(),
            'notes' => $order->getNotes(),
            'orderNumber' => $order->getOrderNumber(),
        ]);
    }

    /**
     * @Route("/orders/delete/{orderNumber}", name="order_delete")
     */
    public function deleteOrder($orderNumber): Response
    {
        $order = $this->orderRepository->findOneBy(array('order_number' => $orderNumber));
        if (!$order) {
            $this->addFlash(
                'danger',
                'Order not found'
            );

            return $this->redirectToRoute('orders');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();

        $this->addFlash(
            'info',
            'Order Deleted Succesfully!'
        );

        return $this->redirectToRoute('orders');
    }

    /**
     * @Route("/orders/note/{orderNumber}", name="order_new_note")
     */
    public function newOrderNote(Request $request, $orderNumber): Response
    {
        $order = $this->orderRepository->findOneBy(array('order_number' => $orderNumber));
        if (!$order) {
            $this->addFlash(
                'danger',
                'Order not found'
            );

            return $this->redirectToRoute('orders');
        }

        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $order->addNote($note);
            $em->flush();

            $this->addFlash('info','Submitted Successfully!');

            die($this->redirectToRoute('order_update', ['orderNumber' => $orderNumber]));

            return $this->redirectToRoute('order_update', ['orderNumber' => $orderNumber]);
        }

        return $this->render('notes/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/orders/delivery-notes/{orderNumber}", name="order_delivery_note")
     */
    public function orderDeliveryNotes(PdfGenerator $pdfGenerator, $orderNumber): Response
    {
        $order = $this->orderRepository->findOneBy(array('order_number' => $orderNumber));
        if (!$order) {
            $this->addFlash(
                'danger',
                'Order not found'
            );

            return $this->redirectToRoute('orders');
        }

        return $pdfGenerator->generateDeliveryNotes($order);
    }

    /**
     * @Route("/orders/bills/{orderNumber}", name="order_bill")
     */
    public function orderBill(PdfGenerator $pdfGenerator, $orderNumber): Response
    {
        $order = $this->orderRepository->findOneBy(array('order_number' => $orderNumber));
        if (!$order) {
            $this->addFlash(
                'danger',
                'Order not found'
            );

            return $this->redirectToRoute('orders');
        }

        return $pdfGenerator->generateBill($order);
    }
}
