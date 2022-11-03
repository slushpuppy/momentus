<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/11/2018
 * Time: 8:55 PM
 */

namespace FileSystem;


use Module\FileSystem\File;

interface FileInterface
{

    /**
     * Get current object class name
     * @return string
     */
    public function FileGetClassHandler();

    /**
     * Get ID of the class name
     * @return int
     */
    public function FileGetClassHandlerId();

    /**
     * @param File $
     * @return void
     */
    public function FileAdd(File $file);

    /**
     * @param File[] $files
     * @return void
     */
    public function FileSetFiles(array $files);

    /**
     * @return File[]
     */
    public function FileGetAll();

}