<?php

namespace Teddy\Model;

use Nette;
use Nette\Utils\Finder;
use Teddy\ImgToDataUrl;


class CssParser extends Nette\Object
{

    /** @var string */
    protected $wwwDir = '';

    /** @var array */
    protected $files = array();

    /** @var bool */
    protected $convertToDataUrl = true;

    /** @var int */
    protected $version = 0;


    /**
     * @param string $wwwDir
     * @param array|string $files
     */
    public function __construct($wwwDir, $files, $convertToDataUrl = true)
    {
        $this->wwwDir = $wwwDir;
        $this->convertToDataUrl = $convertToDataUrl;
        $this->version = filemtime($this->wwwDir . '/css');
        $this->addFiles($files);
    }

    /**
     * @param array|string $files
     * @return null
     */
    public function addFiles($files)
    {
        if (!is_array($files)) {
            $this->files[] = $files;
        } else {
            $this->files = array_merge($this->files, $files);
        }
    }

    /**
     * @return string <link rel="stylesheet" ...>
     */
    public function getCssHeaderLink()
    {
        if (!file_exists($this->getTemp() . '/' . $this->version . '.css')) {
            $this->clean();
            $css = $this->parseLess();
            $filePath = $this->getTemp() . '/' . $this->version . '.css';

            file_put_contents($filePath, $css);
            if ($this->convertToDataUrl) {
                $imgToDataUrl = new ImgToDataUrl(new \SplFileInfo($filePath), $this->wwwDir);
                $css = $imgToDataUrl->convert();
            }
            file_put_contents($filePath, $css);
        }
        return '<link rel="stylesheet" media="all" href="/temp/css/' . $this->version . '.css">';
    }

    /**
     * @param string $name of CSS file
     * @throws \Exception
     */
    protected function parseLess()
    {
        $parser = new \Less_Parser(array('compress' => true));
        foreach ($this->files as $file) {
            $parser->parseFile($this->wwwDir . '/css/' . $file);
        }

        return $parser->getCss();
    }

    /**
     * @return string
     */
    protected function getTemp()
    {
        $temp = $this->wwwDir . '/temp';
        $css = $css = $this->wwwDir . '/temp/css';

        if (!is_writable($temp)) {
            @mkdir($temp, 0777);
        }

        if (!is_writable($css)) {
            @mkdir($css, 0777);
        }

        if (!is_writable($css)) {
            throw new Nette\DirectoryNotFoundException('Directory ' . $css . ' doesn\'t exist or has wrong CHMOD');
        }

        return $css;
    }

    /**
     * Cleans cache
     * @return null
     */
    protected function clean()
    {
        foreach (Finder::findFiles('*')->in($this->getTemp()) as $file) {
            @unlink($file->getPathname());
        }
    }

}