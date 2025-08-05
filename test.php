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

    // Get images in the folder (exclude thumbnails)
    $images = glob($randomFolderPath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
    
    // Filter out thumbnail files
    $images = array_filter($images, function($image) {
        $filename = basename($image);
        return strpos($filename, '_tiny') === false && strpos($filename, '_medium') === false;
    });

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

    // Function to create thumbnail versions
    function createThumbnail($sourcePath, $width, $height, $suffix = '') {
        // Prevent creating thumbnails from thumbnails
        $filename = basename($sourcePath);
        if (strpos($filename, '_tiny') !== false || strpos($filename, '_medium') !== false) {
            return $sourcePath; // Don't create thumbnails from existing thumbnails
        }
        
        $pathInfo = pathinfo($sourcePath);
        $thumbPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . $suffix . '.' . $pathInfo['extension'];
        
        // Check if thumbnail already exists
        if (file_exists($thumbPath)) {
            return $thumbPath;
        }
        
        // Create thumbnail
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) return $sourcePath;
        
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Calculate aspect ratio
        $ratio = min($width / $sourceWidth, $height / $sourceHeight);
        $newWidth = (int)($sourceWidth * $ratio);
        $newHeight = (int)($sourceHeight * $ratio);
        
        // Create source image
        switch ($mimeType) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($sourcePath);
                break;
            default:
                return $sourcePath;
        }
        
        if (!$source) return $sourcePath;
        
        // Create thumbnail
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType == 'image/png' || $mimeType == 'image/gif') {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
            imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
        
        // Save thumbnail
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($thumb, $thumbPath, 50);
                break;
            case 'image/png':
                imagepng($thumb, $thumbPath, 9);
                break;
            case 'image/gif':
                imagegif($thumb, $thumbPath);
                break;
            case 'image/webp':
                imagewebp($thumb, $thumbPath, 50);
                break;
        }
        
        imagedestroy($source);
        imagedestroy($thumb);
        
        return $thumbPath;
    }

    // Generate thumbnail paths
    $tinyThumb = createThumbnail($randomImage, 50, 50, '_tiny');
    $mediumThumb = createThumbnail($randomImage, 200, 200, '_medium');
    
    // Convert file paths to web paths
    $tinyThumbWeb = str_replace($baseDir, $webBase, $tinyThumb);
    $mediumThumbWeb = str_replace($baseDir, $webBase, $mediumThumb);
?>
<style>
    .main-container {
        background-image: url('<?= htmlspecialchars($tinyThumbWeb) ?>');
        background-position: <?= mt_rand(10, 90) ?>% <?= mt_rand(10, 90) ?>%;
        background-size: <?= mt_rand(45, 69) ?>%;
    }

    .description {
        right: <?= mt_rand(10, 75) ?>%; 
        top: <?= mt_rand(20, 70) ?>%;
    }
</style>

<div class="description">
    <h2><?= htmlspecialchars($title) ?></h2>
    <p><?= nl2br(htmlspecialchars($description)) ?></p>
</div>
<main class="main-container" id="mainContainer">
</main>

<script>
    const container = document.getElementById('mainContainer');
    const imageUrls = [
        '<?= htmlspecialchars($mediumThumbWeb) ?>',
        '<?= htmlspecialchars($imageWebPath) ?>'
    ];

    let currentStage = 0;

    function loadNextStage() {
        if (currentStage >= imageUrls.length) return;
        
        const img = new Image();
        img.onload = () => {
            // Update background image
            container.style.backgroundImage = `url('${imageUrls[currentStage]}')`;
            
            // Reduce blur and scale
            if (currentStage === 0) {
                // Medium stage
                container.style.filter = 'blur(8px)';
                // container.style.transform = 'scale(1.05)';
            } else {
                // Final stage
                container.style.filter = 'blur(0px)';
                // container.style.transform = 'scale(1)';
            }
            
            currentStage++;
            
            // Load next stage after a delay
            if (currentStage < imageUrls.length) {
                setTimeout(loadNextStage, 800);
            }
        };
        
        img.src = imageUrls[currentStage];
    }

    // Start loading progression after a short delay
    setTimeout(loadNextStage, 400);
</script>