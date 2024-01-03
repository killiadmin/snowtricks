<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Repository\MediaRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailsController extends AbstractController
{
    /**
     * @Route("/tricks/details/{slug}", name="app_details")
     * @throws NonUniqueResultException
     */
    public function index(string $slug, FigureRepository $figureRepository): Response
    {
        $figure = $figureRepository->findOneBySlug($slug);

        if (!$figure) {
            throw $this->createNotFoundException('Figure not found.');
        }

        $medias = $figure->getMedias();

        if($medias->isEmpty()){
            $medias = null;
        }

        return $this->render('details/index.html.twig', [
            'controller_name' => 'DetailsController',
            'slug' => $slug,
            'figure' => $figure,
            'medias' => $medias
        ]);
    }

    /**
     * Function allowing comments to be loaded in blocks of 5,
     * comments are filtered by the lug which will be passed through the url
     * @Route("/load-more-comments/{slug}", name="load_more_comments", methods={"GET"})
     * @throws \Exception
     */
    public function loadMoreComments(CommentRepository $commentRepository, Request $request): JsonResponse
    {
        //Get current page and limit from query
        $page = $request->query->get('page', 1);
        $limit = 5;
        $offset = ($page - 1) * $limit;
        $slug = '';

        $path = parse_url($_SERVER['REQUEST_URI'])['path'];
        $segments = explode('/', $path);

        if (isset($segments[2])) {
            $slug = $segments[2];
        }

        //Load comments based on slug, page and limit
        $comments = $commentRepository->selectCommentsAssociated($slug, $limit, $offset);

        //Transform data into array
        $data = [];
        foreach ($comments as $comment) {
            $data[] = [
                'content' => $comment->getContentComment(),
                'date' => $comment->getDateCreate()->format('Y-m-d'),
                'user' => $comment->getUserAssociated()->getPseudo()
            ];
        }

        return new JsonResponse([
            'comments' => $data,
            'slug' => $slug
        ]);
    }
}
