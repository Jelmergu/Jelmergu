<?php declare (strict_types=1);

use Jelmergu\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{

    /**
     * @var  Image $image
     */
    protected $image;

    /**
     * Put your temp image into your filesystem.
     * (Not good as unit tests must work with any system,
     * but my research about mocking resources gave me nothing)
     */
    protected function setUp()
    {
        if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'testImage.jpg')) {
            try {
                unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'testImage.jpg');
            } catch (\Exception $e) {
                throw new \Exception('You have no permission to delete files in this directory:'.$e);
            }
        }

        $image = imagecreate(500, 500);
        try {
            imagejpeg($image, dirname(__FILE__).DIRECTORY_SEPARATOR.'testImage.jpg');
        } catch (\Exception $e) {
            throw new \Exception('You have no permission to create files in this directory:'.$e);
        }

        $this->files[] = dirname(__FILE__).DIRECTORY_SEPARATOR.'testImage.jpg';
        $this->image   = new Image($this->files[0]);
    }

    /**
     * Deleting of test images. No try/catch here as it would fire on setup
     */
    protected function tearDown()
    {
            if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'testImage.jpg')) {
                unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'testImage.jpg');
            }
            if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'newImage.jpg')) {
                unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'newImage.jpg');
            }

            if (is_dir(dirname(__FILE__).DIRECTORY_SEPARATOR.'tempDir')) {
                unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'tempDir'.DIRECTORY_SEPARATOR."newImage.jpg");
                rmdir(dirname(__FILE__).DIRECTORY_SEPARATOR.'tempDir');
            }

    }


    public function test_image_is_made()
    {
        $imageFileAsString   = file_get_contents($this->files[0]);
        $imageFileAsFileName = $this->files[0];

        $imageFromString = new Image($imageFileAsString, false);
        $imageFromFile   = new Image($imageFileAsFileName);

        $this->assertInstanceOf(Image::class, $imageFromString);
        $this->assertInstanceOf(Image::class, $imageFromFile);
    }

    public function test_image_dimensions()
    {
        $this->assertEquals(500, $this->image->getWidth());
        $this->assertEquals(500, $this->image->getHeight());
    }

    /**
     * @dataProvider resizingProvider
     *
     * @param $width
     * @param $height
     * @param $resultWidth
     * @param $resultHeight
     *
     * @return void
     */
    public function test_image_resizing($width, $height, $resultWidth, $resultHeight)
    {
        $image = $this->image;

        $image->resizePreserveAR($width, $height);

        $this->assertImageDimensions($resultWidth, $resultHeight);
    }

    public function test_image_upscaling()
    {
        $image = $this->image;

        $image->upscalePreserveAR(null, 600);
        $this->assertImageDimensions(600, 600);
    }

    public function test_image_downScaling()
    {
        $image = $this->image;

        $image->downscalePreserveAR(null, 300);
        $this->assertImageDimensions(300, 300);
    }

    public function test_image_expected_sizes()
    {

        $expectedSizes = [
            'png'  => 806,
            'gif'  => 799,
            'jpeg' => 4787,
            'jpg'  => 4787,
        ];

        $this->assertEquals($expectedSizes, $this->image->getSize());
        $this->assertEquals($expectedSizes['png'], $this->image->getSize('png'));
    }


    public function test_debug_info()
    {
        $expectedInfo = [
            "width"  => 500,
            "height" => 500,
            "size"   => [
                'png'  => 806,
                'gif'  => 799,
                'jpeg' => 4787,
                'jpg'  => 4787,
            ],
        ];

        $this->assertEquals($expectedInfo, $this->image->__debugInfo());
    }

    public function resizingProvider()
    {
        return [
            [600, null, 600, 600],
            [null, 600, 600, 600],
            [300, 600, 300, 600],
            [null, null, 500, 500],
        ];
    }

    public function test_saving_image()
    {
        $image = $this->image;

        $image->saveImage(dirname(__FILE__).DIRECTORY_SEPARATOR, 'newImage.jpg');
        $image->saveImage(dirname(__FILE__).DIRECTORY_SEPARATOR."tempDir".DIRECTORY_SEPARATOR, 'newImage.jpg');
        $this->assertFileExists(dirname(__FILE__).DIRECTORY_SEPARATOR.'newImage.jpg');
    }

    public function test_supported_image_types()
    {

        $constants    = get_defined_constants(true)['standard'];
        $correctTypes = [];
        foreach ($constants as $key => $constant) {
            if (strpos($key, "IMAGETYPE") !== false) {
                $key                = str_replace("IMAGETYPE_", "", $key);
                $correctTypes[$key] = $constant;
            }
        }
        unset($correctTypes['COUNT'], $correctTypes['UNKNOWN']);

        $generatedTypes = Image::getImageTypes();

        $cachedGeneratedTypes = Image::getImageTypes();

        $this->assertEquals($correctTypes, $generatedTypes, "", 0.0, 10, true);
        $this->assertEquals($correctTypes, $cachedGeneratedTypes, "", 0.0, 10, true);
    }

    protected function assertImageDimensions($width, $height)
    {
        $this->assertEquals($height, $this->image->getHeight());
        $this->assertEquals($width, $this->image->getWidth());
    }

}