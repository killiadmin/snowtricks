<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\NewFigureType;
use App\Service\PictureService;
use App\Service\SlugService;
use Proxies\__CG__\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewController extends AbstractController
{
    private SlugService $slugService;

    public function __construct(SlugService $slugService)
    {
        $this->slugService = $slugService;
    }

    /**
     * @Route("/tricks/new", name = "app_new")
     * Handles the submission of the new figure form.
     *
     * @param Request $request The request object.
     * @param PictureService $pictureService The picture service.
     * @return Response The response object.
     */
    public function index(Request $request, PictureService $pictureService): Response
    {
        $figure = new Figure();
        $form = $this->createForm(NewFigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // EntityManager
            $entityManager = $this->getDoctrine()->getManager();

            // Horodatage
            $timeStamp = new \DateTime();

            // Generate a slug
            $slug = $this->slugService->generateSlug($figure->getTitle());

            // ID user associated
            $associatedUserId = 1;

            // Implant idUserAssociated
            $associatedUser = $entityManager->getRepository(User::class)->find($associatedUserId);

            //Check if user exist
            if (!$associatedUser) {
                throw new \RuntimeException('User not found ' . $associatedUserId);
            }

            $figure->setDateCreate($timeStamp);
            $figure->setSlug($slug);
            $figure->setUserAssociated($associatedUser);

            foreach ($figure->getMedias() as $media) {
                if (!empty($media->getMedVideo())) {
                    $media->setMedType('video');
                    $media->setMedFigureAssociated($figure);
                    $media->setMedVideo($this->getIdsVideos($media->getMedVideo()));
                    $entityManager->persist($media);
                }

                if (!empty($media->getMedImage())) {
                    $medImages = $media->getMedImage();

                    // Check if $medImages is an array, otherwise create an array
                    if (!is_array($medImages)) {
                        $medImages = [$medImages];
                    }

                    foreach ($medImages as $med_image) {
                        if (empty($med_image)) {
                            throw new \RuntimeException('The image has not been uploaded correctly.');
                        }

                        $folder = 'uploads';

                        $uploadedImage = new UploadedFile($med_image,'');

                        $fichier = $pictureService->add($uploadedImage, $folder, 300, 300);

                        $media->setMedType('image');
                        $media->setMedFigureAssociated($figure);
                        $media->setMedImage($fichier);
                        $entityManager->persist($media);
                    }
                }
            }

            $entityManager->persist($figure);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('new/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Retrieves the video ID from the given URL.
     *
     * @param string $url The URL of the video.
     * @return string|null The video ID if found in the URL, null otherwise.
     */
    private function getIdsVideos(string $url): ?string
    {
        $parts = parse_url($url);

        if (isset($parts['query'])) {
            parse_str($parts['query'], $qs);

            if (isset($qs['v'])) {
                return $qs['v'];
            }
        }

        return null;
    }
}
