<?php
// simple autoloading for classes
spl_autoload_register(function ($class) {
    $prefix = 'classes\\';
    $base_dir = __DIR__ . '/classes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// load required classes
require_once 'classes/Database.php';
require_once 'classes/StorageTypes/MySQL.php';
require_once 'classes/StorageTypes/contracts/Storage.php';
require_once 'classes/Repository/BaseRepository.php';
require_once 'classes/Repository/PostRepository.php';
require_once 'classes/Repository/UserRepository.php';

require_once 'classes/Repository/SettingRepository.php';
require_once 'classes/Post.php';

require_once 'classes/Setting.php';
