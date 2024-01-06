<?php

namespace App\Service;

use Exception;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * Adds the specified picture to the specified folder and creates a mini version of the picture with the given width and height.
     *
     * @param UploadedFile $picture The picture to be added.
     * @param string|null $folder The folder in which the picture should be added (defaults to an empty string).
     * @param int|null $width The width of the mini version of the picture (defaults to 250).
     * @param int|null $height The height of the mini version of the picture (defaults to 250).
     * @return string The filename of the added picture.
     * @throws RuntimeException If the image format is incorrect or if the directory cannot be created.
     */
    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250): string
    {
        $fichier = md5(uniqid(mt_rand(), true)) . '.webp';
        $pictureInfos = $this->getImageInfos($picture);

        $picture_source = $this->createPictureSource($pictureInfos, $picture);

        list($src_x, $src_y, $squareSize) = $this->calculateCroppingInfos($pictureInfos);

        $resized_picture = imagecreatetruecolor($width, $height);

        imagecopyresampled($resized_picture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory') . $folder;

        if(!file_exists($path . '/mini/') && !mkdir($concurrentDirectory = $path . '/mini/', 0755, true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        imagewebp($resized_picture, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        imagedestroy($resized_picture);
        imagedestroy($picture_source);

        $picture->move($path . '/', $fichier);

        return $fichier;
    }

    private function getImageInfos(UploadedFile $picture): array
    {
        $pictureInfos = getimagesize($picture);
        if($pictureInfos === false){
            throw new RuntimeException('Incorrect image format');
        }
        return $pictureInfos;
    }

    private function createPictureSource(array $pictureInfos, UploadedFile $picture)
    {
        switch($pictureInfos['mime']){
            case 'image/png':
                $picture_source = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $picture_source = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                $picture_source = imagecreatefromwebp($picture);
                break;
            default:
                throw new RuntimeException('Incorrect image format');
        }
        return $picture_source;
    }

    private function calculateCroppingInfos(array $pictureInfos): array
    {
        $imageWidth = $pictureInfos[0];
        $imageHeight = $pictureInfos[1];
        switch ($imageWidth <=> $imageHeight){
            case -1: // portrait
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
            case 0: // carré
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case 1: // paysage
                $squareSize = $imageHeight;
                $src_x = ($imageWidth - $squareSize) / 2;
                $src_y = 0;
                break;
        }
        return [$src_x, $src_y, $squareSize];
    }

    /**
     * Deletes the specified file from the specified folder and its corresponding miniature image if it exists.
     *
     * @param string $fichier The name of the file to be deleted.
     * @param string|null $folder The folder in which the file is located (defaults to an empty string).
     * @param int|null $width The width of the miniature image (defaults to 250).
     * @param int|null $height The height of the miniature image (defaults to 250).
     * @return bool True if the file and the miniature image (if it exists) were successfully deleted, false otherwise.
     */
    public function delete(string $fichier, ?string $folder = '', ?int $width = 300, ?int $height = 300): bool
    {
        if($fichier !== 'default.webp'){
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            $mini = $path . '/mini/' . $width . 'x' . $height . '-' . $fichier;

            if(file_exists($mini)){
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $fichier;

            if(file_exists($original)){
                unlink($original);
                $success = true;
            }
            return $success;
        }
        return false;
    }
}