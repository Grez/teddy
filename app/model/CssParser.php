<?php

namespace Teddy\Model;

use Nette;
use Nette\Utils\Finder;


/**
 * @TODO: Image DataStream?
 */
class CssParser extends Nette\Object
{

    /** @var string */
    protected $wwwDir = '';

    /** @var array */
    protected $files = array();


    /**
     * @param string $wwwDir
     * @param array|string $files
     */
    public function __construct($wwwDir, $files)
    {
        $this->wwwDir = $wwwDir;
        $this->addFiles($files);
    }

    /**
     * @param array|string $files
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
        $version = filemtime($this->wwwDir . '/css');
        if (!file_exists($this->getTemp() . $version . '.css')) {
            $this->clean();
            $this->parse($version);
        }
        return '<link rel="stylesheet" media="all" href="/temp/css/' . $version . '.css">';
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

    /**
     * @param string $name of CSS file
     * @throws \Exception
     */
    protected function parse($name)
    {
        $parser = new \Less_Parser(array('compress' => true));
        foreach ($this->files as $file) {
            $parser->parseFile($this->wwwDir . '/css/' . $file);
        }

        $css = $parser->getCss();
        file_put_contents($this->getTemp() . '/' . $name . '.css', $css);
    }

}