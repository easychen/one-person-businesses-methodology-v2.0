import { promises as fs } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';
import { fetch } from 'node-fetch';

const __dirname = dirname(fileURLToPath(import.meta.url));
const markdownDir = join(__dirname, 'src'); // Markdown文件所在目录
const imagesDir = join(__dirname, 'src/images'); // 图片下载目录

// 确保图片下载目录存在
await fs.mkdir(imagesDir, { recursive: true }).catch(console.error);

// 下载图片
async function downloadImage(url, filepath) {
  const response = await fetch(url,{
    headers: {
        'referer': 'https://ft07.com'
    }
  });
  const buffer = await response.buffer();
  await fs.writeFile(filepath, buffer);
  console.log('Downloaded: ' + filepath);
}

// 替换Markdown中的图片链接并下载图片
async function replaceImageLinksInFile(filePath) {
  let data = await fs.readFile(filePath, 'utf8');
  const regex = /!\[.*?\]\((https:\/\/res07\.ftqq\.com\/.*?\.png)\)/g;

  let match;
  while ((match = regex.exec(data)) !== null) {
    const imageUrl = match[1];
    const imageName = imageUrl.split('/').pop();
    const localImagePath = join(imagesDir, imageName);

    // 替换Markdown中的图片链接为本地路径
    data = data.replace(imageUrl, localImagePath);

    // 下载图片
    await downloadImage(imageUrl, localImagePath);
  }

  // 保存更新后的Markdown文件
  await fs.writeFile(filePath, data, 'utf8');
  console.log('Updated file: ' + filePath);
}

// 读取并处理每个Markdown文件
const files = await fs.readdir(markdownDir);
for (const file of files) {
  if (file.endsWith('.md')) {
    const filePath = join(markdownDir, file);
    await replaceImageLinksInFile(filePath);
  }
}
