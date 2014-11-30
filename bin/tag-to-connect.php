<?php
/*
 * The MIT License (MIT)
 * 
 * Copyright (c) 2014 Daniel Deady
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

@include_once __DIR__.'/../vendor/autoload.php';

// these consts normally defined in Mage.php
if (! defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if (! defined('PS')) define('PS', PATH_SEPARATOR);

function clockworkgeekErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (error_reporting() & $errno) {
        echo $errstr, PHP_EOL;
    }
}
set_error_handler('clockworkgeekErrorHandler');
// TODO Set error_reporting() based on -q and -v options

$packageName = @$argv[1];
$tagName = @$argv[2];
$exit = 0;

if (! isset($packageName)) {
    trigger_error('Package name is required', E_USER_ERROR);
    $exit++;
}
if (! isset($tagName)) {
    trigger_error('Tag name is required', E_USER_ERROR);
    $exit++;
}
if ($exit) exit($exit);

$tag = new Clockworkgeek_TagToConnect_GitTag($tagName);
$package = new Clockworkgeek_TagToConnect_Package($tag);
$package->setName($packageName);
$package->loadGitTag();
$package->loadComposerJson();

echo 'Packaging var/connect/', $package->getReleaseFilename(), ".tgz...\n";
$package->save('var/connect');

foreach ($package->getErrors() as $error) {
    trigger_error($error, E_USER_ERROR);
    $exit++;
}
exit($exit);
