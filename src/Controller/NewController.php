<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Media;
use App\Form\MediaType;
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
        $figure = new Figure();
        $form = $this->createForm(NewFigureType::class, $figure);
        $form->handleRequest($request);

        $media = new Media();
        $formMedia = $this->createForm(MediaType::class, $media);
        $formMedia->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $title = $form->get('title')->getData();
            /*$title = $form->getData();*/
            $category = $form->get('category')->getData();
            $contentFigure = $form->get('contentFigure')->getData();
            /*$linksVideos = $formMedia->get('med_video')->getData();*/
            /*$linksVideos = $formMedia->getData();*/
            $pictureFigure = $formMedia->get('med_image')->getData();

            $testMedVideo = $form->getData();
            $videos = [];
            foreach ($testMedVideo->getMedias() as $media) {
                $videos[] = $media->getMedVideo();
            }

            file_put_contents(__DIR__ . '/$test.txt', '$mesLiensVideos :' . print_r($testMedVideo, true) . PHP_EOL, FILE_APPEND);
            /*file_put_contents(__DIR__ . '/$test.txt', '$title :' . print_r($title, true) . PHP_EOL, FILE_APPEND);
            file_put_contents(__DIR__ . '/$test.txt', '$contentFigure :' . print_r($contentFigure, true) . PHP_EOL, FILE_APPEND);
            file_put_contents(__DIR__ . '/$test.txt', '$category :' . print_r($category, true) . PHP_EOL, FILE_APPEND);*/

            die();

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
            if (!empty($linksVideos)) {
                foreach ($linksVideos as $linkVideo) {
                    $mediaVideo = new Media();
                    $mediaVideo->setMedVideo($linkVideo);
                    $mediaVideo->setMedType('video');
                    $mediaVideo->setMedFigureAssociated($figure);
                    $entityManager->persist($mediaVideo);
                }
            }

            //Loop in this images
            if ($pictureFigure instanceof UploadedFile) {
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
            'formMedia' => $formMedia->createView()
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
