<?php

namespace Clue\Terminal2gif;

use Symfony\Component\Process\ExecutableFinder;

class Terminal2gif
{
    private $paths = array();

    public function assertInstalled()
    {
        $finder = new ExecutableFinder();

        $bins = array('ttygif', 'ttygif-concat', 'import', 'convert', 'ttyrec');

        foreach ($bins as $bin) {
            $this->getBin($bin);
        }
    }

    public function getBin($bin)
    {
        $finder = new ExecutableFinder();

        if ($bin === 'ttygif-concat' && $this->paths) {
            $file = $this->isMac() ? 'concat_osx.sh' : 'concat.sh';
            foreach ($this->paths as $path) {
                if (is_executable($path = $path . $file)) {
                    return $path;
                }
            }
        }

        $ret = $finder->find($bin, null, $this->paths);
        if ($ret === null) {
            throw new \Exception('Unable to locate "' . $bin . '". Looks like your installation is incomplete!');
        }

        return $ret;
    }

    public function setPath($path)
    {
        $path = rtrim($path, '/') . '/';
        $this->paths = array($path);
    }

    public function getTemp()
    {
        $tempdir = sys_get_temp_dir() . '/' . uniqid('terminal2gif-');
        if (mkdir($tempdir, 0700, true) === false) {
            throw new \Exception('Unable to create temporary directory "' . $tempdir . '"');
        }

        return $tempdir;
    }

    public function exec($bin, $arg1)
    {
        $cmd = $this->getBin($bin);

        $args = func_get_args();
        array_shift($args);

        foreach ($args as $arg) {
            if (is_array($arg)) {
                $cmd .= ' ' . $arg[0];
            } else {
                $cmd .= ' ' . escapeshellarg($arg);
            }
        }

        echo $cmd . PHP_EOL;

        passthru($cmd);
    }

    public function isMac()
    {
        return (PHP_OS === 'Darwin');
    }

    public function install()
    {
        $cmd = 'sudo apt-get install imagemagick git gcc ttyrec';
        echo '[1/4] Running: "' . $cmd . '"' . PHP_EOL;
        passthru($cmd);

        // as per http://choly.ca/ttygif/ :
        $cmd = 'git clone https://github.com/icholy/ttygif.git';
        echo '[2/4] Cloning icholy/ttygit' . PHP_EOL;
        passthru($cmd);

        echo '[3/4] Running make' . PHP_EOL;
        $olddir = getcwd();
        chdir('ttygif');
        passthru('make');
        $path = rtrim(getcwd(), '/') . '/';
        chdir($olddir);

        echo '[4/4] Symlinking /usr/local/bin' . PHP_EOL;
        $this->exec('sudo', 'ln', '-sf', $path . 'ttygif', '/usr/local/bin/ttygif');
        $this->exec('sudo', 'ln', '-sf', $path . ($this->isMac() ? 'concat_osx.sh' : 'concat.sh'), '/usr/local/bin/ttygif-concat');
        $this->setPath($path);

        echo 'Installation complete!' . PHP_EOL . PHP_EOL;
    }
}
