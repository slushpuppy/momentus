<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 31/3/2019
 * Time: 1:19 AM
 */

namespace Module\Social\Group;


use Module\FileSystem\Type\Image;

class Avatar extends Image
{
    const MAX_SIZE = "1000000";

    public static function getDefault()
    {
        return static::createFromStaticFile(\Config\File::SAVE_PATH.'/image/social/group/avatar/default.png');
    }

    public static function getUrlFromPath(string $path) {
        return \Config\File::DOMAIN . \Config\File::SAVE_PATH . '/images/' . $path;
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
        return \Config\File::SAVE_PATH.'/image/social/group/avatar/'.$this->getFilePath();
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->checksum.'.'.$this->extension;
    }
}