<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Repository\MediaRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DetailsController extends AbstractController
{
    private PictureService $pictureService;

    public function __construct(PictureService $pictureService) {
        $this->pictureService = $pictureService;
    }

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
     * @Route("/tricks/details/{slug}/delete", name="app_delete_figure", methods={"DELETE"})
     *
     * Function allowing to delete a figure by its slug
     *
     * @param string $slug The slug of the figure to be deleted
     * @param FigureRepository $figureRepository The repository for fetching the figure
     * @param EntityManagerInterface $em The entity manager for managing the deletion
     *
     * @return Response
     * @throws NotFoundHttpException|NonUniqueResultException If the figure is not found
     *
     */
    public function deleteFigure(string $slug, FigureRepository $figureRepository, EntityManagerInterface $em): Response
    {
        // Find figure by slug
        $figure = $figureRepository->findOneBySlug($slug);

        // If the figure does not exist, create an exception
        if (!$figure) {
            throw $this->createNotFoundException('The figure does not exist');
        }

        $medias = $figure->getMedias();

        foreach ($medias as $media) {
            if ($media->getMedType() === 'image') {
                $this->pictureService->delete($media->getMedImage(), 'uploads');
            }

            $em->remove($media);
        }

        // Delete figure
        $em->remove($figure);
        $em->flush();

        return new JsonResponse([
            'redirect' => $this->generateUrl('app_home')
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
