<?php
    $baseDir = './assets/img/gallery';
    $webBase = './assets/img/gallery';

    // Get all work folders
    $folders = array_filter(glob($baseDir . '/*'), 'is_dir');

    if (empty($folders)) {
        die("No works found.");
    }

    // Pick random folder
    $randomFolderPath = $folders[array_rand($folders)];
    $workId = basename($randomFolderPath);

    // Get images in the folder
    $images = glob($randomFolderPath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

    if (empty($images)) {
        die("No images in selected work.");
    }

    // Pick random image
    $randomImage = $images[array_rand($images)];
    $imageWebPath = "$webBase/$workId/" . basename($randomImage);

    // Get context (title and description)
    $txtFiles = glob($randomFolderPath . '/*.txt');
    $title = 'Untitled Work';
    $description = '';

    if (count($txtFiles) === 1) {
        $title = pathinfo($txtFiles[0], PATHINFO_FILENAME);
        $description = file_get_contents($txtFiles[0]);
    }
?>

<main class="main-container" style="
    background-image: url('<?= htmlspecialchars($imageWebPath) ?>');
    background-position: <?= mt_rand(10, 90) ?>% <?= mt_rand(10, 90) ?>%;
    background-size: <?= mt_rand(50, 80) ?>%;
">
    <div class="description" style="right: <?= mt_rand(10, 80) ?>%; top: <?= mt_rand(10, 70) ?>%;">
        <h2><?= htmlspecialchars($title) ?></h2>
        <p><?= nl2br(htmlspecialchars($description)) ?></p>
    </div>
</main>
