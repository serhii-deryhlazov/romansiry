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

            $firstImageTiny = preg_replace('/\.(\w+)$/', '_tiny.$1', $firstImage);
            $firstImageMedium = preg_replace('/\.(\w+)$/', '_medium.$1', $firstImage);

            if ($firstImage) {
                $items[] = [
                    'workId' => $folderName,
                    'src' => "$webBase/$folderName/$firstImage",
                    'srcTiny' => "$webBase/$folderName/$firstImageTiny",
                    'srcMedium' => "$webBase/$folderName/$firstImageMedium"
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
                    <img id="work<?= htmlspecialchars($item['workId']) ?>" src="<?= htmlspecialchars($item['srcTiny']) ?>" alt="Gallery <?= htmlspecialchars($item['workId']) ?>" />
                </a>
            </div>
            <script>
                new ProgressiveImageLoader(
                    'work<?= htmlspecialchars($item['workId']) ?>',
                    [
                        '<?= htmlspecialchars($item['srcMedium']) ?>',
                        '<?= htmlspecialchars($item['src']) ?>'
                    ]
                );
            </script>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No images found.</p>
    <?php endif; ?>
</main>

