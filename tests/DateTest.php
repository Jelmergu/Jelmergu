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

    public function test_add_year() {
        
        $oneMoreYear = (new Date("2017-01-01"))->addYears(1)->format("Y-m-d H:i:s");
        $oneLessYear = (new Date("2017-01-01"))->addYears(-1)->format("Y-m-d H:i:s");
        $tenMoreYears= (new Date("2017-01-01"))->addYears(10)->format("Y-m-d H:i:s");
        $tenLessYears= (new Date("2017-01-01"))->addYears(-10)->format("Y-m-d H:i:s");
         
        $this->assertEquals("2018-01-01 00:00:00", $oneMoreYear);
        $this->assertEquals("2016-01-01 00:00:00", $oneLessYear);
        $this->assertEquals("2027-01-01 00:00:00", $tenMoreYears);
        $this->assertEquals("2007-01-01 00:00:00", $tenLessYears);
    }

    public function test_add_months () {
        $oneMoreMonth = (new Date("2017-01-01"))->addMonths(1)->format("Y-m-d H:i:s");
        $oneLessMonth = (new Date("2017-01-01"))->addMonths(-1)->format("Y-m-d H:i:s");

        $this->assertEquals("2017-02-01 00:00:00", $oneMoreMonth);
        $this->assertEquals("2016-12-01 00:00:00", $oneLessMonth);
    }
}