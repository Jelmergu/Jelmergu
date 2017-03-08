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

    public function test_date_in_range()
    {
        $inrange = (new Date("2017-02-09"))->between('2017-02-08', "2017-02-10");
        $beforerange = (new Date("2017-02-09"))->between('2017-02-06', "2017-02-08");
        $afterrange = (new Date("2017-02-09"))->between('2017-02-12', "2017-02-10");

        $this->assertTrue($inrange);
        $this->assertFalse($beforerange);
        $this->assertFalse($afterrange);
    }

}