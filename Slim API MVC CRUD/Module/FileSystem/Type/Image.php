<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 16/11/2018
 * Time: 10:55 PM
 */

namespace Module\FileSystem\Type;


use Exception\NotImplemented;
use Module\Exception\FileSystem;
use Module\FileSystem\File;

class Image extends File
{

    public function getMimeData()
    {
        if ($this->extension != "") return $this->extension;

        if ($this->blob != null) {
            $data = &$this->blob;
        }
        if ($this->uploadFilePath != null) {
            $handle = fopen($this->uploadFilePath, "r");
            $data = fread($handle, 15);
            fclose($handle);
        }

        $this->extension = 'jpg';

        if (strpos($data,"\xff\xd8\xff") === 0) {
            $this->extension = 'jpg';
        }
        if (strpos($data,"\x89PNG\x0d\x0a") === 0) {
            $this->extension = 'png';
        }
        if (strpos($data,"GIF87a") === 0 or strpos($data,"GIF89a") === 0) {
            $this->extension = 'gif';
        }
        return $this->extension;
    }


    public function setExtensionType(string $extension) {
        throw new NotImplemented();
    }


    public function save(bool $updateIfFound = true)
    {
        if (\in_array($this->getMimeData(),\Config\File::ALLOWED_IMAGE_MIME_TYPES)) {
            return parent::save(); // TODO: Change the autogenerated stub
        }
        else
        {
           throw new FileSystem(FileSystem::INVALID_FILE_TYPE);
        }
    }

    public function getUrl()
    {
        return \Config\File::DOMAIN . \Config\File::SAVE_PATH . '/images/' . $this->path;
    }
}