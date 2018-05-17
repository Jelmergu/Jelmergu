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

    public function test_add_days() {
        $oneMoreDay = (new Date("2017-01-01"))->addDays(1)->format("Y-m-d H:i:s");
        $oneLessDay = (new Date("2017-01-01"))->addDays(-1)->format("Y-m-d H:i:s");

        $this->assertEquals("2017-01-02 00:00:00", $oneMoreDay);
        $this->assertEquals("2016-12-31 00:00:00", $oneLessDay);
    }

    public function test_add_hours() {
        $oneMoreHour = (new Date("2017-01-01"))->addHours(1)->format("Y-m-d H:i:s");
        $oneLessHour = (new Date("2017-01-01"))->addHours(-1)->format("Y-m-d H:i:s");

        $this->assertEquals("2017-01-01 01:00:00", $oneMoreHour);
        $this->assertEquals("2016-12-31 23:00:00", $oneLessHour);
    }

    public function test_add_minutes() {
        $oneMoreMinute = (new Date("2017-01-01"))->addMinutes(1)->format("Y-m-d H:i:s");
        $oneLessMinute = (new Date("2017-01-01"))->addMinutes(-1)->format("Y-m-d H:i:s");

        $this->assertEquals("2017-01-01 00:01:00", $oneMoreMinute);
        $this->assertEquals("2016-12-31 23:59:00", $oneLessMinute);
    }

    public function test_add_seconds() {
        $oneMoreSecond = (new Date("2017-01-01"))->addSeconds(1)->format("Y-m-d H:i:s");
        $oneLessSecond = (new Date("2017-01-01"))->addSeconds(-1)->format("Y-m-d H:i:s");

        $this->assertEquals("2017-01-01 00:00:01", $oneMoreSecond);
        $this->assertEquals("2016-12-31 23:59:59", $oneLessSecond);
    }

    public function test_date_format_constants () {
        $date = new Date("2017-01-01 00:00:00");

        $this->assertEquals("2017-01-01", $date->format(DATE_MYSQL_DATE));
        $this->assertEquals("2017-01-01", $date->getDate());

        $this->assertEquals("00:00:00", $date->format(DATE_MYSQL_TIME));
        $this->assertEquals("00:00:00", $date->getTime());

        $this->assertEquals("2017-01-01 00:00:00", $date->format(DATE_MYSQL_TIMESTAMP));
    }

    public function test_date_toString() {
        $date = new Date("2017-01-01 00:00:00");

        $this->assertEquals("2017-01-01 00:00:00", (string)$date);
    }

    public function test_date_constants() {

        $this->assertEquals("Y-m-d", DATE_MYSQL_DATE);
        $this->assertEquals("H:i:s", DATE_MYSQL_TIME);
        $this->assertEquals("Y-m-d H:i:s", DATE_MYSQL_TIMESTAMP);
    }
}