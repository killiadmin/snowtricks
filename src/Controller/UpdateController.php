<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\NewFigureType;
use App\Repository\FigureRepository;
use App\Service\ImageUploadService;
use App\Service\UtilsService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(Request $request, string $slug, FigureRepository $figureRepository, ImageUploadService $imageUploadService): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            // If the user is not authenticated
            throw $this->createAccessDeniedException('You must be logged in to access this page.');
        }

        $figure = $figureRepository->findOneBySlug($slug);

        //We retrieve the formFigure to retrieve the Media collection
        $newMedia = new Figure();

        if ($figure === null) {
            throw $this->createNotFoundException('The requested figure does not exist.');
        }

        $form = $this->createForm(NewFigureType::class, $figure, ['display_medias' => false]);
        $mediaForm = $this->createForm(NewFigureType::class, $newMedia, ['display_figure' => false]);

        $mediaForm->handleRequest($request);
        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            $newMediaFigure = $mediaForm->getData();
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
            return $this->redirectToRoute('tricks_editing', ['slug' => $figure->getSlug()]);
        } else {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $figure->setSlug($this->utilsService->generateSlug($figure->getTitle()));
                $this->getDoctrine()->getManager()->flush();

                // return to the figure page
                return $this->redirectToRoute('app_details', ['slug' => $figure->getSlug()]);
            }
        }

        // render your template with form
        return $this->render('update/index.html.twig', [
            'formUpdate' => $form->createView(),
            'formMedia' => $mediaForm->createView(),
            'figure' => $figure
        ]);
    }
}
