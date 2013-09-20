<?php

namespace Clue\Terminal2gif\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Clue\Terminal2gif\Terminal2gif;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\DialogHelper;
use Clue\Terminal2gif\Ttyrecord;
use Clue\Terminal2gif\Player;

class Play extends Command
{
    protected function configure()
    {
        $this->setName('play')
             ->setDescription('Play back a recorded terminal session')
             ->addOption('rate', null, InputOption::VALUE_OPTIONAL, 'Optional playback rate (speed multiplier)', 1.0)
             ->addArgument('path', InputArgument::REQUIRED, 'Target file to play back');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $rate = (float)$input->getOption('rate');

        $ttyrecord = new Ttyrecord($path);
        $player = new Player($ttyrecord);
        $player->setOutput(function ($chunk) use ($output) {
            $output->write($chunk);
        });
        $player->setRate($rate);

        $output->writeln(str_repeat('-', 40));
        $player->play();
        $output->writeln(str_repeat('-', 40));
    }
}
