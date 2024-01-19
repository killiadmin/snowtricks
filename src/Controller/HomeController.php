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
        $isAuthenticated = $this->isGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('home/index.html.twig',
            [
                'is_authenticated' => $isAuthenticated
            ]);
    }

    /**
     * @Route("/load-more", name="load_more")
     * Retrieves a list of figures to load for pagination.
     *
     * @param FigureRepository $figureRepository The repository for figures.
     * @param Request $request The request object containing the page number.
     * @return JsonResponse The JSON response containing the loaded figures.
     */
    public function loadMore(FigureRepository $figureRepository, Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = 15;
        $figures = $figureRepository->selectAllFigures($page, $limit);

        // Transform data into associative array
        $data = [];
        foreach ($figures as $figure) {
            $medias = $figure->getMedias();
            $pictures = [];

            foreach ($medias as $media) {
                $pictures[] = $media->getMedImage();
            }

            $data[] = [
                'title' => $figure->getTitle(),
                'picture' => $pictures,
                'slug' => $figure->getSlug()
            ];
        }

        return new JsonResponse($data);
    }
}
