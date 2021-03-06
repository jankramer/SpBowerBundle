<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Tests\Bower;

use Sp\BowerBundle\Bower\Bower;
use Doctrine\Common\Cache\ArrayCache;
use Sp\BowerBundle\Bower\Configuration;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerFunctionalTest extends AbstractBowerTest
{
    protected $bower;
    protected $target;
    protected $filesystem;
    protected $cache;

    public function setUp()
    {
        if (!isset($_SERVER['BOWER_BIN'])) {
            $this->markTestSkipped('There is no BOWER_BIN environment variable.');
        }

        $this->target = sys_get_temp_dir() .'/bower_install_'. uniqid();
        $this->cache = new ArrayCache();
        $this->bower = new Bower($_SERVER['BOWER_BIN'], $this->cache, new EventDispatcher());
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->target);
    }

    public function testFileInstall()
    {
        $src = self::$fixturesDirectory .'/config';
        $configuration = new Configuration($src);
        $configuration->setJsonFile('component.json');
        $configuration->setEndpoint('https://bower.herokuapp.com');
        $configuration->setAssetDirectory($this->filesystem->makePathRelative($this->target .'/components', $src));
        $this->bower->install($configuration);

        $this->assertFileExists($this->target .'/components');
        $this->assertFileExists($this->target .'/components/jquery');
        $this->assertFileExists($this->target .'/components/jquery/jquery.js');
    }
}
