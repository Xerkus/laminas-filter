<?php

/**
 * @see       https://github.com/laminas/laminas-filter for the canonical source repository
 * @copyright https://github.com/laminas/laminas-filter/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-filter/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Filter;

use Laminas\Filter\Compress as CompressFilter;

/**
 * @group      Laminas_Filter
 */
class CompressTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('bz2')) {
            $this->markTestSkipped('This filter is tested with the bz2 extension');
        }
    }

    public function tearDown()
    {
        if (file_exists(__DIR__ . '/../_files/compressed.bz2')) {
            unlink(__DIR__ . '/../_files/compressed.bz2');
        }
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBasicUsage()
    {
        $filter  = new CompressFilter('bz2');

        $text     = 'compress me';
        $compressed = $filter($text);
        $this->assertNotEquals($text, $compressed);

        $decompressed = $filter->decompress($compressed);
        $this->assertEquals($text, $decompressed);
    }

    /**
     * Setting Options
     *
     * @return void
     */
    public function testGetSetAdapterOptionsInConstructor()
    {
        $filter = new CompressFilter(array(
            'adapter' => 'bz2',
            'options' => array(
                'blocksize' => 6,
                'archive'   => 'test.txt',
            )
        ));

        $this->assertEquals(
            array('blocksize' => 6, 'archive' => 'test.txt'),
            $filter->getAdapterOptions()
        );

        $adapter = $filter->getAdapter();
        $this->assertEquals(6, $adapter->getBlocksize());
        $this->assertEquals('test.txt', $adapter->getArchive());
    }

    /**
     * Setting Options through constructor
     *
     * @return void
     */
    public function testGetSetAdapterOptions()
    {
        $filter = new CompressFilter('bz2');
        $filter->setAdapterOptions(array(
            'blocksize' => 6,
            'archive'   => 'test.txt',
        ));
        $this->assertEquals(
            array('blocksize' => 6, 'archive'   => 'test.txt'),
            $filter->getAdapterOptions()
        );
        $adapter = $filter->getAdapter();
        $this->assertEquals(6, $adapter->getBlocksize());
        $this->assertEquals('test.txt', $adapter->getArchive());
    }

    /**
     * Setting Blocksize
     *
     * @return void
     */
    public function testGetSetBlocksize()
    {
        $filter = new CompressFilter('bz2');
        $this->assertEquals(4, $filter->getBlocksize());
        $filter->setBlocksize(6);
        $this->assertEquals(6, $filter->getOptions('blocksize'));

        $this->setExpectedException('\Laminas\Filter\Exception\InvalidArgumentException', 'must be between');
        $filter->setBlocksize(15);
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testGetSetArchive()
    {
        $filter = new CompressFilter('bz2');
        $this->assertEquals(null, $filter->getArchive());
        $filter->setArchive('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getArchive());
        $this->assertEquals('Testfile.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testCompressToFile()
    {
        $filter   = new CompressFilter('bz2');
        $archive = __DIR__ . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter('compress me');
        $this->assertTrue($content);

        $filter2  = new CompressFilter('bz2');
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);

        $filter3 = new CompressFilter('bz2');
        $filter3->setArchive($archive);
        $content3 = $filter3->decompress(null);
        $this->assertEquals('compress me', $content3);
    }

    /**
     * testing toString
     *
     * @return void
     */
    public function testToString()
    {
        $filter = new CompressFilter('bz2');
        $this->assertEquals('Bz2', $filter->toString());
    }

    /**
     * testing getAdapter
     *
     * @return void
     */
    public function testGetAdapter()
    {
        $filter = new CompressFilter('bz2');
        $adapter = $filter->getAdapter();
        $this->assertTrue($adapter instanceof \Laminas\Filter\Compress\CompressionAlgorithmInterface);
        $this->assertEquals('Bz2', $filter->getAdapterName());
    }

    /**
     * Setting Adapter
     *
     * @return void
     */
    public function testSetAdapter()
    {
        if (!extension_loaded('zlib')) {
            $this->markTestSkipped('This filter is tested with the zlib extension');
        }

        $filter = new CompressFilter();
        $this->assertEquals('Gz', $filter->getAdapterName());


        $filter->setAdapter('\Laminas\Filter\Boolean');

        $this->setExpectedException('\Laminas\Filter\Exception\InvalidArgumentException', 'does not implement');
        $adapter = $filter->getAdapter();
    }

    /**
     * Decompress archiv
     *
     * @return void
     */
    public function testDecompressArchive()
    {
        $filter   = new CompressFilter('bz2');
        $archive = __DIR__ . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter('compress me');
        $this->assertTrue($content);

        $filter2  = new CompressFilter('bz2');
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);
    }

    /**
     * Setting invalid method
     *
     * @return void
     */
    public function testInvalidMethod()
    {
        $filter = new CompressFilter();

        $this->setExpectedException('\Laminas\Filter\Exception\BadMethodCallException', 'Unknown method');
        $filter->invalidMethod();
    }
}
