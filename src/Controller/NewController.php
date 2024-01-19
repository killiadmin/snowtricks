<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\NewFigureType;
use App\Repository\UserRepository;
use App\Service\ImageUploadService;
use App\Service\UtilsService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class NewController extends AbstractController
{
    private UtilsService $utilsService;
    private Security $security;

    public function __construct(UtilsService $utilsService, Security  $security)
    {
        $this->utilsService = $utilsService;
        $this->security = $security;
    }

    /**
     * @Route("/tricks/new", name = "app_new")
     * Handles the submission of the new figure form.
     *
     * @param Request $request The request object.
     * @return Response The response object.
     * @throws NonUniqueResultException
     */
    public function index(Request $request, UserRepository $userRepository, ImageUploadService $imageUploadService): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            // If the user is not authenticated
            throw $this->createAccessDeniedException('You must be logged in to access this page.');
        }

        $figure = new Figure();
        $form = $this->createForm(NewFigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // EntityManager
            $entityManager = $this->getDoctrine()->getManager();

            // Horodatage
            $timeStamp = new \DateTime();

            // Generate a slug
            $slug = $this->utilsService->generateSlug($figure->getTitle());

            // User associated
            $user = $this->security->getUser();
            if ($user) {
                $userMail = $user->getUserIdentifier();
                //Find this User by mail
                $associatedUser = $userRepository->findUserByEmail($userMail);
                // Implant idUserAssociated
                $figure->setUserAssociated($associatedUser);
            } else {
                $this->addFlash('error', 'You are not allowed to post a trick');
                return $this->redirectToRoute('app_login');
            }

            $figure->setDateCreate($timeStamp);
            $figure->setSlug($slug);

            foreach ($figure->getMedias() as $media) {
                if (!empty($media->getMedVideo())) {
                    $media->setMedType('video');
                    $media->setMedFigureAssociated($figure);
                    $media->setMedVideo($this->utilsService->getIdsVideos($media->getMedVideo()));
                    $entityManager->persist($media);
                }

                //Uploads Pictures
                $imageUploadService->handleUpload($media, $figure);
            }

            $entityManager->persist($figure);
            $entityManager->flush();

            $this->addFlash('success', 'The figure has been successfully created!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('new/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
