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
use Proxies\__CG__\App\Entity\User;
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

        //On récupère le formFigure pour récupérer la collection Medias
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
                if (!empty($media->getMedVideo())) {
                    $media->setMedType('video');
                    $media->setMedFigureAssociated($figure);
                    $media->setMedVideo($this->utilsService->getIdsVideos($media->getMedVideo()));
                    $figure->addMedia($media);
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
            $entityManager->flush();
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
