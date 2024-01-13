<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\NewFigureType;
use App\Repository\FigureRepository;
use App\Service\UtilsService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends AbstractController
{
    private UtilsService $utilsService;

    public function __construct(UtilsService $utilsService)
    {
        $this->utilsService = $utilsService;
    }
    /**
     * @Route("/tricks/editing/{slug}", name="tricks_editing")
     * @throws NonUniqueResultException
     */
    public function index(Request $request, string $slug, FigureRepository $figureRepository): Response
    {
        $figure = $figureRepository->findOneBySlug($slug);

        if ($figure === null) {
            throw $this->createNotFoundException('The requested figure does not exist.');
        }

        /*$form = $this->createForm(NewFigureType::class, $figure);*/
        $form = $this->createForm(NewFigureType::class, $figure, ['display_medias' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $figure->setSlug($this->utilsService->generateSlug($figure->getTitle()));
            $this->getDoctrine()->getManager()->flush();

            // return to the figure page
            return $this->redirectToRoute('app_details', ['slug' => $figure->getSlug()]);
        }

        // render your template with form
        return $this->render('update/index.html.twig', [
            'formUpdate' => $form->createView(),
            'figure' => $figure
        ]);
    }
}
