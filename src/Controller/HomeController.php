<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(FigureRepository $figureRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'figures' => $figureRepository->selectAllFigures(),
        ]);
    }

    /**
     * @Route("/load-more", name="load_more")
     */
    public function loadMore(FigureRepository $figureRepository, Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $figures = $figureRepository->selectAllFigures($page);

        // Transform data into associative array
        $data = [];
        foreach ($figures as $figure) {
            $data[] = [
                'title' => $figure->getTitle(),
                'picture' => $figure->getPictureFigure()
            ];
        }

        return new JsonResponse($data);
    }
}
