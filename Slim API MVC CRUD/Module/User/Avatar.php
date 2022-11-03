<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 31/3/2019
 * Time: 1:19 AM
 */

namespace Module\User;


use Module\FileSystem\Type\Image;

class Avatar extends Image
{
    const MAX_SIZE = "1000000";

    public static function getUrlFromPath(string $path) {
        return\Config\File::DOMAIN . \Config\File::SAVE_PATH_FOR_URL . '/image/profile/avatar/' . $path;
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
        return \Config\File::SAVE_PATH.'/image/profile/avatar/'.$this->getFilePath();
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->checksum.'.'.$this->extension;
    }

    /**
     * @return Avatar
     */
    public static function getDefault() {
        return static::createFromStaticFile(\Config\File::SAVE_PATH.'/image/profile/avatar/default.png');
    }
}