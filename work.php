<?php
    // Configuration
    $baseDir = './assets/img/gallery';
    $webBase = './assets/img/gallery';

    // Helpers
    function getContext($folderPath) {
        $txtFiles = glob($folderPath . '/*.txt');
        if (count($txtFiles) === 1) {
            $txtFile = $txtFiles[0];
            return [
                'title' => pathinfo($txtFile, PATHINFO_FILENAME),
                'description' => file_get_contents($txtFile)
            ];
        }
        return ['title' => 'Untitled Work', 'description' => ''];
    }

    function getImageList($folderPath) {
        $images = glob($folderPath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

        // Exclude images containing _medium or _tiny
        $images = array_filter($images, function ($path) {
            $filename = basename($path);
            return !str_contains($filename, '_medium') && !str_contains($filename, '_tiny');
        });

        usort($images, function ($a, $b) {
            return strnatcmp(basename($a), basename($b));
        });

        return array_values($images); // Re-index the array
    }

    // Extract workId
    $workId = $_GET['workId'] ?? null;
    $images = [];
    $context = ['title' => '', 'description' => ''];

    if ($workId) {
        $folderPath = realpath("$baseDir/$workId");
        if ($folderPath && strpos($folderPath, realpath($baseDir)) === 0 && is_dir($folderPath)) {
            $images = getImageList($folderPath);
            $context = getContext($folderPath);
        }
    }
?>

<main class="work-container">
    <?php if (empty($images)): ?>
        <p>No work selected or no images found.</p>
    <?php else: ?>
        <div class="work-left">
            <div class="image-preview">
                <img id="main-image" src="<?= "$webBase/$workId/" . basename($images[0]) ?>" alt="Main Preview" />
            </div>
            <div class="thumbnail-row" id="thumbnails">
                <?php foreach ($images as $index => $imgPath): 
                    $imgSrc = "$webBase/$workId/" . basename($imgPath);
                ?>
                    <img 
                        src="<?= $imgSrc ?>" 
                        alt="Thumbnail <?= $index ?>" 
                        class="<?= $index === 0 ? 'active' : '' ?>" 
                        onclick="document.getElementById('main-image').src='<?= $imgSrc ?>';
                                    Array.from(document.querySelectorAll('#thumbnails img')).forEach(i=>i.classList.remove('active'));
                                    this.classList.add('active');"
                    />
                <?php endforeach; ?>
            </div>
        </div>

        <div class="work-right">
            <h1 class="work-title"><?= htmlspecialchars($context['title']) ?></h1>
            <p class="work-description"><?= nl2br(htmlspecialchars($context['description'])) ?></p>
        </div>
    <?php endif; ?>
</main>