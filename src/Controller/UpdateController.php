<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Media;
use App\Form\MediaType;
use App\Form\NewFigureType;
use App\Repository\FigureRepository;
use App\Service\PictureService;
use App\Service\UtilsService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends AbstractController
{
    private UtilsService $utilsService;

    public function __construct(UtilsService $utilsService)
    {
        $this->utilsService = $utilsService;
    }
    /**
     * @Route("/tricks/editing/{slug}", name="tricks_editing")
     * @throws NonUniqueResultException
     */
    public function index(Request $request, string $slug, FigureRepository $figureRepository, PictureService $pictureService): Response
    {
        $figure = $figureRepository->findOneBySlug($slug);
        $newMedia = new Media();

        if ($figure === null) {
            throw $this->createNotFoundException('The requested figure does not exist.');
        }

        $form = $this->createForm(NewFigureType::class, $figure, ['display_medias' => false]);
        $mediaForm = $this->createForm(MediaType::class, $newMedia);

        $form->handleRequest($request);
        $mediaForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $figure->setSlug($this->utilsService->generateSlug($figure->getTitle()));
            $this->getDoctrine()->getManager()->flush();

            // return to the figure page
            return $this->redirectToRoute('app_details', ['slug' => $figure->getSlug()]);
        }

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $mediaData = $mediaForm->getData();

            if (!empty($mediaData->getMedVideo())) {
                $mediaData->setMedType('video');
                $mediaData->setMedFigureAssociated($figure);
                $mediaData->setMedVideo($this->utilsService->getIdsVideos($mediaData->getMedVideo()));
                $entityManager->persist($mediaData);
            }

            if (!empty($mediaData->getMedImage())) {
                $medImages = $mediaData->getMedImage();

                if (!is_array($medImages)) {
                    $medImages = [$medImages];
                }

                foreach ($medImages as $med_image) {
                    if (empty($med_image)) {
                        throw new \RuntimeException('The image has not been uploaded correctly.');
                    }

                    $folder = 'uploads';

                    $uploadedImage = new UploadedFile($med_image, '');

                    $fichier = $pictureService->add($uploadedImage, $folder, 300, 300);

                    $mediaData->setMedType('image');
                    $mediaData->setMedFigureAssociated($figure);
                    $mediaData->setMedImage($fichier);
                    $entityManager->persist($mediaData);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            // return to the figure page
            return $this->redirectToRoute('app_details', ['slug' => $figure->getSlug()]);
        }

        // render your template with form
        return $this->render('update/index.html.twig', [
            'formUpdate' => $form->createView(),
            'formMedia' => $mediaForm->createView(),
            'figure' => $figure
        ]);
    }
}
