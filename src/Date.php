<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @since     0.1
 * @version   1.0.1
 *
 * @package   Jelmergu/Jelmergu
 */

namespace {

    const DATE_MYSQL_DATE      = 'Y-m-d';
    const DATE_MYSQL_TIMESTAMP = 'Y-m-d H:i:s';
    const DATE_MYSQL_TIME      = 'H:i:s';
}

namespace Jelmergu {
    use \DateTimeZone;

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
            if ($time === 'now') {
                $microTime = explode('.', microtime(true));
                $time = date('Y-m-d H:i:s').'.'.$microTime[1] ?? 0;
            } elseif (is_numeric($time)) {
                $mTime = \is_float($time) ? explode('.', $time)[1] : 0;
                $time  = date('Y-m-d H:i:s', $time).'.'.$mTime;
            }
            parent::__construct($time, $timezone);
        }

        /**
         * Changes the date value to the dutch version of date
         *
         * @since   1.0
         * @version 1.0
         *
         * @param  string $month Numeric representation of a month, with leading zeros as returned by date
         *
         * @return string A full textual representation of a month in the dutch language similar to date
         */
        public function monthToString(string $month) : string
        {
            $months = [
                '01' => 'januari',
                '02' => 'februari',
                '03' => 'maart',
                '04' => 'april',
                '05' => 'mei',
                '06' => 'juni',
                '07' => 'juli',
                '08' => 'augustus',
                '09' => 'september',
                '10' => 'oktober',
                '11' => 'november',
                '12' => 'december',
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
        public function getTime() : string
        {
            return $this->format('H:i:s');
        }

        /**
         * Returns the date in yyyy-mm-dd format
         *
         * @since   1.0.6
         * @version 1.0
         *
         * @return string
         */
        public function getDate() : string
        {
            return $this->format('Y-m-d');
        }

        /**
         * Add or subtract specified years from the current date
         *
         * @since   1.0.5
         * @version 1.0
         *
         * @param int $years The years to add or subtract. Subtraction is specified with a minus
         *
         * @throws \Exception
         *
         * @return Date
         */
        public function addYears(int $years) : Date
        {
            $this->addTime($years, 'Y');

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
         * @throws \Exception
         *
         * @return Date
         */
        public function addMonths(int $months) : Date
        {
            $this->addTime($months, 'M');

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
         * @throws \Exception
         *
         * @return Date
         */
        public function addDays(int $days) : Date
        {
            $this->addTime($days, 'D');

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
         * @throws \Exception
         *
         * @return Date
         */
        public function addHours(int $hours) : Date
        {
            $this->addTime($hours, 'H');

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
         * @throws \Exception
         *
         * @return Date
         */
        public function addMinutes(int $minutes) : Date
        {
            $this->addTime($minutes, 'I');

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
         * @throws \Exception
         *
         * @return Date
         */
        public function addSeconds(int $seconds) : Date
        {
            $this->addTime($seconds, 'S');

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

        /**
         * Returns the stored date ready to be used as a SQL timestamp
         *
         * @since   1.0.7
         * @version 1.0
         *
         * @return string
         */
        public function __toString()
        {
            return $this->format(DATE_MYSQL_TIMESTAMP);
        }

        protected function addTime(int $interval, string $unit)
        {
            switch ($unit) {
                case 'I':
                    $unit = 'M';
                case 'S':
                case 'H':
                    $dI = new \DateInterval('PT'.sqrt($interval ** 2).$unit);
                break;
                case 'Y':
                case 'M':
                case 'D':
                    $dI = new \DateInterval('P'.sqrt($interval ** 2).$unit);
                break;
                default:
                    return;
                break;
            }

            $interval < 0 ? $this->sub($dI) : $this->add($dI);
        }

    }
} // namespace Jelmergu