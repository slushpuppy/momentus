<?php


namespace Module\User;


use Module\FileSystem\Type\Image;

class Document extends Image
{
    const MAX_SIZE = "10000000";

    public static function getUrlFromPath(string $path) {
        return\Config\File::DOMAIN . \Config\File::SAVE_PATH_FOR_URL . '/image/profile/document/' . $path;
    }
    public function getUrl()
    {
        return self::getUrlFromPath($this->path);
    }
    /**
     * @return string
     */
    public function getSavePath()
    {
        return \Config\File::SAVE_PATH.'/image/profile/document/'.$this->getFilePath();
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->checksum.'.'.$this->extension;
    }
}