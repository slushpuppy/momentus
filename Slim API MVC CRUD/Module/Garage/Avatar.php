<?php


namespace Module\Garage;


use Module\FileSystem\Type\Image;

class Avatar extends Image
{
    const MAX_SIZE = "1000000";

    public static function getUrlFromPath(string $path) {
        return \Config\File::DOMAIN . \Config\File::SAVE_PATH_FOR_URL . '/image/garage/avatar/' . $path;
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
        return \Config\File::SAVE_PATH.'/image/garage/avatar/'.$this->getFilePath();
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->checksum.'.'.$this->extension;
    }

    public static function getDefault() {
        return static::createFromStaticFile(\Config\File::SAVE_PATH.'/image/garage/avatar/default.png');
    }
}