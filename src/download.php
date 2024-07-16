<?php

$markdownDir = './'; // Markdown文件所在目录
$imagesDir = 'images'; // 图片下载目录

// 确保图片下载目录存在
if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0777, true);
}

// 使用cURL下载图片
function downloadImage($url, $filepath)
{
    $ch = curl_init($url);
    $fp = fopen($filepath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_REFERER, 'https://ft07.com'); // 设置Referer头部
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    echo "Downloaded: $filepath\n";
}

// 替换Markdown中的图片链接并下载图片
function replaceImageLinksInFile($filePath, $imagesDir)
{
    $data = file_get_contents($filePath);
    $regex = '/!\[.*?\]\((https:\/\/r2\.ft07\.com\/.*?\.(jpg|png|gif|bmp|webp))\)/';

    preg_match_all($regex, $data, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $imageUrl = $match[1];
        $imageName = basename($imageUrl);
        $localImagePath = $imagesDir . '/' . $imageName;

        // 替换Markdown中的图片链接为本地路径
        $data = str_replace($imageUrl, $localImagePath, $data);

        // 下载图片
        downloadImage($imageUrl, $localImagePath);
    }

    // 保存更新后的Markdown文件
    file_put_contents($filePath, $data);
    echo "Updated file: $filePath\n";
}

// 读取并处理每个Markdown文件
$files = scandir($markdownDir);
foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
        $filePath = $markdownDir . '/' . $file;
        replaceImageLinksInFile($filePath, $imagesDir);
    }
}

echo "Done.\n";
