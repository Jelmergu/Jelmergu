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
}