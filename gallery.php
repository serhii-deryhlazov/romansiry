<?php
    $baseDir = './assets/img/gallery';
    $webBase = './assets/img/gallery';

    function getFirstImage($folderPath) {
        $images = glob($folderPath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        usort($images, function($a, $b) {
            return strnatcmp(basename($a), basename($b));
        });

        return !empty($images) ? basename($images[0]) : null;
    }

    $items = [];

    if (is_dir($baseDir)) {
        $folders = array_filter(glob($baseDir . '/*'), 'is_dir');

        foreach ($folders as $folderPath) {
            $folderName = basename($folderPath);
            $firstImage = getFirstImage($folderPath);

            if ($firstImage) {
                $items[] = [
                    'workId' => $folderName,
                    'src' => "$webBase/$folderName/$firstImage"
                ];
            }
        }
    }
?>
<main class="gallery-grid">
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
            <div class="gallery-item" >
                <a href="?page=work&workId=<?= htmlspecialchars($item['workId']) ?>">
                    <img src="<?= htmlspecialchars($item['src']) ?>" alt="Gallery <?= htmlspecialchars($item['workId']) ?>" />
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No images found.</p>
    <?php endif; ?>
</main>
