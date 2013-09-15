<?php

namespace Clue\Terminal2gif\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Clue\Terminal2gif\Terminal2gif;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\DialogHelper;

class Record extends Command
{
    protected function configure()
    {
        $this->setName('record')
             ->setDescription('Record a terminal session and generate a gif')
             ->addOption('ttygif', null, InputOption::VALUE_OPTIONAL, 'Optional path to ttygif installation')
             ->addArgument('path', InputArgument::OPTIONAL, 'Optional target file to record to', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('path');
        if ($name === null) {
            $name = uniqid('terminal2gif');
        }

        $dialog = $this->getHelper('dialog');
        /* @var $dialog DialogHelper */

        $term = new Terminal2gif();

        $ttygif = $input->getOption('ttygif');
        if ($ttygif !== null) {
            $term->setPath($ttygif);
        }

        try{
            try {
                $term->assertInstalled();
            }
            catch (\Exception $e) {
                if ($input->isInteractive() && $dialog->askConfirmation($output, 'Installation incomplete! Do you want to install?')) {
                    $term->install();
                    $term->assertInstalled();
                } else {
                    throw $e;
                }
            }
        }
        catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return;
        }

        $tempdir = $term->getTemp();
        $ttyrec = $tempdir . '/' . $name . '.ttyrec';

        $output->writeln('[1/3] Starting recording. Hit <info>CTRL+D</info> or type <info>exit</info> to stop recording');
        $input->isInteractive() && $dialog->askConfirmation($output, 'Hit enter to start recording.');
        $term->exec('ttyrec', $ttyrec);
        $output->writeln('     <info>Done recording!</info>');

        $output->writeln('[2/3] Generating gif images for each frames (this will clear your screen and play back your session!)');
        $input->isInteractive() && $dialog->askConfirmation($output, 'Hit enter to start generating image frames');
        $olddir = getcwd();
        chdir($tempdir);
        $term->exec('ttygif', $ttyrec);
        $output->writeln('      <info>Done generating images for each frame!</info>');

        $output->writeln('[3/3] Combining frames into looping gif');
        $term->exec('ttygif-concat', $name . '.gif');
        $output->writeln('      <info>Done!</info>');

        $tempname = realpath($name . '.gif');
        chdir($olddir);
        rename($tempname, $name . '.gif');

        unlink($ttyrec);
        rmdir($tempdir);

        passthru('xdg-open ' . escapeshellarg($name . '.gif'));
    }
}
