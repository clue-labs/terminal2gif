# clue/terminal2gif [![Build Status](https://travis-ci.org/clue/terminal2gif.png?branch=master)](https://travis-ci.org/clue/terminal2gif)

Simple wrapper for ttyrec & ttygif to easily record your terminal session and create a looping gif for it:

![example dependency graph for clue/terminal2gif](http://www.lueck.tv/terminal2gif/terminal2gif.png)

## Usage

Once clue/terminal2gif is [installed](#install), you can use it via command line like this.

### terminal2gif record

The `record` command starts an interactive recording session that will guide
you through the progress of recording your terminal session and playing it back
in order to create a gif for you. Just run:

```bash
$ php terminal2gif.phar record demo.gif
```

*   It accepts an optional argument which is the path to write the resulting gif
    image to.
    (defaults to a random name in the current directory).

*   You may optionally pass an `--ttygif=[..]` option to set the path to your
    ttygif installation. Will check your `$PATH` otherwise.

## Install

You can grab a copy of clue/terminal2gif in either of the following ways.

### As a phar (recommended)

You can simply download a pre-compiled and ready-to-use version as a Phar
to any directory:

```bash
$ wget http://www.lueck.tv/terminal2gif/terminal2gif.phar
```


> If you prefer a global (system-wide) installation without having to type the `.phar` extension
each time, you may simply invoke:
> 
> ```bash
> $ chmod 0755 terminal2gif.phar
> $ sudo mv terminal2gif.phar /usr/local/bin/terminal2gif`
> ```
>
> You can verify everything works by running:
> 
> ```bash
> $ terminal2gif --version
> ```

#### Updating phar

There's no separate `update` procedure, simply overwrite the existing phar with the new version downloaded.

### Manual Installation from Source

This project requires PHP 5.3+, Composer:

```bash
$ sudo apt-get install php5-cli
$ git clone https://github.com/clue/terminal2gif.git
$ cd terminal2gif
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
```

> If you want to build the above mentioned `terminal2gif.phar` yourself, you have
to install [clue/phar-composer](https://github.com/clue/phar-composer#install)
and can simply invoke:
>
> ```bash
> $ php phar-composer.phar build ~/workspace/terminal2gif
> ```

#### Updating manually

```bash
$ git pull
$ php composer.phar install
```

## License

MIT

