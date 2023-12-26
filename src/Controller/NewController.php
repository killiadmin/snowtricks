<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Media;
use App\Form\NewFigureType;
use Proxies\__CG__\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewController extends AbstractController
{
    /**
     * @Route("/tricks/new", name="app_new")
     */
    public function index(Request $request): Response
    {
        $uniqueIdVideo = uniqid('videoFigure_', true);
        $uniqueIdPicture = uniqid('pictureFigure_', true);

        $figure = new Figure();

        $form = $this->createForm(NewFigureType::class, $figure, [
            'uniqueIdVideo' => $uniqueIdVideo,
            'uniqueIdPicture' => $uniqueIdPicture
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->get('category')->getData();
            $title = $form->get('title')->getData();
            $linksVideos = $form->get('videoFigure')->getData();
            $pictureFigure = $form->get('pictureFigure')->getData();
            $contentFigure = $form->get('contentFigure')->getData();

            if (null === $title) {
                throw new \RuntimeException('Title is required.');
            }

            if (null === $contentFigure) {
                throw new \RuntimeException('The "content_figure" field is required.');
            }

            if (null === $category) {
                throw new \RuntimeException('The "category" field is required.');
            }

            // EntityManager
            $entityManager = $this->getDoctrine()->getManager();

            // Horodatage
            $timeStamp = new \DateTime();

            // Generate a slug
            $slug = $this->generateSlug($title);

            // ID user associated
            $associatedUserId = 1;

            // Implant idUserAssociated
            $associatedUser = $entityManager->getRepository(User::class)->find($associatedUserId);

            //Check if user exist
            if (!$associatedUser) {
                throw new \RuntimeException('User not found ' . $associatedUserId);
            }

            $figure->setTitle($title);
            $figure->setCategory($category);
            $figure->setContentFigure($contentFigure);
            $figure->setDateCreate($timeStamp);
            $figure->setSlug($slug);
            $figure->setUserAssociated($associatedUser);

            //Loop in this videos
            if (!empty($linksVideos)){
                foreach ($linksVideos as $linkVideo) {
                    $mediaVideo = new Media();
                    $mediaVideo->setMedVideo($linkVideo);
                    $mediaVideo->setMedType('video');
                    $mediaVideo->setMedFigureAssociated($figure);
                    $entityManager->persist($mediaVideo);
                }
            }

            //Loop in this images
            if ($pictureFigure instanceof UploadedFile){
                foreach ($pictureFigure as $picture) {
                    $mediaPicture = new Media();
                    $mediaPicture->setMedImage($picture);
                    $mediaPicture->setMedType('image');
                    $mediaPicture->setMedFigureAssociated($figure);
                    $entityManager->persist($mediaPicture);
                }
            }

            $entityManager->persist($figure);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('new/index.html.twig', [
            'form' => $form->createView(),
            'uniqueIdVideo' => $uniqueIdVideo,
            'uniqueIdPicture' => $uniqueIdPicture
        ]);
    }

    /**
     * @param $title
     * @return string
     */
    private function generateSlug($title): string
    {
        $title = preg_replace('~[^\pL\d]+~u', '-', $title);
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
        $title = preg_replace('~[^-\w]+~', '', $title);
        $title = trim($title, '-');
        $title = preg_replace('~-+~', '-', $title);
        $title = strtolower($title);

        if (empty($title)) {
            return 'n-a';
        }

        return $title;
    }
}
