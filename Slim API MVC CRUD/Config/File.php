<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 15/11/2018
 * Time: 2:09 PM
 */
namespace Config;

class File
{
    const SAVE_PATH = Site::BASE_PATH."/upload";
    const SAVE_PATH_FOR_URL = "/upload";
    const ALLOWED_IMAGE_MIME_TYPES = [
        'jpeg',
        'jpg',
        'png',
        'gif'
    ];

    const ALLOWED_VIDEO_EXTENSIONS = [
        'webm',
        'mpg',
        'mp2',
        'mpeg',
        'mpe',
        'mpv',
        'ogg',
        'mp4',
        'm4p',
        'm4v'

    ];

    const DOMAIN = "https://rust.bike";
}