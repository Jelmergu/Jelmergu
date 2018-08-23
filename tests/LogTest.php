<?php declare (strict_types=1);

use Jelmergu\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    /**
     * @dataProvider location_provider
     *
     *
     * @return void
     */
    public function test_setting_location_parses_to_correct_location($location, $result) {
        Log::setLogLocation($location);
        
        $this->assertEquals($result, Log::$logLocation);
    }
    
    public function location_provider() : array{
        return [
            ['/some/unix/like/path/with/unix/directory/separators/', DIRECTORY_SEPARATOR.'some'.DIRECTORY_SEPARATOR.'unix'.DIRECTORY_SEPARATOR.'like'.DIRECTORY_SEPARATOR.'path'.DIRECTORY_SEPARATOR.'with'.DIRECTORY_SEPARATOR.'unix'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'separators'.DIRECTORY_SEPARATOR],
            ['\some\unix\like\path\with\windows\directory\separators', DIRECTORY_SEPARATOR.'some'.DIRECTORY_SEPARATOR.'unix'.DIRECTORY_SEPARATOR.'like'.DIRECTORY_SEPARATOR.'path'.DIRECTORY_SEPARATOR.'with'.DIRECTORY_SEPARATOR.'windows'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'separators'.DIRECTORY_SEPARATOR],
            ['c:\some\windows\like\path\with\windows\ds', 'c:'.DIRECTORY_SEPARATOR.'some'.DIRECTORY_SEPARATOR.'windows'.DIRECTORY_SEPARATOR.'like'.DIRECTORY_SEPARATOR.'path'.DIRECTORY_SEPARATOR.'with'.DIRECTORY_SEPARATOR.'windows'.DIRECTORY_SEPARATOR.'ds'.DIRECTORY_SEPARATOR],
            ['c:/some/windows/like/path/with/unix/ds', 'c:'.DIRECTORY_SEPARATOR.'some'.DIRECTORY_SEPARATOR.'windows'.DIRECTORY_SEPARATOR.'like'.DIRECTORY_SEPARATOR.'path'.DIRECTORY_SEPARATOR.'with'.DIRECTORY_SEPARATOR.'unix'.DIRECTORY_SEPARATOR.'ds'.DIRECTORY_SEPARATOR],
        ];
    }
}
