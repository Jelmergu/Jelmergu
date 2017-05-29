<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @since     0.1
 * @version   1.0.1
 *
 * @package   Jelmergu/Jelmergu
 */
namespace {

    const DATE_MYSQL_DATE = "Y-m-d";
    const DATE_MYSQL_TIMESTAMP = "Y-m-d H:i:s";
    const DATE_MYSQL_TIME = "H:i:s";
}

namespace Jelmergu {

    use DateTime;


    /**
     * The class Date contains additional options for date
     *
     * @package Jelmergu/Jelmergu
     */
    class Date extends DateTime
    {
        /**
         * Construct a Date object with microtime
         *
         * @since   1.0.6
         * @version 1.0
         *
         * @inheritDoc
         */
        public function __construct($time = 'now', DateTimeZone $timezone = null)
        {
            if ($time == 'now') {
                $time = date("Y-m-d H:i:s") . "." . explode(".", microtime(true))[1];
            }
            parent::__construct($time, $timezone);
        }

        /**
         * Changes the date(m) value to the dutch version of date(F)
         *
         * @since   1.0
         * @version 1.0
         *
         * @param  string $month Numeric representation of a month, with leading zeros as returned by date(m)
         *
         * @return string  A full textual representation of a month in the dutch language similar to date(F)
         */
        public function monthToString(string $month) : string
        {
            $months = [
                '01' => "januari",
                '02' => "februari",
                '03' => "maart",
                '04' => "april",
                '06' => "juni",
                '07' => "juli",
                '08' => "augustus",
                '09' => "september",
                '10' => "oktober",
                '11' => "november",
                '12' => "december",
                '05' => "mei",
            ];

            return $months[$month];
        }

        /**
         * Returns the time in 24-hour:minute:second format
         *
         * @since   1.0.6
         * @version 1.0
         *
         * @return string
         */
        public function getTime()
        {
            return $this->format("H:i:s");
        }

        /**
         * Returns the date in 'yyyy-mm-dd' format
         *
         * @since   1.0.6
         * @version 1.0
         *
         * @return string
         */
        public function getDate()
        {
            return $this->format("Y-m-d");
        }

        /**
         * Add or subtract specified years from the current date
         *
         * @since   1.0.5
         * @version 1.0
         *
         * @param int $years The years to add or subtract. Subtraction is specified with a minus
         *
         * @return Date
         */
        public function addYears(int $years) : Date
        {
            $method = "add";
            if ($years < 0) {
                $method = "sub";
            }
            $this->$method(new \DateInterval("P" . sqrt(pow($years, 2)) . "Y"));

            return $this;
        }

        /**
         * Add or subtract specified months from the current date
         *
         * @since   1.0.5
         * @version 1.0
         *
         * @param int $months The months to add or subtract. Subtraction is specified with a minus
         *
         * @return Date
         */
        public function addMonths(int $months) : Date
        {
            $method = "add";
            if ($months < 0) {
                $method = "sub";
            }
            $this->$method(new \DateInterval("P" . sqrt(pow($months, 2)) . "M"));

            return $this;
        }

        /**
         * Add or subtract specified days from the current date
         *
         * @since   1.0.5
         * @version 1.0
         *
         * @param int $days The days to add or subtract. Subtraction is specified with a minus
         *
         * @return Date
         */
        public function addDays(int $days) : Date
        {
            $method = "add";
            if ($days < 0) {
                $method = "sub";
            }
            $this->$method(new \DateInterval("P" . sqrt(pow($days, 2)) . "D"));

            return $this;
        }

        /**
         * Add or subtract specified hours from the current date
         *
         * @since   1.0.5
         * @version 1.0
         *
         * @param int $hours The hours to add or subtract. Subtraction is specified with a minus
         *
         * @return Date
         */
        public function addHours(int $hours) : Date
        {
            $method = "add";
            if ($hours < 0) {
                $method = "sub";
            }
            $this->$method(new \DateInterval("PT" . sqrt(pow($hours, 2)) . "H"));

            return $this;
        }

        /**
         * Add or subtract specified minutes from the current date
         *
         * @since   1.0.5
         * @version 1.0
         *
         * @param int $minutes The minutes to add or subtract. Subtraction is specified with a minus
         *
         * @return Date
         */
        public function addMinutes(int $minutes) : Date
        {
            $method = "add";
            if ($minutes < 0) {
                $method = "sub";
            }
            $this->$method(new \DateInterval("PT" . sqrt(pow($minutes, 2)) . "M"));

            return $this;
        }

        /**
         * Add or subtract specified seconds from the current date
         *
         * @since   1.0.5
         * @version 1.0
         *
         * @param int $seconds The seconds to add or subtract. Subtraction is specified with a minus
         *
         * @return Date
         */
        public function addSeconds(int $seconds) : Date
        {
            $method = "add";
            if ($seconds < 0) {
                $method = "sub";
            }
            $this->$method(new \DateInterval("PT" . sqrt(pow($seconds, 2)) . "S"));

            return $this;
        }

        /**
         * Check if the current date is between the input dates
         *
         * @since   1.0.5
         * @version 1.0
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
} // namespace Jelmergu