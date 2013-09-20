<?php

namespace Clue\Terminal2gif;

use DateTime;
use Exception;
use InvalidArgumentException;

class Ttyrecord
{
    private $parsed;

    public function __construct($path)
    {
        $this->parsed = $this->parse($path);
    }

    /**
     * get duration in seconds (time between first and last chunk)
     *
     * @return float
     */
    public function getDuration()
    {
        $parsed = $this->parsed;
        $last   = end($parsed);
        $first  = reset($parsed);

        return ($last[0] - $first[0]);
    }

    /**
     * get start date of first recorded chunk
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        $parsed = $this->parsed;
        $first  = reset($parsed);

        return $this->getDateForMicrotime($first[0]);
    }

    /**
     * get end date of last recorded chunk
     *
     * @return \DateTime
     */
    public function getDateLast()
    {
        $parsed = $this->parsed;
        $last   = end($parsed);

        return $this->getDateForMicrotime($last[0]);
    }

    /**
     * get output string (append all chunks)
     *
     * @return string
     */
    public function getOutput()
    {
        $output = '';
        foreach ($this->parsed as $one) {
            $output .= $one[1];
        }

        return $output;
    }

    public function getOutputSafe()
    {
        return preg_replace('/[^\s[:graph:]]/u', '', $this->getOutput());
    }

    /**
     * get total length of output (sum length of all chunks)
     *
     * The length represents the number of bytes, which must not necessarily
     * reflect the number of characters displayed on the screen, due to control
     * characters and multi-byte encodings.
     *
     * @return int
     */
    public function getLength()
    {
        $length = 0;
        foreach ($this->parsed as $one) {
            $length += strlen($one[1]);
        }

        return $length;
    }

    /**
     * get total number of chunk
     *
     * @return int
     */
    public function getNumberOfChunks()
    {
        return count($this->parsed);
    }

    public function getAllDates()
    {
        $dates = array();
        foreach ($this->parsed as $one) {
            $dates[] = $this->getDateForMicrotime($one[0]);
        }

        return $dates;
    }

    public function getAllDelays()
    {
        $last   = null;
        $delays = array();
        foreach ($this->parsed as $one) {
            $now = $one[0];
            $delay = 0;

            if ($last !== null) {
                $delay = $now - $last;
            }
            $last = $now;
            $delays[] = $delay;
        }

        return $delays;
    }

    public function getAllChunks()
    {
        $chunks = array();
        foreach ($this->parsed as $one) {
            $chunks[] = $one[1];
        }

        return $chunks;
    }

    public function play($rate = 1.0)
    {
        if ($rate <= 0) {
            throw new InvalidArgumentException('Rate must be positive');
        }

        $last = null;
        foreach($this->parsed as $one) {
            $now = $one[0];
            $delay = 0;

            if ($last !== null) {
                $delay = ($now - $last) / $rate;
            }
            //echo $delay;
            usleep($delay * 1000000);
            $last = $now;

            echo $one[1];
        }
    }

    private function getDateForMicrotime($time)
    {
        $micro = sprintf("%06d", (($time - floor($time)) * 1000000));
        return new DateTime(date('Y-m-d H:i:s.' . $micro, $time));
    }

    private function parse($file)
    {
        $handle = @fopen($file, 'r');
        if (!$handle) {
            throw new InvalidArgumentException('Unable to open file');
        }

        $ret = array();

        while (true) {
            try {
                $buffer = $this->fread($handle, 3 * 4); // 3 * INT
            }
            catch (Exception $e) {
                break;
            }

            $data = unpack('Vsec/Vusec/Vlength', $buffer);

            $time = $data['sec'] + $data['usec'] * 0.000001;


            //var_dump($data, $time, $date->format('Y-m-d H:i:s.u'));
            $chunk = $this->fread($handle, $data['length']);

            //$chunk = preg_replace('/[^\w\s]/', '?' , $chunk);
            //echo $chunk;

            $ret[] = array($time, $chunk);
        }

        if (!$ret) {
            throw new Exception('Empty file');
        }

        return $ret;
    }

    private function fread($fp, $len)
    {
        $ret = fread($fp, $len);
        if ($ret === false) {
            throw new Exception('Input error');
        }
        if (strlen($ret) !== $len) {
            throw new Exception('Incomplete result: read ' . strlen($ret) . ' of ' . $len . ' total');
        }

        return $ret;
    }
}
