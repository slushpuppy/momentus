<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 17/11/2018
 * Time: 1:38 AM
 */
namespace Module\Exception;


class FileSystem extends \Exception
{
    const FILE_TOO_LARGE="File too large";
    const INVALID_FILE_TYPE="Invalid File Type";
    const INVALID_SAVE_OBJECT="Invalid save Object";
    const INVALID_FILE_CONTROLLER_OBJECT="AbstractController does not have all necessary file system properties";
    const SAVE_ERROR = "File did not save";
}