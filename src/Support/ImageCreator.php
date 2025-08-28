<?php
namespace App\Support;

use Exception;

class ImageCreator {
    
    public function imageCleanResize(array $image, $extension){

        $createFrom = [
            'jpg' => 'imagecreatefromjpeg',
            'jpeg' => 'imagecreatefromjpeg',
            'png' => 'imagecreatefrompng',
            'gif' => 'imagecreatefromgif',
            'bmp' => 'imagecreatefrombmp',
        ];

        if (!function_exists($createFrom[$extension])) {
            return false;
        }

        $originalImage = @$createFrom[$extension]($image['tmp_name']);
        if (!$originalImage) {
            return false;
        }

        // EXIF rotation handling only for JPEG
        if (in_array($extension, ['jpg', 'jpeg']) && function_exists('exif_read_data')) {

            $exif = @exif_read_data($image['tmp_name']);

            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $originalImage = imagerotate($originalImage, 180, 0);
                        break;
                    case 6:
                        $originalImage = imagerotate($originalImage, -90, 0);
                        break;
                    case 8:
                        $originalImage = imagerotate($originalImage, 90, 0);
                        break;
                }
            }
        }

        $origWidth = imagesx($originalImage);
        $origHeight = imagesy($originalImage);

        $maxDim = 1500;
        if ($origWidth <= $maxDim && $origHeight <= $maxDim) {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        } 
        else {
            $scale = $maxDim / max($origWidth, $origHeight);
            $newWidth = (int)($origWidth * $scale);
            $newHeight = (int)($origHeight * $scale);
        }

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // preserve transparency PNG, GIF
        if (in_array($extension, ['png', 'gif'])) {
            imagecolortransparent($resizedImage, imagecolorallocatealpha($resizedImage, 0, 0, 0, 127));
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
        }

        imagecopyresampled(
            $resizedImage,
            $originalImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $origWidth, $origHeight
        );

        return [$resizedImage, $originalImage];
    } 

    public function imageValidation(array $image){

        if (empty($image) || $image['error'] !== UPLOAD_ERR_OK || $image['size'] === 0) {
            return false;
        }

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
        $extension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedTypes)) {
            echo "Sorry, only JPG, JPEG, PNG, GIF & BMP files are allowed.";
            return false;
        }
        return $extension; 
    }

    public function imageFilename(array $image, string $user_id) {

        $nameWithoutExt = pathinfo($image['name'], PATHINFO_FILENAME);
        $safeName = preg_replace('/[^a-zA-Z0-9]/', '', $nameWithoutExt);

        $hash = hash('sha256', $user_id . '_' . $safeName . '_' . time() . '_' . random_bytes(8));
        $fileName = $hash . '.jpg';

        return $fileName;
    }

    public function createImage(array $image, string $user_id): array|false { // create image for posts

        $user_id = (int) $user_id;

        $extension = $this->imageValidation($image);

        $fileName = $this->imageFilename($image, $user_id);

        //paths
        $relativeUserPath = "content/user/{$user_id}/";
        $absoluteUserPath = realpath(__DIR__ . "/../../") . '/' . $relativeUserPath;

        if (!is_dir($absoluteUserPath)) {
            mkdir($absoluteUserPath, 0755, true);
        }
        $destImage = $absoluteUserPath . $fileName;
        $imageFolder = $relativeUserPath . $fileName;

        //image resize and clean
        $images = $this->imageCleanResize($image, $extension);
        if ($images === false){
            return false;
        }

        return [
            'new_image' => $images[0],
            'destination' => $destImage,
            'old_image' => $images[1],
            'image_folder' => $imageFolder,
        ];
    }

    public function createImageProf (array $image, string $user_id): array|false { //create image for profile pics

        $user_id = (int) $user_id;

        $extension = $this->imageValidation($image);

        $fileName = $this->imageFilename($image, $user_id);

        //paths
        $relativeUserPath = "content/user/{$user_id}/profile/";
        $absoluteUserPath = realpath(__DIR__ . "/../../") . '/' . $relativeUserPath;

        if (!is_dir($absoluteUserPath)) {
            mkdir($absoluteUserPath, 0755, true);
        }
        $destImage = $absoluteUserPath . $fileName;
        $imageFolder = $relativeUserPath . $fileName;

        //image resize and clean
        $images = $this->imageCleanResize($image, $extension);

        return [
            'new_image' => $images[0],
            'destination' => $destImage,
            'old_image' => $images[1],
            'image_folder' => $imageFolder,
        ];
    }

    public function viewImage(array $image, string $user_id): array|false { //viewing temp image 
        $user_id = (int) $user_id;

        $extension = $this->imageValidation($image);

        $fileName = $this->imageFilename($image, $user_id);;

        $images = $this->imageCleanResize($image, $extension);
       
        return [
            'new_image' => $images[0],
            'old_image' => $images[1],
            'filename' => $fileName
        ];
    }
}