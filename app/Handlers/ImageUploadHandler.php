<?php

namespace App\Handlers;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageUploadHandler
{
    protected $allow_ext = ['png', 'jpg', 'gif', 'jpeg'];

    public function save($file, $folder, $filePrefix, $maxWidth = false)
    {
        // 构建存储的文件夹规则，值如： uploads/images/avatars/2017/09/21/

        // 文件夹切割能让查找交率更高

        $folderName = "uploads/images/{$folder}/" . date('Ym/d', time());

        // 文件具体的存储路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
        // 值
        // 如： /home/vagrant/Code/larabbs3/public/uploads/images/avatars/20170921/
        $uploadPath = public_path() . '/' . $folderName;

        // 获取文件的后缀名，因图片从剪贴皮里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        // 拼接文件名，加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
        // 值如 1_1493521050_7BVc9v9ujP.png
        $filename = $filePrefix . '_' . time() . '_' . Str::random() . '.' . $extension;

        // 如果上传的不是图片将终止操作
        if (! in_array($extension, $this->allow_ext)) {
            return false;
        }

        // 将图片移动到我们的目标存储路径中
        $file->move($uploadPath, $filename);

        // 如果限制了图片宽度，就进行裁剪
        if ($maxWidth && $extension != 'gif') {
            $this->reduceSize($uploadPath . '/' . $filename, $maxWidth);
        }

        return [
            'path' => config('app.url') . "/{$folderName}/{$filename}",
        ];
    }

    public function reduceSize($filePath, $maxWidth)
    {
        // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($filePath);

        // 进行大小调整的操作
        $image->resize($maxWidth, null, function ($constraint) {
            // 设定宽度是 $maxWidth, 高度等比例缩放
            $constraint->aspectRatio();

            $constraint->upsize();
        });

        // 对图片进行裁剪后进行保存
        $image->save();
    }
}
