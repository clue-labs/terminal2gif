<?php

use Clue\Terminal2gif\Ttyrecord;
class TtyrecordTest extends TestCase
{
    public function testFixedFree()
    {
        $ttyrec = new Ttyrecord(__DIR__ . '/fixtures/free.ttyrec');

        $this->assertEquals(1379666634, $ttyrec->getDateStart()->getTimestamp());
        $this->assertEquals(1379666645, $ttyrec->getDateLast()->getTimestamp());
        $this->assertEquals(11.7, $ttyrec->getDuration(), null, 0.1);
        $this->assertEquals(328, $ttyrec->getLength());
        $this->assertEquals(53, $ttyrec->getNumberOfChunks());

        $space = ' ';
        $expected = <<<EOF
$ echo demo for clue/terminal2gif
demo for clue/terminal2gif
$ free
             total       used       free     shared    buffers     cached
Mem:       4490528    4234060     256468          0      57328     572296
-/+ buffers/cache:    3604436     886092
Swap:      4192252     100424    4091828
$ echo bye!
bye!
$$space

EOF;
        $expected = str_replace("\n", "\r\n", str_replace("\r\n", "\n", $expected));
        $this->assertEquals($expected, $ttyrec->getOutput());
        $this->assertEquals($expected, $ttyrec->getOutputSafe());

        $this->assertEquals(11.7, array_sum($ttyrec->getAllDelays()), null, 0.1);
        $this->assertInternalType('array', $ttyrec->getAllChunks());
        $this->assertInternalType('array', $ttyrec->getAllDates());
    }

    public function testFixedDate()
    {
        $ttyrec = new Ttyrecord(__DIR__ . '/fixtures/date.ttyrec');

        $this->assertEquals(1379670450, $ttyrec->getDateStart()->getTimestamp());
        $this->assertEquals(1379670456, $ttyrec->getDateLast()->getTimestamp());
        $this->assertEquals(6.5, $ttyrec->getDuration(), null, 0.1);
        $this->assertEquals(47, $ttyrec->getLength());
        $this->assertEquals(13, $ttyrec->getNumberOfChunks());

        $expected = <<<EOF
$ date
Fri Sep 20 11:47:33 CEST 2013
$ exit

EOF;
        $expected = str_replace("\n", "\r\n", str_replace("\r\n", "\n", $expected));
        $this->assertEquals($expected, $ttyrec->getOutput());
        $this->assertEquals($expected, $ttyrec->getOutputSafe());

        $this->assertEquals(6.5, array_sum($ttyrec->getAllDelays()), null, 0.1);
        $this->assertInternalType('array', $ttyrec->getAllChunks());
        $this->assertInternalType('array', $ttyrec->getAllDates());
    }

    /**
     * @expectedException Exception
     */
    public function testEmptyFails()
    {
        new Ttyrecord(__DIR__ . '/fixtures/empty');
    }

    /**
     * @expectedException Exception
     */
    public function testNonExistantFails()
    {
        new Ttyrecord(__DIR__ . '/fixtures/does-not-exist');
    }
}
