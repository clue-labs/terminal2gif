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
use DateTime;

class Info extends Command
{
    protected function configure()
    {
        $this->setName('info')
             ->setDescription('Show information about a recorded terminal session')
             ->addArgument('path', InputArgument::REQUIRED, 'Target file to show information for');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        $ttyrecord = new Ttyrecord($path);

        $this->row($output, 'Start date', $ttyrecord->getDateStart());
        $this->row($output, 'End date', $ttyrecord->getDateLast());
        $this->row($output, 'Duration', round($ttyrecord->getDuration(), 1) . 's');
        $this->row($output, 'Length', $ttyrecord->getLength() . 'b');
        $this->row($output, 'Number of chunks', $ttyrecord->getNumberOfChunks());
    }

    private function row(OutputInterface $output, $name, $value)
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d H:i:s.u');
        }
        $output->writeln(str_pad($name, 20) . ': ' . $value);
    }
}
