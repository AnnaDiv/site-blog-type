<?php
/**
 * An example of a project-specific implementation.
 *
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \Foo\Bar\Baz\Qux class
 * from /path/to/project/src/Baz/Qux.php:
 *
 *      new \Foo\Bar\Baz\Qux;
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {
    // An associative array of namespace prefixes and their base directories
    $prefixes = [
        'App\\' => __DIR__ . '/../src/',
        'PHPMailer\\PHPMailer\\' => __DIR__ . '/../PHPMailer/src/',
        'Firebase\\JWT' => __DIR__ . '/../php-jwt/src/'
        // Add more libraries here as needed
    ];

    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue; // try the next prefix
        }

        // Get the relative class name
        $relative_class = substr($class, $len);

        // Replace namespace separators with directory separators, then append with .php
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        // If the file exists, require it
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
