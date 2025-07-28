<?php
header('Content-Type: application/json');

$baseDir = __DIR__ . '/../assets/img/gallery';
$webBase = '/assets/img/gallery';

function getContext($folderPath) {
    $txtFiles = glob($folderPath . '/*.txt');
    
    if (count($txtFiles) === 1) {
        $txtFile = $txtFiles[0];
        $title = pathinfo($txtFile, PATHINFO_FILENAME);
        $description = file_get_contents($txtFile);

        return [
            'title' => $title,
            'description' => $description
        ];
    }

    // Default fallback if not exactly one .txt file
    return [
        'title' => 'Untitled Work',
        'description' => ''
    ];
}

function getImageList($folderPath) {
    $images = glob($folderPath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
    usort($images, function($a, $b) {
        return strnatcmp(basename($a), basename($b));
    });
    return $images;
}

$method = $_GET['method'] ?? '';
$workId = $_GET['workId'] ?? null;  // changed here to workId

if ($method === 'first-images') {
    $result = [];

    if (is_dir($baseDir)) {
        $folders = array_filter(glob($baseDir . '/*'), 'is_dir');

        foreach ($folders as $folderPath) {
            $folderName = basename($folderPath);
            $images = getImageList($folderPath);

            if (!empty($images)) {
                $firstImage = basename($images[0]);
                $result[] = "$webBase/$folderName/$firstImage";
            }
        }
    }

    echo json_encode($result);
    exit;
}

if ($method === 'images-by-id' && $workId !== null) {
    $folderPath = realpath("$baseDir/$workId");

    // Security check: ensure folderPath is within baseDir
    if ($folderPath && strpos($folderPath, realpath($baseDir)) === 0 && is_dir($folderPath)) {
        $images = getImageList($folderPath);
        $context = getContext($folderPath);

        $imagePaths = array_map(function ($path) use ($workId, $webBase) {
            return "$webBase/$workId/" . basename($path);
        }, $images);

        echo json_encode([
            'images' => $imagePaths,
            'context' => $context
        ]);
        exit;
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Work not found"]);
        exit;
    }
}

http_response_code(400);
echo json_encode(["error" => "Invalid or missing method"]);
exit;
