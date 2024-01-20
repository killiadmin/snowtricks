<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Repository\UserRepository;
use App\Service\PictureService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProfilController extends AbstractController
{
    private Security $security;
    private PictureService $pictureService;

    public function __construct(Security  $security, PictureService $pictureService)
    {
        $this->security = $security;
        $this->pictureService = $pictureService;
    }

    /**
     * @Route("/profil", name="app_profil")
     * @param Request $request The HTTP request object.
     * @param UserRepository $userRepository The repository for managing user data.
     *
     * @return Response The response object.
     * @throws NonUniqueResultException
     * Index method
     *
     * This method is responsible for rendering the user's profile page and handling form submissions to modify the profile.
     *
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            // If the user is not authenticated
            throw $this->createAccessDeniedException('You must be logged in to access this page.');
        }

        $user = $this->security->getUser();

        if (!$user) {
            $this->addFlash('error', 'This user does not exist');
            return $this->redirectToRoute('app_home');
        }

        $userMail = $user->getUserIdentifier();

        //Find this User by mail
        $associatedUser = $userRepository->findUserByEmail($userMail);

        $profilForm = $this->createForm(ProfilType::class, $associatedUser);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($associatedUser) {
                $folder = 'avatar';
                $uploadedImage = $profilForm->get('pictureIdentifier')->getData();

                //We retrieve the avatar that was uploaded in the form
                if ($uploadedImage) {
                    // Here, we delete the old avatar first
                    $oldAvatar = $associatedUser->getPictureIdentifier();

                    if($oldAvatar){
                        $this->pictureService->delete($oldAvatar, $folder);
                    }

                    $file = $this->pictureService->add($uploadedImage, $folder, 300, 300);
                    $associatedUser->setPictureIdentifier($file);
                }

                $associatedUser->setFirstnameIdentifier($profilForm->get('firstnameIdentifier')->getData());
                $associatedUser->setNameIdentifier($profilForm->get('nameIdentifier')->getData());
                $entityManager->persist($associatedUser);
                $entityManager->flush();

                $this->addFlash('success', 'Your profile has been modified');
            } else {
                $this->addFlash('error', 'Ton form ne s\'est pas éxécuté');
            }
            return $this->redirectToRoute('app_home');
        }

        return $this->render('profil/index.html.twig', [
            'profilForm' => $profilForm->createView(),
        ]);
    }
}
