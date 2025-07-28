<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Empty Page</title>
    <link rel="stylesheet" href="./assets/css/main.css" />
    <link rel="stylesheet" href="./assets/css/gallery.css" />
    <link rel="stylesheet" href="./assets/css/work.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gruppo&family=Quicksand:wght@300..700&family=Reenie+Beanie&family=Syncopate:wght@400;700&display=swap" rel="stylesheet">
</head>

<?php

    $allowedPages = ['gallery', 'about', 'work'];
    $page = $_GET['page'] ?? null;

?>

<body>

    <header>
        <nav class="header-nav">
            <div class="logo"><a href="/">Roman Siryi</a></div>
            <ul class="menu">
                <li>
                    <a class="<?= $page === 'gallery' ? 'active' : '' ?>" href="?page=gallery">Gallery</a>
                </li>
                <li>
                    <a class="<?= $page === 'about' ? 'active' : '' ?>" href="?page=about">About</a>
                </li>
            </ul>
        </nav>
    </header>

    <?php

        if ($page && in_array($page, $allowedPages)) {
            $filepath = __DIR__ . "/{$page}.php";
            if (file_exists($filepath)) {
                include($filepath);
            } else {
                echo "<p>Page not found.</p>";
            }
        } else {
            include(__DIR__ . "/main.php");
        }

    ?>

    <script src="./assets/js/main.js"></script>
</body>

</html>