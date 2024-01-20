<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Form\CommentType;
use App\Form\NewFigureType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use App\Service\ImageUploadService;
use App\Service\PictureService;
use App\Service\UtilsService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DetailsController extends AbstractController
{
    private PictureService $pictureService;
    private UtilsService $utilsService;
    private Security $security;

    public function __construct(PictureService $pictureService, UtilsService $utilsService, Security $security) {
        $this->pictureService = $pictureService;
        $this->utilsService = $utilsService;
        $this->security = $security;
    }

    /**
     * @Route("/tricks/details/{slug}", name="app_details", methods={"GET","POST"})
     * @param Request $request
     * @param string $slug
     * @param FigureRepository $figureRepository
     * @param EntityManagerInterface $manager
     * @param ImageUploadService $imageUploadService
     * @param UserRepository $userRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function index(Request $request, string $slug, FigureRepository $figureRepository, EntityManagerInterface $manager, ImageUploadService $imageUploadService, UserRepository $userRepository): Response
    {
        $figure = $figureRepository->findOneBySlug($slug);

        if (!$figure) {
            $this->addFlash('error', 'This figure does not exist');
            return $this->redirectToRoute('app_home');
        }

        $medias = $figure->getMedias();

        if ($medias->isEmpty()) {
            $medias = null;
        }

        // Editing Form Figure
        $figureForm = $this->createForm(NewFigureType::class, $figure, ['display_medias' => false]);

        // Editing Form Media
        $newInstanceMedia = new Figure();
        $mediaFormEditing = $this->createForm(NewFigureType::class, $newInstanceMedia, ['display_figure' => false]);
        $mediaFormEditing->handleRequest($request);
        if ($mediaFormEditing->isSubmitted() && $mediaFormEditing->isValid()) {
            $newMediaFigure = $mediaFormEditing->getData();
            $entityManager = $this->getDoctrine()->getManager();

            foreach ($newMediaFigure->getMedias() as $media) {
                //Add Videos
                if (!empty($media->getMedVideo())) {
                    $media->setMedType('video');
                    $media->setMedFigureAssociated($figure);
                    $media->setMedVideo($this->utilsService->getIdsVideos($media->getMedVideo()));
                    $figure->addMedia($media);
                    $entityManager->persist($media);
                }

                //Uploads Pictures
                $imageUploadService->handleUpload($media, $figure);
            }
            $entityManager->flush();

            // return to the figure editing
            return $this->redirectToRoute('app_details', ['slug' => $figure->getSlug()]);
        } else {
            // If we do not change media then we change the data of a figure
            $figureForm->handleRequest($request);

            if ($figureForm->isSubmitted() && $figureForm->isValid()) {
                $title = $figureForm->get('title')->getData();
                $figure->setSlug($this->utilsService->generateSlug($title));

                $manager->flush();

                return $this->redirectToRoute('app_details', ['slug' => $figure->getSlug()]);
            }
        }

        // Section Comments
        $comment = new Comment();
        $postComment = $this->createForm(CommentType::class, $comment);
        $postComment->handleRequest($request);

        if ($postComment->isSubmitted() && $postComment->isValid()) {
            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
                // If the user is not authenticated
                throw $this->createAccessDeniedException('You must be logged in to access this page.');
            }

            $entityManager = $this->getDoctrine()->getManager();

            // User associated
            $user = $this->security->getUser();
            if ($user) {
                $userMail = $user->getUserIdentifier();
                //Find this User by mail
                $associatedUser = $userRepository->findUserByEmail($userMail);
                // Implant idUserAssociated
                $comment->setUserAssociated($associatedUser);
            } else {
                $this->addFlash('error', 'You are not allowed to post a comment');
                return $this->redirectToRoute('app_login');
            }

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
            'figureForm' => $figureForm->createView(),
            'mediaFormEditing' => $mediaFormEditing->createView(),
            'formComment' => $postComment->createView(),
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
     * @param CommentRepository $commentRepository
     * @return Response
     * @throws NonUniqueResultException If the figure is not found
     * @throws Exception
     */
    public function deleteFigure(string $slug, FigureRepository $figureRepository, EntityManagerInterface $em, CommentRepository $commentRepository): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            // If the user is not authenticated
            throw $this->createAccessDeniedException('You must be logged in to access this page.');
        }

        // Find figure by slug
        $figure = $figureRepository->findOneBySlug($slug);

        // If the figure does not exist, create an exception
        if (!$figure) {
            throw $this->createNotFoundException('The figure does not exist');
        }

        $medias = $figure->getMedias();

        // Deleting all comments associated with this figure
        $comments = $commentRepository->selectCommentsAssociated($slug);
        if (!empty($comments)) {
            foreach ($comments as $comment) {
                $em->remove($comment);
            }
        }

        foreach ($medias as $media) {
            if ($media->getMedType() === 'image') {
                $this->pictureService->delete($media->getMedImage(), 'uploads');
            }

            $em->remove($media);
        }

        // First, flush to perform the deletions of comments and medias
        $em->flush();

        // Delete figure
        $em->remove($figure);

        // Flush again to perform the deletion of figure
        $em->flush();

        $this->addFlash('success', 'The figure has been deleted !');

        return new JsonResponse([
            'redirect' => $this->generateUrl('app_home')
        ]);
    }

    /**
     * @Route("/media/{id}/delete", name="media_delete", methods={"DELETE"})
     * Deletes a media entry from the database and associated file if applicable.
     *
     * @param int $id The ID of the media entry to delete.
     * @param MediaRepository $mediaRepository The media repository object for retrieving the media entry.
     * @param EntityManagerInterface $em The entity manager object for deleting the media entry.
     * @return Response A JSON response indicating the success or failure of the deletion.
     * @throws NotFoundHttpException If the media entry does not exist.
     */
    public function deleteMedia(int $id, MediaRepository $mediaRepository, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            // If the user is not authenticated
            throw $this->createAccessDeniedException('You must be logged in to access this page.');
        }

        // Find media by id
        $media = $mediaRepository->find($id);

        // If the media does not exist, throw an exception
        if (!$media) {
            throw $this->createNotFoundException('The media does not exist');
        }

        if ($media->getMedType() === 'image') {
            $this->pictureService->delete($media->getMedImage(), 'uploads');
        }

        $em->remove($media);
        $em->flush();

        return new JsonResponse(['message' => 'Media successfully deleted']);
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
        $limit = 10;
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
            $avatar = 'default_avatar.webp';
            $avatarPath = "/img/avatar/mini/300x300-" . $comment->getUserAssociated()->getPictureIdentifier();
            if (file_exists($_SERVER['DOCUMENT_ROOT'].$avatarPath)) {
                $avatar = $comment->getUserAssociated()->getPictureIdentifier();
            }

            $data[] = [
                'content' => $comment->getContentComment(),
                'date' => $comment->getDateCreate()->format('Y-m-d'),
                'user' => $comment->getUserAssociated()->getPseudo(),
                'avatar' => $avatar,
                'lastname' => $comment->getUserAssociated()->getNameIdentifier(),
                'firstname' => $comment->getUserAssociated()->getFirstnameIdentifier()
            ];
        }

        return new JsonResponse([
            'comments' => $data,
            'slug' => $slug
        ]);
    }
}
