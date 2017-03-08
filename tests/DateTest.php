<?php

use Jelmergu\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function test_second_month_returns_february()
    {
        $month = (new Date())->monthToString('02');

        $this->assertEquals('februari', $month);
    }

    public function test_constructor_with_different_format(){


        $validStringDate = new Date("2017-02-09");

        // add 1 if the constructor doesn't throw an error
        $this->addToAssertionCount(1);

        $validMicrotimeDate = new Date(strtotime("2017-02-09"));

        $this->addToAssertionCount(1);

        $this->expectException(Exception::class);

        $invalidStringDate = new Date("2016-15-02");

        $this->expectException(Exception::class);

        $invalidMicrotimeDate = new Date(strtotime("2016-15-02"));

    }

    public function test_date_in_range()
    {
        $inRange = (new Date(strtotime("2017-02-09")))->between('2017-02-08', "2017-02-10");
        $beforeRange = (new Date(strtotime("2017-02-09")))->between('2017-02-06', "2017-02-08");
        $afterRange = (new Date(strtotime("2017-02-09")))->between('2017-02-12', "2017-02-10");

        $this->assertTrue($inRange);
        $this->assertFalse($beforeRange);
        $this->assertFalse($afterRange);
    }

}