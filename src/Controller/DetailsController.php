<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\NewFigureType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Repository\MediaRepository;
use App\Service\PictureService;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DetailsController extends AbstractController
{
    private PictureService $pictureService;

    private SlugService $slugService;

    public function __construct(PictureService $pictureService, SlugService $slugService) {
        $this->pictureService = $pictureService;
        $this->slugService = $slugService;
    }

    /**
     * @Route("/tricks/details/{slug}", name="app_details", methods={"GET","POST"})
     * @throws NonUniqueResultException
     */
    public function index(Request $request, string $slug, FigureRepository $figureRepository, EntityManagerInterface $manager): Response
    {
        $figure = $figureRepository->findOneBySlug($slug);

        if (!$figure) {
            throw $this->createNotFoundException('Figure not found');
        }

        $medias = $figure->getMedias();

        if ($medias->isEmpty()) {
            $medias = null;
        }

        // Editing Form Figure
        $figureForm = $this->createForm(NewFigureType::class, $figure);
        $figureForm->handleRequest($request);

        if ($figureForm->isSubmitted() && $figureForm->isValid()) {
            $title = $figureForm->get('title')->getData();
            $figure->setSlug($this->slugService->generateSlug($title));

            $manager->flush();

            return $this->redirectToRoute('app_details', ['slug' => $figure->getSlug()]);
        }

        // Section Comments
        $comment = new Comment();
        $postComment = $this->createForm(CommentType::class, $comment);
        $postComment->handleRequest($request);

        if ($postComment->isSubmitted() && $postComment->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $associatedUserId = 1;

            // Implant idUserAssociated
            $associatedUser = $entityManager->getRepository(User::class)->find($associatedUserId);
            $comment->setUserAssociated($associatedUser);
            $comment->setFigureAssociated($figure);
            $comment->setDateCreate(new \DateTime());

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_details', ['slug' => $slug, '_fragment' => 'loadComment']);
        }

        // Return render
        return $this->render('details/index.html.twig', [
            'controller_name' => 'DetailsController',
            'slug' => $slug,
            'figure' => $figure,
            'medias' => $medias,
            'formComment' => $postComment->createView(),
            'figureForm' => $figureForm->createView(),
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
     * @Route("/load-more-comments/{slug}", name="load_more_comments", methods={"GET"})
     * Function allowing comments to be loaded in blocks of 5,
     * comments are filtered by the lug which will be passed through the url
     * @throws Exception
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
