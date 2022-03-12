<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType;
use App\Repository\DishRepository;
use PhpParser\Node\Expr\FuncCall;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dish', name: 'dish.')]
class DishController extends AbstractController
{
    #[Route('/edit', name: 'edit')]
    public function index(DishRepository $dishRepository): Response {

        $dish = $dishRepository->findAll();

        return $this->render('dish/index.html.twig', [
            'dish' => $dish
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request){

        $dish = new Dish(); 
        $createDishForm = $this->createForm(DishType::class, $dish);
        $createDishForm->handleRequest($request);

        if($createDishForm->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $imageOfDish = $request->files->get('dish')['attachement'];

            if($imageOfDish){
                $filename = md5(uniqid()). '.'. $imageOfDish->guessExtension();
            }

            $imageOfDish->move(
                $this->getParameter('images_folder'),
                $filename
            );

            $dish->setImage($filename);
            $em->persist($dish);
            $em->flush();
            return $this->redirect($this->generateUrl('dish.edit'));
        }

        return $this->render('dish/create.html.twig', [
            'createForm' => $createDishForm->createView()
        ]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function removeDish($id, DishRepository $dishRepository){

        $em = $this->getDoctrine()->getManager();
        $dishToRemove = $dishRepository->find($id);
        $em->remove($dishToRemove);
        $em->flush();
        $this->addFlash('success','dish has been removed successfully');
        return $this->redirect($this->generateUrl('dish.edit'));
    }

    #[Route('/show/{id}', name: 'show')]
    public function showImage(Dish $dish){

        return $this->render('dish/show.html.twig', [
            'dish' => $dish
        ]);
    }
}
