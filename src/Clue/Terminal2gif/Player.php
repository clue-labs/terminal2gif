<?php

namespace Clue\Terminal2gif;

use Clue\Terminal2gif\Ttyrecord;
use InvalidArgumentException;

class Player
{
    private $record;
    private $rate = 1.0;
    private $output;

    public static function factoryFile($path)
    {
        return new self(new Ttyrecord($path));
    }

    public function __construct(Ttyrecord $record)
    {
        $this->record = $record;
        $this->output = function ($chunk) {
            echo $chunk;
        };
    }

    public function setRate($rate)
    {
        if ($rate <= 0) {
            throw new InvalidArgumentException('Rate must be positive');
        }
        $this->rate = $rate;
    }

    public function setOutput($fn)
    {
        $this->output = $fn;
    }

    public function play()
    {
        $delays = $this->record->getAllDelays();
        $chunks = $this->record->getAllChunks();

        foreach ($delays as $i => $delay) {
            //echo $delay;
            usleep($delay * 1000000 / $this->rate);

            call_user_func($this->output, $chunks[$i]);
        }
    }
}
