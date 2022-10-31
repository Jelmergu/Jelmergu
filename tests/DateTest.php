<?php declare (strict_types=1);

use Jelmergu\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    /**
     * @dataProvider constructor_provider
     */
    public function test_date_constructor($argument, $result)
    {
        $date = new Date($argument);

        $this->assertEquals($result, $date->format(DATE_MYSQL_TIMESTAMP.'.u'));
    }

    public function constructor_provider() : array
    {
        return [
            ['2017-02-09', '2017-02-09 00:00:00.000000'],
            ['2017-02-09 00:00:00.1', '2017-02-09 00:00:00.100000'],
        ];
    }

    public function test_constructor_with_numeric_time()
    {
        $date = new Date(1486598400);

        $this->assertEquals('2017-02-09 00:00:00.000000', $date->format(DATE_MYSQL_TIMESTAMP.'.u'));
    }

    public function test_second_month_returns_february()
    {
        $month = (new Date())->monthToString('02');

        $this->assertEquals('februari', $month);
    }

    public function test_date_in_range()
    {
        $inrange     = (new Date('2017-02-09'))->between('2017-02-08', '2017-02-10');
        $beforerange = (new Date('2017-02-09'))->between('2017-02-06', '2017-02-08');
        $afterrange  = (new Date('2017-02-09'))->between('2017-02-12', '2017-02-10');

        $this->assertTrue($inrange);
        $this->assertFalse($beforerange);
        $this->assertFalse($afterrange);
    }

    public function methodProvider() : array
    {
        return [
            [
                '2017-01-01',
                +1,
                [
                    'year'   => '2018-01-01 00:00:00',
                    'month'  => '2017-02-01 00:00:00',
                    'day'    => '2017-01-02 00:00:00',
                    'hour'   => '2017-01-01 01:00:00',
                    'minute' => '2017-01-01 00:01:00',
                    'second' => '2017-01-01 00:00:01',
                ],
            ],
            [
                '2017-01-01',
                -1,
                [
                    'year'   => '2016-01-01 00:00:00',
                    'month'  => '2016-12-01 00:00:00',
                    'day'    => '2016-12-31 00:00:00',
                    'hour'   => '2016-12-31 23:00:00',
                    'minute' => '2016-12-31 23:59:00',
                    'second' => '2016-12-31 23:59:59',
                ],
            ],
        ];
    }

    /**
     * @dataProvider methodProvider
     */
    public function test_multiple_methods_with_provider($start, $change, array $results)
    {
        $this->assertEquals($results['year'], (new Date($start))->addYears($change)->format(DATE_MYSQL_TIMESTAMP));
        $this->assertEquals($results['month'], (new Date($start))->addMonths($change)->format(DATE_MYSQL_TIMESTAMP));
        $this->assertEquals($results['day'], (new Date($start))->addDays($change)->format(DATE_MYSQL_TIMESTAMP));
        $this->assertEquals($results['hour'], (new Date($start))->addHours($change)->format(DATE_MYSQL_TIMESTAMP));
        $this->assertEquals($results['minute'], (new Date($start))->addMinutes($change)->format(DATE_MYSQL_TIMESTAMP));
        $this->assertEquals($results['second'], (new Date($start))->addSeconds($change)->format(DATE_MYSQL_TIMESTAMP));
    }

    public function test_date_format_constants()
    {
        $date = new Date('2017-01-01 00:00:00');

        $this->assertEquals('2017-01-01', $date->format(DATE_MYSQL_DATE));
        $this->assertEquals('2017-01-01', $date->getDate());

        $this->assertEquals('00:00:00', $date->format(DATE_MYSQL_TIME));
        $this->assertEquals('00:00:00', $date->getTime());

        $this->assertEquals('2017-01-01 00:00:00', $date->format(DATE_MYSQL_TIMESTAMP));
    }

    public function test_date_toString()
    {
        $date = new Date('2017-01-01 00:00:00');

        $this->assertEquals('2017-01-01 00:00:00', (string)$date);
    }

    public function test_date_constants()
    {
        $this->assertEquals('Y-m-d', DATE_MYSQL_DATE);
        $this->assertEquals('H:i:s', DATE_MYSQL_TIME);
        $this->assertEquals('Y-m-d H:i:s', DATE_MYSQL_TIMESTAMP);
    }

    /**
     * @dataProvider addTimeProvider
     */
    public function test_add_time($start, $change, $result)
    {
        $date = new DateTestCover($start);
        $date->addTimeCover($change[0], $change[1]);
        $this->assertEquals($result, $date->format(DATE_MYSQL_TIMESTAMP));
    }

    public function addTimeProvider()
    {
        return [
            [
                '2017-01-01 00:00:00',
                [10, 'I'],
                '2017-01-01 00:10:00',
            ],
            [
                '2017-01-01 00:00:00',
                [10, 'S'],
                '2017-01-01 00:00:10',
            ],
            [
                '2017-01-01 00:00:00',
                [10, 'H'],
                '2017-01-01 10:00:00',
            ],
            [
                '2017-01-01 00:00:00',
                [10, 'Y'],
                '2027-01-01 00:00:00',
            ],
            [
                '2017-01-01 00:00:00',
                [10, 'M'],
                '2017-11-01 00:00:00',
            ],
            [
                '2017-01-01 00:00:00',
                [10, 'D'],
                '2017-01-11 00:00:00',
            ],
            [
                '2017-01-01 00:00:00',
                [10, 'abcdefghijklmnopqrstuvwxyz'],
                '2017-01-01 00:00:00',
            ],

        ];
    }

}

class DateTestCover extends Date
{
    public function addTimeCover(int $interval, string $unit)
    {
        $this->addTime($interval, $unit);
    }

}