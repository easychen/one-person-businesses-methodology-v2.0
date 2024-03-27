#!/bin/bash

# 设置mdbook的源文件目录路径
mdbook_src_dir="src"

# 初始化字符数变量
total_chars=0

# 遍历目录下的所有.md文件并统计字符
for file in $(find "$mdbook_src_dir" -name '*.md'); do
  chars=$(wc -m <"$file")
  total_chars=$((total_chars + chars))
done

# 输出总字符数
echo "Total characters: $total_chars"
