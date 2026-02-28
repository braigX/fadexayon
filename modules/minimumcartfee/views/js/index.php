<?php
// Full path to the current folder
$dir = __DIR__;

// Scan the directory
$files = scandir($dir);

// Filter out dot files and the script itself
$files = array_filter($files, function ($file) {
    return $file !== '.' && $file !== '..' && $file !== basename(__FILE__);
});

// Output simple HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Directory Listing: <?php echo basename($dir); ?></title>
    <style>
        body { font-family: sans-serif; padding: 2rem; background: #f9f9f9; }
        h1 { font-size: 1.5rem; margin-bottom: 1rem; }
        ul { list-style: none; padding: 0; }
        li { padding: 0.3rem 0; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>📁 Listing of: <?php echo basename($dir); ?></h1>
    <ul>
        <?php foreach ($files as $file): ?>
            <li>
                <a href="<?php echo htmlspecialchars($file); ?>">
                    <?php echo is_dir($file) ? '📁 ' : '📄 '; ?>
                    <?php echo htmlspecialchars($file); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
