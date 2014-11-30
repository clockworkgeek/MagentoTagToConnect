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

class Clockworkgeek_TagToConnect_GitTag
{

    /**
     * Use the tag's hash everywhere in case of name collision with another ref
     * 
     * @var string
     */
    protected $hash;

    protected $name;

    protected $filenames;

    public function __construct($name)
    {
        $this->hash = escapeshellarg(exec('git show-ref -s --tags '.escapeshellarg($name)));
        $this->name = $name;
    }

    public function getFilenames()
    {
        if (is_null($this->filenames)) {
            exec('git ls-tree -r --name-only '.$this->hash, $this->filenames);
        }
        return $this->filenames;
    }

    public function getFileExists($filename)
    {
        return in_array($filename, $this->getFilenames());
    }

    public function getFileContents($filename)
    {
        exec(sprintf('git show %s:%s', $this->hash, escapeshellarg($filename)), $contents, $error);
        return $error ? false : $contents;
    }

    public function saveFileContents($filename, $destination)
    {
        exec(sprintf('git show %s:%s > %s', $this->hash, escapeshellarg($filename), escapeshellarg($destination)), $result, $error);
        return !$error;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getMessage()
    {
        $contents = shell_exec('git cat-file -p '.$this->hash);
        list(, $message) = explode("\n\n", $contents, 2);
        return trim ($message);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDatetime()
    {
        return exec('git log -1 --format="%ai" '.$this->hash);
    }

    /**
     * Parse version info from tag name
     * 
     * Response is in two parts; version number and stability.
     * Composer's "RC" and "patch" are not supported.
     * 
     * @return array
     */
    public function getVersion()
    {
        preg_match('/^v?(\d+\.\d+\.\d+)(?:-(dev|alpha|beta)\d*)?$/', $this->name, $matches);
        $version = @$matches[1];
        $suffix = @$matches[2];
        if ($suffix == 'dev') {
            $suffix = 'devel';
        }
        elseif (! $suffix) {
            $suffix = 'stable';
        }
        return array($version, $suffix);
    }
}
