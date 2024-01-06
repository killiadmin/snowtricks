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
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/load-more", name="load_more")
     */
    public function loadMore(FigureRepository $figureRepository, Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = 15;
        $figures = $figureRepository->selectAllFigures($page, $limit);

        // Transform data into associative array
        $data = [];
        foreach ($figures as $figure) {
            $data[] = [
                'title' => $figure->getTitle(),
                'picture' => $figure->getPictureFigure(),
                'slug' => $figure->getSlug()
            ];
        }

        return new JsonResponse($data);
    }
}
