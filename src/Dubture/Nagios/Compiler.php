<?php

/*
 * This file is part of the nagios-php utility.
 *
 * (c) Robert Gruendler <robert@dubture.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dubture\Nagios;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * Phar compiler for the nagios plugin utility.
 * 
 * Based on the Silex compiler.
 *
 * @see https://github.com/fabpot/Silex/blob/master/src/Silex/Compiler.php
 */
class Compiler
{

    /**
     * Compiles the nagios-php source code into one single Phar file.
     *
     * @param string $pharFile Name of the output Phar file
     */
    public function compile($pharFile = 'nagios.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $process = new Process('git log --pretty="%h %ci" -n1 HEAD');
        if ($process->run() > 0) {
            throw new \RuntimeException('The git binary cannot be found.');
        }

        $phar = new \Phar($pharFile, 0, 'nagios.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
                ->ignoreVCS(true)
                ->name('*.php')
                ->notName('Compiler.php')
                ->notName('plugin.php')
                ->in(__DIR__ . '/../..')
                ->in(__DIR__ . '/../../../vendor/composer')
                ->in(__DIR__ . '/../../../vendor/pimple/pimple/src')
                ->in(__DIR__ . '/../../../vendor/symfony/class-loader/Symfony/Component/ClassLoader')
                ->in(__DIR__ . '/../../../vendor/symfony/console/Symfony/Component/Console')
                ->in(__DIR__ . '/../../../vendor/symfony/finder/Symfony/Component/Finder')
                ->in(__DIR__ . '/../../../vendor/symfony/event-dispatcher/Symfony/Component/EventDispatcher')
                ->in(__DIR__ . '/../../../vendor/symfony/process/Symfony/Component/Process');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../../../vendor/autoload.php'));

        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        // $phar->compressFiles(\Phar::GZ);

        unset($phar);
    }

    protected function addFile($phar, $file, $strip = true)
    {
        
        $dir = str_replace("src/Dubture/Nagios", "", __DIR__);
        $path = str_replace($dir, '', $file->getRealPath());
        $content = file_get_contents($file);
        if ($strip) {
            $content = self::stripComments($content);
        }

        $phar->addFromString($path, $content);
    }

    protected function getStub()
    {
        return <<<'EOF'
    <?php
    Phar::mapPhar('nagios.phar');
    require_once 'phar://nagios.phar/vendor/autoload.php';

    __HALT_COMPILER();
EOF;
    }

    /**
     * Removes comments from a PHP source string.
     *
     * Based on Kernel::stripComments(), but keeps line numbers intact.
     *
     * @param string $source A PHP string
     *
     * @return string The PHP string with the comments removed
     */
    static public function stripComments($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }
}

