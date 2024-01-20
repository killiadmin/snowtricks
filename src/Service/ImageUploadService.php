<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadService
{
    private PictureService $pictureService;
    private EntityManagerInterface $entityManager;

    public function __construct(PictureService $pictureService, EntityManagerInterface $entityManager)
    {
        $this->pictureService = $pictureService;
        $this->entityManager = $entityManager;
    }

    public function handleUpload(Media $media, $figure): void
    {
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
                $uploadedImage = new UploadedFile($med_image, '');

                $file = $this->pictureService->add($uploadedImage, $folder, 300, 300);

                $media->setMedType('image');
                $media->setMedFigureAssociated($figure);
                $media->setMedImage($file);
                $this->entityManager->persist($media);
            }
        }
    }
}
