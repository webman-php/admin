<?php

namespace plugin\admin\app\controller\common;

use Intervention\Image\ImageManagerStatic as Image;
use plugin\admin\app\controller\Base;
use support\Request;
use function base_path;
use function json;

/**
 * 上传
 */
class UploadController extends Base
{

    /**
     * 上传文件
     *
     * @param Request $request
     * @return \support\Response
     */
    public function file(Request $request)
    {
        $file = current($request->file());
        if (!$file || !$file->isValid()) {
            return $this->json(1, '未找到文件');
        }
        $img_exts = [
            'jpg',
            'jpeg',
            'png',
            'gif'
        ];
        if (in_array($file->getUploadExtension(), $img_exts)) {
            return $this->image($request);
        }
        $data = $this->base($request, '/upload/files/'.date('Ymd'));
        if ($data['code']) {
            return $this->json($data['code'], $data['message']);
        }
        return json(['code' => 0, 'message' => '上传成功', 'url' => $data['data']['src']]);
    }

    /**
     * 上传头像
     *
     * @param Request $request
     * @return \support\Response
     * @throws \Exception
     */
    public function avatar(Request $request)
    {
        $file = current($request->file());
        if ($file && $file->isValid()) {
            $ext = strtolower($file->getUploadExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'gif', 'png'])) {
                return json(['code' => 2, 'msg' => '仅支持 jpg jpeg gif png格式']);
            }
            $image = Image::make($file);
            $width = $image->width();
            $height = $image->height();
            $size = $width > $height ? $height : $width;
            $relative_path = 'upload/avatar/' . date('Ym');
            $real_path = base_path() . "/plugin/admin/public/$relative_path";
            if (!is_dir($real_path)) {
                mkdir($real_path, 0777, true);
            }
            $name = bin2hex(pack('Nn',time(), random_int(1, 65535)));
            $ext = $file->getUploadExtension();

            $image->crop($size, $size)->resize(300, 300);
            $path = base_path() . "/plugin/admin/public/$relative_path/$name.lg.$ext";
            $image->save($path);

            $image->resize(120, 120);
            $path = base_path() . "/plugin/admin/public/$relative_path/$name.md.$ext";
            $image->save($path);

            $image->resize(60, 60);
            $path = base_path() . "/plugin/admin/public/$relative_path/$name.$ext";
            $image->save($path);

            $image->resize(30, 30);
            $path = base_path() . "/plugin/admin/public/$relative_path/$name.sm.$ext";
            $image->save($path);

            return json([
                'code' => 0,
                'message' => '上传成功',
                'url' => "/app/admin/$relative_path/$name.$ext"
            ]);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }

    /**
     * 上传图片
     *
     * @param Request $request
     * @return \support\Response
     */
    public function image(Request $request)
    {
        $data = $this->base($request, '/upload/img/'.date('Ymd'));
        if ($data['code']) {
            return json(['code' => $data['code'], 'message' => $data['msg']]);
        }
        $realpath = $data['data']['realpath'];
        try {
            $img = Image::make($realpath);
            $max_height = 1170;
            $max_width = 1170;
            $width = $img->width();
            $height = $img->height();
            $ratio = 1;
            if ($height > $max_height || $width > $max_width) {
                $ratio = $width > $height ? $max_width / $width : $max_height / $height;
            }
            $img->resize($width*$ratio, $height*$ratio)->save($realpath);
        } catch (\Exception $e) {
            unlink($realpath);
            return json( [
                'code'  => 500,
                'message'  => '处理图片发生错误'
            ]);
        }
        return json( [
            'code'  => 0,
            'message'  => '上传成功',
            'url'      => $data['data']['src']
        ]);
    }

    /**
     * 获取上传数据
     *
     * @param Request $request
     * @param $relative_dir
     * @return array
     * @throws \Exception
     */
    protected function base(Request $request, $relative_dir)
    {
        $relative_dir = ltrim($relative_dir, '/');
        $file = current($request->file());
        if (!$file || !$file->isValid()) {
            return ['code' => 400, 'message' => '未找到上传文件'];
        }

        $base_dir = base_path() . '/plugin/admin/public/';
        $full_dir = $base_dir . $relative_dir;
        if (!is_dir($full_dir)) {
            mkdir($full_dir, 0777, true);
        }

        $ext = strtolower($file->getUploadExtension());
        $ext_forbidden_map = ['php', 'php3', 'php5', 'css', 'js', 'html', 'htm', 'asp', 'jsp'];
        if (in_array($ext, $ext_forbidden_map)) {
            return ['code' => 400, 'message' => '不支持该格式的文件上传'];
        }

        $relative_path = $relative_dir . '/' . bin2hex(pack('Nn',time(), random_int(1, 65535))) . ".$ext";
        $full_path = $base_dir . $relative_path;
        $file_size = $file->getSize();
        $file_name = $file->getUploadName();
        $file->move($full_path);
        return [
            'code' => 0,
            'msg'  => '上传成功',
            'data' => [
                'src'      => "/app/admin/$relative_path",
                'name'     => $file_name,
                'realpath' => $full_path,
                'size'     => $this->formatSize($file_size)
            ]
        ];
    }

    /**
     * 格式化文件大小
     *
     * @param $file_size
     * @return string
     */
    protected function formatSize($file_size) {
        $size = sprintf("%u", $file_size);
        if($size == 0) {
            return("0 Bytes");
        }
        $sizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizename[$i];
    }

}
