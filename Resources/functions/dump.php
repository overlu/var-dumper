<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Mini\Context;
use Mini\Service\HttpMessage\Stream\SwooleStream;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('dump')) {
    /**
     * @author Nicolas Grekas <p@tchwork.com>
     */
    function dump($var, ...$moreVars)
    {
        VarDumper::dump($var);

        foreach ($moreVars as $v) {
            VarDumper::dump($v);
        }

        if (1 < func_num_args()) {
            return func_get_args();
        }

        return $var;
    }
}

if (!function_exists('dd')) {
    /**
     * @param ...$vars
     */
    function dd(...$vars)
    {
        foreach ($vars as $v) {
            VarDumper::dump($v);
        }
        throw new \Mini\Exception\DdException();
    }
}


if (!function_exists('debug')) {
    /**
     * @param $var
     * @param array $moreVars
     */
    function debug($var, ...$moreVars)
    {
        if (config('debugger.debug') && Context::has('IsInRequestEvent') && $swResponse = \response()->getSwooleResponse()) {
            Context::set('hasWriteContent', true);
            $cloner = new VarCloner();
            $dumper = new HtmlDumper();
            $dumper->setTheme(config('debugger.debug_theme', 'dark'));
            $output = fopen('php://memory', 'r+b');
            $dumper->dump($cloner->cloneVar($var)->withContext([SourceContextProvider::class => (new SourceContextProvider)->getContext()]), $output, [
                'fileLinkFormat' => "file://%f#L%l"
            ]);
            foreach ($moreVars as $moreVar) {
                $dumper->dump($cloner->cloneVar($moreVar)->withContext([SourceContextProvider::class => (new SourceContextProvider)->getContext()]), $output, [
                    'fileLinkFormat' => "file://%f#L%l"
                ]);
            }
            $output = stream_get_contents($output, -1, 0);
            $swResponse->header('content-type', 'text/html;charset=UTF-8', true);
            $swResponse->header('Server', 'Mini', true);
            $swResponse->write(new SwooleStream($output));
        }
    }
}

if (!function_exists('pp')) {
    /**
     * @param $var
     * @param array $moreVars
     */
    function pp($var, ...$moreVars)
    {
        if (config('debugger.debug') && Context::has('IsInRequestEvent') && $swResponse = \response()->getSwooleResponse()) {
            Context::set('hasWriteContent', true);
            $cloner = new VarCloner();
            $dumper = new HtmlDumper();
            $dumper->setTheme(config('debugger.debug_theme', 'dark'));
            $output = fopen('php://memory', 'r+b');
            $dumper->dump($cloner->cloneVar($var)->withContext([SourceContextProvider::class => (new SourceContextProvider)->getContext()]), $output, [
                'fileLinkFormat' => "file://%f#L%l"
            ]);
            foreach ($moreVars as $moreVar) {
                $dumper->dump($cloner->cloneVar($moreVar)->withContext([SourceContextProvider::class => (new SourceContextProvider)->getContext()]), $output, [
                    'fileLinkFormat' => "file://%f#L%l"
                ]);
            }
            $output = stream_get_contents($output, -1, 0);
            $swResponse->header('content-type', 'text/html;charset=UTF-8', true);
            $swResponse->header('Server', 'Mini', true);
            $swResponse->write(new SwooleStream($output));
        }
        throw new \Mini\Exception\DdException();
    }
}