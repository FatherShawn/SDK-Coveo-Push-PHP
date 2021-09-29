<?php

/**
 * @file
 * Use to autoload needed classes without Composer.
 *
 * @param string $class The fully-qualified class name.
 *
 * @return void
 */

/*
 * Based on https://www.php-fig.org/psr/psr-4/examples/.
 */
spl_autoload_register(function ($class) {

  // project-specific namespace prefix.
    $prefix = 'Coveo\\Search\\SDK\\SDKPushPHP\\';

    // Base directory for the namespace prefix.
    $base_dir = __DIR__ . '/coveopush/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
      // No, move to the next registered autoloader.

      return;
    }

    // Get the relative class name.
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // eparators with directory separators in the relative class name, append
    // with .php.
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it.
  if (file_exists($file)) {
    require $file;
  }
});

spl_autoload_register(function ($class) {
    $prefixes = array(
        'Psr\\Log\\' => 'psr/log/Psr/Log/',
    );

    $base_dir = __DIR__.'/vendor/';

    foreach ($prefixes as $prefix => $subdir) {
        $len = strlen($prefix);
        if (0 !== strncmp($prefix, $class, $len)) {
            continue;
        }

        $relative_class = substr($class, $len);

        $file = $base_dir.$subdir.str_replace('\\', '/', $relative_class).'.php';

        if (file_exists($file)) {
            require $file;
        }
    }
});
