<?php

use Jelmergu\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{


    /**
     * Put your temp image into your filesystem.
     * (Not good as unit tests must work with any system,
     * but my research about mocking resources gave me nothing)
     */
    protected function setUp()
    {
        if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR.'testImage.jpg')) {
            try {
                unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR. 'testImage.jpg');
            } catch (\Exception $e) {
                throw new \Exception('You have no permission to delete files in this directory:' . $e);
            }

        } else {
            $image = imagecreate(500, 500);
            try {
                imagejpeg($image, dirname(__FILE__) . '/testImage.jpg');
            } catch (\Exception $e) {
                throw new \Exception('You have no permission to create files in this directory:' . $e);
            }
        }

        $this->files[] = '/testImage.jpg';
    }

    /**
     * Deleting of test images. No try/catch here as it would fire on setup
     */
    protected function tearDown()
    {
        if (file_exists(dirname(__FILE__) . '/testImage.jpeg')) {
            rmdir(dirname(__FILE__) . '/testImage.jpeg');
        }

        if (file_exists(dirname(__FILE__) . '/newImage.jpeg')) {
            rmdir(dirname(__FILE__) . '/newImage.jpeg');
        }
    }


    // public function test_image_is_made() {
    //
    // }

    public function test_supported_image_types() {

        $constants = get_defined_constants(true)['standard'];
        $correctTypes = [];
        foreach ($constants as $key => $constant) {
            if (strpos($key, "IMAGETYPE") !== false) {
                $key = str_replace("IMAGETYPE_", "", $key);
                $correctTypes[$key] = $constant;
            }
        }
        unset($correctTypes['COUNT'], $correctTypes['UNKNOWN']);

        $generatedTypes = Image::getImageTypes();

        $cachedGeneratedTypes = Image::getImageTypes();

        $this->assertEquals($correctTypes, $generatedTypes,"", 0.0,10, true);
        $this->assertEquals($correctTypes, $cachedGeneratedTypes,"", 0.0,10, true);
    }

}