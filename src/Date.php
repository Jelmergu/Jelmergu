<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @version   0.1
 *
 * @package   Jelmergu
 */

namespace Jelmergu;

use DateTime;

/**
 * The class Date contains additional options for date
 *
 * @package Jelmergu
 */
class Date extends DateTime
{
    /**
     * Changes the date(m) value to the dutch version of date(F)
     *
     * @version v1.0
     *
     * @param  string $month Numeric representation of a month, with leading zeros as returned by date(m)
     *
     * @return string           A full textual representation of a month in the dutch language similar to date(F)
     */
    public function monthToString(string $month) : string
    {
        $months = [
            '01' => "januari", '02' => "februari",
            '03' => "maart", '04' => "april",
            '06' => "juni", '07' => "juli",
            '08' => "augustus", '09' => "september",
            '10' => "oktober", '11' => "november",
            '12' => "december", '05' => "mei",
        ];

        return $months[$month];
    }

    /**
     * Check if the current date is between the input dates
     *
     * @version v1.0.5
     *
     * @param string $lowerDate The beginning of the date range
     * @param string $upperDate The end of the date range
     *
     * @return bool
     */
    public function between(string $lowerDate, string $upperDate) : bool
    {
        return ($this->getTimestamp() >= strtotime($lowerDate) && $this->getTimestamp() <= strtotime($upperDate));
    }
}