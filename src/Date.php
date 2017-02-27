<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @version   0.1
 *
 * @package Jelmergu
 */

namespace Jelmergu;

/**
 * The class Date contains additional options for date
 *
 * @package Jelmergu
 */
class Date extends \DateTime
{
    /**
     * Changes the date(m) value to the dutch version of date(F)
     *
     * @param  int $maand Numeric representation of a month, with leading zeros as returned by date(m)
     *
     * @return string           A full textual representation of a month in the dutch language similar to date(F)
     */
    public function monthToString(int $month) : string
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
}