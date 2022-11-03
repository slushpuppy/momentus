<?php
require_once __DIR__.'/../autoload.php';

\Lib\Core\Cache::i()->setExEncrypted(md5("sfsfdfdfdfd"),"ssffdf",\Config\Login\Internal::TOKEN_VALIDITY_SEC);