<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\File;
use Image;

/**
 * 文件上传下载
 *
 * @Resource("文件", uri="/api/file")
 */
class FileController extends Controller
{
    /**
     * 文件下载
     *
     * @Get("?id=58d1283604f80a6024000f27{&thumb=1}")
     * @Response(200)
     */
    public function download(Request $request)
    {
        $id = $request->input('id');
        $thumb = $request->input('thumb') == 1;

        $file = File::findOrFail($id);

        return response()->download(
            $file->realPath($thumb),
            ($thumb ? 'thumb_' : '') . $file->name
        );
    }

    /**
     * 上传文件
     *
     * @Get("upload")
     * @Request({
     *     "file": "file....",
     *     "tag": "tag",
     *     "title": "title",
     *     "comment": "comment",
     * })
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": {
     *         "id": "58d1dea704f80a5250007ab3",
     *         "tag": "hhhtag",
     *         "title": "kkktitle",
     *         "comment": "kkkcomment",
     *         "name": "TIM截图20170317154115.png",
     *         "mime": "image/png",
     *         "size": 35640
     *     },
     *     "errors":null,
     *     "code":0
     * })
     */
    public function upload(Request $request)
    {
        $tag = $request->input('tag');
        $title = $request->input('title');
        $comment = $request->input('comment');

        if (! $uploadFile = $request->file('file')) {
            return $this->ajax('error', '上传文件失败');
        }

        if (! $path = $uploadFile->store('upload/'.date('Y_m_d'))) {
            return $this->ajax('error', '储存文件失败');
        }

        $file = new File;

        $file->tag = $tag;
        $file->title = $title;
        $file->comment = $comment;
        $file->name = $uploadFile->getClientOriginalName();
        $file->path = $path;
        $file->mime = $uploadFile->getClientMimeType();
        $file->size = $uploadFile->getClientSize();

        if (! $file->save()) {
            return $this->ajax('error', '保存文件信息失败');
        }

        // 为图片生成缩略图
        if (stripos($file->mime, 'image/') === 0) {
            $img = Image::make($file->realPath())
                ->resize(400, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

            $img->save($file->realPath(true), 90);
        }

        return $this->ajax('ok', '上传成功', [
            'id'      => $file->id,
            'tag'     => $file->tag,
            'title'   => $file->title,
            'comment' => $file->comment,
            'name'    => $file->name,
            'mime'    => $file->mime,
            'size'    => $file->size,
        ]);
    }
}
