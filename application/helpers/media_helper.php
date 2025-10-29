<?php

// combine all chunks
// no exception handling included here - you may wish to incorporate that
function combineChunks($chunks, $targetFile)
{
    // open target file handle
    $handle = fopen($targetFile, 'a+');

    foreach ($chunks as $file) {
        fwrite($handle, file_get_contents($file));
    }

    // you may need to do some checks to see if file 
    // is matching the original (e.g. by comparing file size)

    // after all are done delete the chunks
    foreach ($chunks as $file) {
        @unlink($file);
    }

    // close the file handle
    fclose($handle);
}

function generate_thumbnails($srcFile)
{
    $CI = &get_instance();
    $CI->load->library('image_lib');


    $fileInfo = pathinfo($srcFile);
    // $fileName = $fileInfo['basename'];
    $destPath = $fileInfo['dirname'] . "/thumbnails";

    $extension = strtolower($fileInfo['extension']);

    if (!file_exists($destPath)) {
        if (!mkdir($destPath, 0744, true)) {
            chrome_log("failing in mkdir");
            return false;
        }
    }


    $thumbPath = $srcFile;

    $tinyHeight = $CI->config->item('image_tiny_height');
    $mainHeight = $CI->config->item('image_main_height');

    $mainPath = $destPath . '/main_' . $fileInfo['filename'] . ".png";

    if (in_array($extension, $CI->config->item('allowed_video_types'))) {
        //$thumbPath =  $destPath . '/tiny_' . $fileInfo['filename'] . ".jpg";
        $absPath = $CI->config->item('ffmpeg');

        $command = $absPath . " -ss 00:00:01 -i $srcFile -vframes 1 -vf scale=-2:$mainHeight -f image2 -y $mainPath";

        @exec($command, $output, $return);
        if ($return != 0) {
            return false;
        }
    } else if (in_array($extension, $CI->config->item('allowed_image_types'))) {
        //$thumbPath =  $destPath . '/tiny_' . $fileName;
        $config['image_library'] = 'gd2';
        $config['source_image'] = $srcFile;
        $config['new_image'] = $mainPath;
        $config['create_thumb'] = false;
        $config['maintain_ratio'] = true;
        $config['quality'] = 100;

        $config['height'] = $mainHeight;
        $CI->image_lib->initialize($config);

        if (!$CI->image_lib->resize()) {
            return false;
        }
    } else {
        return false;
    }

    $thumbPath =  $destPath . '/tiny_' . $fileInfo['filename'] . ".png";
    if (file_exists($mainPath)) {
        $CI->image_lib->clear();

        $config['image_library'] = 'gd2';
        $config['source_image'] = $mainPath;
        $config['new_image'] = $thumbPath;
        $config['create_thumb'] = false;
        $config['maintain_ratio'] = true;
        $config['quality'] = 100;

        $CI->config->item('allowed_video_types');
        $config['height'] = $tinyHeight;
        $CI->image_lib->initialize($config);

        //failed to create thumbnail
        if (!$CI->image_lib->resize()) {
            return false;
        }
    }


    $ret['tiny'] = substr($thumbPath, 1);
    $ret['main'] = substr($mainPath, 1);

    return $ret;
}

function generate_preview_image($movie, $outfile, $width = 0, $height = 0)
{
    $CI = &get_instance();
    if (empty($movie) || empty($outfile)) {
        return false;
    }
    $size = "";
    if ($width > 0 && $height > 0) {
        $size = "-s " . $width . "x" . $height;
    }
    $absPath = $CI->config->item('ffmpeg');

    $command = $absPath . " -ss 00:00:00 -i $movie -vframes 1 -vf scale=-2:360 -f image2 -y  $outfile";
    @exec($command, $output, $return);


    if ($return == 0) {
        return true;
    } else {
        return false;
    }
}

if (!function_exists("generate_preview_image")) {
    /**
     * 生成视频文件的预览图
     *
     * @param object $movie
     * @param object $outfile
     * @param object $width [optional]
     * @param object $height [optional]
     * @return
     */
    function generate_preview_image($movie, $outfile, $width = 0, $height = 0)
    {
        $CI = &get_instance();
        if (empty($movie) || empty($outfile)) {
            return false;
        }
        $size = "";
        if ($width > 0 && $height > 0) {
            $size = "-s " . $width . "x" . $height;
        }
        $absPath = $CI->config->item('ffmpeg');

        $command = $absPath . " -ss 00:00:00 -i $movie -vframes 1 -vf scale=-2:360 -f image2 -y  $outfile";
        @exec($command, $output, $return);


        if ($return == 0) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('generate_preview_movie')) {
    /**
     * 生成预览视频
     * @param object $movie
     * @param object $outfile
     * @param object $width [optional]
     * @param object $height [optional]
     * @return
     */
    function generate_preview_movie($movie, $movie_ext, $outfile, $width = 0, $height = 0)
    {
        $CI = &get_instance();
        if (empty($movie) || empty($outfile)) {
            return false;
        }
        $size = "";
        if ($width > 0 && $height > 0) {
            $size = '-s ' . $width . 'x' . $height;
        }
        $absPath = $CI->config->item('ffmpeg');
        /*	if($movie_ext == 'MKV') {
                $command = $absPath.' -i '.$movie.' -y -ss 3 -ab 128 -ac 2 -ar 22050 -qscale 4 '.$size.' '.$outfile;
            }else {
                $command = $absPath.' -i '.$movie.' -y -ss 3 -ab 128 -ar 22050 -b 500 '.$size.' '.$outfile;
            }
        */
        $command = $absPath . ' -i ' . $movie . ' -vcodec libx264 -vprofile baseline -preset slow -b:v 250k -maxrate 250k -bufsize 500k -vf scale=-2:360 ' . $outfile;

        exec($command, $output, $return);
        if ($return == 0) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('generate_client_movie')) {
    /**
     * 0 = 90 CounterCLockwise和垂直翻转（默认）1 = 90 Clockwise 2 = 90 CounterClockwise 3 = 90Clockwise和垂直翻转
     *
     * @param object $movie
     * @param object $outfile
     * @param object $width [optional]
     * @param object $height [optional]
     * @param object $transpose [optional] 旋转
     * @return
     */
    function generate_client_movie($movie, $outfile, $width = 0, $height = 0, $t_width = 0, $t_height = 0, $transpose = -1, $is_fit = false)
    {
        if (empty($movie) || empty($outfile)) {
            return false;
        }
        $CI = &get_instance();

        $size = "";
        $vcodec = "";
        if (!isset($info['fps']) || $info['fps'] > 30) {
            $info['fps'] = 30;
        }
        $info = get_movie_info($movie);
        if ($info && $transpose == 1) {
            $width = $info['width'];
            $height = $info['height'];
            //当竖屏高度超过1080时需要等比压缩
            if ($width > 1080) {
                $height = resize_width(ceil(($height * 1080) / $width));
                $width = 1080;
            }
            $size = '-s ' . $height . 'x' . $width;
            if ($info) {
                $vcodec = "-b " . $info['bitrate'] . "k";
                $vcodec .= " -r " . $info['fps'];
            }
        } else {
            $vcodec = "-vcodec copy";
        }
        /**
        if (!isset($info['fps']) || $info['fps'] > 30){
            $info['fps'] = 30;
        }
        if ($info) {
            $vcodec = "-b ".$info['bitrate']."k";
            $vcodec .= " -r ".$info['fps'];
        }*/

        $trans = "";
        $absPath = $CI->config->item('ffmpeg');
        $t_height = 2 * $t_height;  //template高度
        $t_width = 2 * $t_width;    //template宽度
        if (intval($transpose) >= 0) {
            if ($is_fit) {
                $video_width = $info['width'];
                $video_height = $info['height'];
                if ($video_width < $t_width && $video_height < $t_height) {
                    $black_w = (intval(($t_height - $video_height) / 2) * 2) / 2;  //左右黑边高度
                    $black_h = (intval(($t_width - $video_width) / 2) * 2) / 2;    //上下黑边高度
                    $trans = ' -vf "scale=' . $video_width . ':' . $video_height . ',pad=' . $t_width . ':' . $t_height . ':' . $black_h . ':' . $black_w . ':black,transpose=' . $transpose . '"';
                } else {
                    //原来的视频高度作为现在视频的宽度，重新定义视频的宽和高
                    $video_width = $height;                         //转成后视频宽度等于原来视频的高度
                    $video_height = ceil($height * $height / $width);   //转成后视频高度
                    if ($t_width / $t_height < $video_width / $video_height) {
                        $v_width = $t_width;                                      //转成厚视频宽度
                        $v_height = ceil($t_width * $video_height / $video_width);    //转成后视频高度
                        $black_w = 0;
                        $black_h = (intval(($t_height - $v_height) / 2) * 2) / 2; //上下黑边高度
                        $trans = ' -vf "scale=' . $v_width . ':' . $v_height . ',pad=' . $t_width . ':' . $t_height . ':' . $black_w . ':' . $black_h . ':black,transpose=' . $transpose . '"';
                    } else {
                        $v_width = ceil($video_width * $t_height / $video_height);   //转成厚视频宽度
                        $v_height = $t_height;                                   //转成后视频高度
                        $black_w = (intval(($t_width - $v_width) / 2) * 2) / 2;  //左右黑边高度
                        $black_h = 0;
                        $trans = ' -vf "scale=' . $v_width . ':' . $v_height . ',pad=' . $t_width . ':' . $t_height . ':' . $black_w . ':' . $black_h . ':black,transpose=' . $transpose . '"';
                    }
                }
                $command = $absPath . ' -i ' . $movie . ' -y -acodec copy ' . $vcodec . ' ' . $trans . ' ' . $outfile;
            } else {
                $trans = ' -vf "transpose=' . $transpose . '"';
                $command = $absPath . ' -i ' . $movie . ' -y -acodec copy ' . $size . ' ' . $vcodec . ' ' . $trans . ' ' . $outfile;
            }
        } else {
            $width = $info['width'];
            $height = $info['height'];
            $size = '-s ' . $width . 'x' . $height;
            $command = $absPath . ' -i ' . $movie . ' -y -acodec copy ' . $vcodec . ' ' . $size . ' ' . $outfile;
        }
        exec($command, $output, $return);
        if ($return == 0) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_movie_info')) {
    function get_movie_info($movie)
    {
        if (empty($movie) || !file_exists($movie)) {
            return false;
        }

        $command = "/usr/bin/ffprobe -v error -of json  -show_streams $movie 2>&1";;
        ob_start();
        passthru($command);
        $info = ob_get_contents();
        ob_end_clean();

        $info_obj = json_decode($info);
        $data = [];

        if ($info_obj && isset($info_obj->streams)) {

            $streams = $info_obj->streams;
            foreach ($streams as $stream) {
                if ($stream->codec_type == "video") {
                    $data['seconds'] = $stream->duration;
                    $data['play_time'] = $data['seconds'];
                    $data['start'] = $stream->start_time;
                    $data['bitrate'] = $stream->bit_rate;
                    $data['vcodec'] = $stream->codec_name;
                    $data['pix_fmt'] = $stream->pix_fmt;
                    $data['width'] = $stream->width;
                    $data['height'] = $stream->height;
                    $data['size'] = filesize($movie);
                }
            }
        }
        return $data;
    }
    function get_movie_info_org($movie)
    {
        if (empty($movie) || !file_exists($movie)) {
            return false;
        }


        $CI = &get_instance();
        $absPath = $CI->config->item('ffmpeg');
        $command = "$absPath -i $movie 2>&1";
        ob_start();
        passthru($command);
        $info = ob_get_contents();
        ob_end_clean();

        $data = array();

        if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $info, $match)) {
            $arr_duration = explode(':', $match[1]);
            $data['seconds'] = $arr_duration[0] * 3600 + $arr_duration[1] * 60 + $arr_duration[2]; //转换播放时间为秒数
            $data['start'] = $match[2]; //开始时间
            $data['bitrate'] = $match[3]; //码率(kb)
        }

        if (preg_match("/Video: (.*?), (.*?), (.*?)[,\s]/", $info, $match)) {
            $data['vcodec'] = $match[1]; //视频编码格式
            $data['vformat'] = $match[2]; //视频格式
            $arr_resolution = explode('x', $match[3]);

            $data['width'] = $arr_resolution[0];
            $data['height'] = $arr_resolution[1];
        }
        if (preg_match('/(\d+\.?\d*) fps/', $info, $matches)) {
            $data['fps'] = $matches[1];
        }

        $data['has_audio'] = 0;

        if (strpos($info, "Stream #0:1") !== false) {
            $data['has_audio'] = 1;
        }

        if (isset($data['seconds']) && isset($data['start'])) {
            $data['play_time'] = $data['seconds'] + $data['start']; //实际播放时间
        }
        $data['size'] = filesize($movie); //文件大小

        return $data;
    }
}

if (!function_exists("pdf2video")) {
    function pdf2video($pdfpath, $interval)
    {
        $CI = &get_instance();
        $ffmpegPath = $CI->config->item('ffmpeg');

        $ext = pathinfo($pdfpath, PATHINFO_EXTENSION);
        $filename = basename($pdfpath, "." . $ext);
        $OutputPath = dirname($pdfpath) . '/' . $filename;

        if (!extension_loaded('imagick')) {
            echo "!!";
            return false;
        }
        if (!file_exists($pdfpath)) {
            echo "??????";
            return false;
        }

        if (!file_exists($OutputPath)) {
            mkdir($OutputPath);
        }

        $IM = new imagick();
        $IM->readImage($pdfpath);
        $count = 0;
        foreach ($IM as $Key => $Var) {
            $Var->setImageFormat('png');
            $count++;
            $pngname = $OutputPath . '/' . $filename . $count . '.png';
            if ($Var->writeImage($pngname) == true) {
            }
        }

        //conver to video
        $videopath = dirname($pdfpath) . '/' . $filename . '.avi';
        $command = $ffmpegPath . " -r " . (1 / $interval) . " -i " . $OutputPath . '/' . $filename . "%d.png" . ' -b 1500 ' . $videopath . ' -vcodec xvid';
        @exec($command, $output, $return);

        //delete tempory folders

        $command = 'rm -rf ' . $OutputPath;
        @exec($command);
        return $videopath;
    }
}
if (!function_exists("office2video")) {
    function office2video($full_path, $interval)
    {
        $ext = pathinfo($full_path, PATHINFO_EXTENSION);
        $filename = basename($full_path, "." . $ext);
        $OutputPath = dirname($full_path);
        $videopath = dirname($full_path) . '/' . $filename . '.avi';

        //if the video file alreay exsit;
        if (file_exists($videopath)) {
            echo "Already exist:" . $videopath . "<br>";
            return $videopath;
        }


        $command = 'soffice --invisible --headless  --convert-to pdf:writer_pdf_Export --outdir ' . $OutputPath . ' ' . $full_path . ' 2>&1';
        @exec($command, $output, $return);


        if ($return == 0) {
            $pdfpath = $OutputPath . '/' . $filename . ".pdf";
            $videopath = $this->pdf2video($pdfpath, $interval);
        } else {
            echo var_dump($output);
            return false;
        }

        $command = 'rm -f ' . $pdfpath;
        @exec($command);

        return $videopath;
    }
}

if (!function_exists("generate_client_area_media")) {

    /**
     * 生成客户机某个区域的媒体文件
     *
     * @param object $media
     * @param object $template
     * @param object $area
     * @param object $is_rotate
     * @param object $is_fit this will be working if is_rotate be true
     * @return 成功文件路径，否则为false
     */
    function generate_client_area_media($media, $template, $area, $t_width, $t_height, $is_rotate = false, $is_fit = false)
    {
        if ($media === false) {
            return false;
        }

        if ($template === false) {
            return false;
        }

        if ($area === false) {
            return false;
        }


        $CI = &get_instance();
        $base = $CI->config->item('base_path') . '/';
        $resources = $CI->config->item('resources');

        $video_area = false;
        if ($area->area_type == $CI->config->item('area_type_movie')) {
            $video_area = true;
        }
        //支持非Local的图片文件
        if ($media->source != $CI->config->item('media_source_local')) {
            if ($media->media_type == $CI->config->item('media_type_image')) {
                $download_path = $resources . $media->company_id . '/download/';
                if (!file_exists($base . $download_path)) {
                    mkdir($base . $download_path, 0777, true);
                }
                $absPath = downloadRemoteFile($media->full_path, $download_path);
                if ($absPath === false) {
                    return false;
                }
            } else {
                return $media->full_path;
            }
        } else {
            $absPath = $media->full_path;
        }

        if (!$absPath) {
            return false;
        }
        $is_logo = $area->area_type == $CI->config->item('area_type_logo');
        $is_bg = $area->area_type == $CI->config->item('area_type_bg');
        $vertical = ($template->width < $template->height) && $is_rotate && !$is_logo;

        if (($template->width < $template->height) && !$is_logo) {
            if ($media->media_type == $CI->config->item('media_type_image')) {
                $info = get_movie_info($absPath);
                $image_width = $info['width'];
                $image_height = $info['height'];
                if (!$is_fit) {
                    if ($is_rotate) {
                        //YUV中高度宽度和模板的高度宽度颠倒
                        $width = resize_width(ceil(($area->h * $template->height) / $template->h));
                        $height = resize_height(ceil(($area->w * $template->width) / $template->w));
                    } else {
                        $height = resize_width(ceil(($area->w * $template->width) / $template->w));
                        $width = resize_height(ceil(($area->h * $template->height) / $template->h));
                    }
                } else {
                    if ($is_rotate) {
                        //图片旋转+Fit
                        $t_width = 2 * $t_width;
                        $t_height = 2 * $t_height;
                        if ($image_width < $t_width && $image_height < $t_height) {
                            $black_h = (intval(($t_height - $image_height) / 2) * 2) / 2;  //左右黑边高度
                            $black_w = (intval(($t_width - $image_width) / 2) * 2) / 2;    //上下黑边高度
                            $width = $image_width;
                            $height = $image_height;
                        } else {
                            if ($t_width / $image_width <= $t_height / $image_height) {
                                $width = $t_width;                                      //转成后图片宽度
                                $height = ceil($t_width * $image_height / $image_width);    //转成后图片高度
                                $height = intval($height / 2) * 2;

                                $black_w = 0;
                                $black_h = (intval(($t_height - $height) / 2) * 2) / 2; //上下黑边高度
                            } else {
                                $height = $t_height; //转成后图片高度
                                $width = ceil($image_width * $t_height / $image_height);   //转成后图片宽度
                                $width = intval($width / 2) * 2;

                                $black_w = (intval(($t_width - $width) / 2) * 2) / 2;  //左右黑边高度
                                $black_h = 0;
                            }
                        }
                    } else {
                        //图片Fit
                        $temp = 2 * $t_width;
                        $t_width = 2 * $t_height;
                        $t_height = $temp;
                        if ($image_width < $t_width && $image_height < $t_height) {
                            $black_h = (intval(($t_height - $image_height) / 2) * 2) / 2;  //左右黑边高度
                            $black_w = (intval(($t_width - $image_width) / 2) * 2) / 2;    //上下黑边高度
                            $width = $image_width;
                            $height = $image_height;
                        } else {
                            if ($t_width / $t_height <= $image_width / $image_height) {
                                $width = $t_width;                                      //转成后图片宽度
                                $height = ceil($t_width * $image_height / $image_width);    //转成后图片高度
                                $height = intval($height / 2) * 2;

                                $black_w = 0;
                                $black_h = (intval(($t_height - $height) / 2) * 2) / 2; //上下黑边高度
                            } else {
                                $height = $t_height; //转成后图片高度
                                $width = ceil($image_width * $t_height / $image_height);   //转成后图片宽度
                                $width = intval($width / 2) * 2;

                                $black_w = (intval(($t_width - $width) / 2) * 2) / 2;  //左右黑边高度
                                $black_h = 0;
                            }
                        }
                    }
                }
            } else {
                if ($vertical) {
                    //YUV中高度宽度和模板的高度宽度颠倒
                    $width = resize_width(ceil(($area->h * $template->height) / $template->h));
                    $height = resize_height(ceil(($area->w * $template->width) / $template->w));
                    if ($is_fit) {
                        //Fit 竖屏宽来决定视频的高度大小，维持一定的比例（视频的原始尺寸比例）
                        $info = get_movie_info($absPath);
                        if ($info) {
                            $height = resize_height(ceil($width * $info['height'] / $info['width']));
                        }
                    }
                } else {
                    $width = resize_width(ceil(($area->w * $template->width) / $template->w));
                    $height = resize_height(ceil(($area->h * $template->height) / $template->h));
                }
                /*
                $t_width = 2 * $t_width;
                $t_height = 2 * $t_height;
                $width = resize_width(ceil(($area->h * $template->height) / $template->h));
                $height = resize_height(ceil(($area->w * $template->width) / $template->w));
                */
            }
        } else {
            if ($area->area_type == 1 && $is_fit || $area->area_type == 0 && $is_fit) {
                //根据图片尺寸缩放图片
                $t_width = 2 * $t_width;
                $t_height = 2 * $t_height;
                $info = get_movie_info($absPath);
                $image_width = $info['width'];
                $image_height = $info['height'];
                $height = resize_width(ceil(($area->w * $template->width) / $template->w));
                $width = resize_height(ceil(($area->h * $template->height) / $template->h));
                if ($image_width < $t_width && $image_height < $t_height) {
                    $width = $image_width;
                    $height = $image_height;
                    $black_h = (intval(($t_height - $image_height) / 2) * 2) / 2;  //左右黑边高度
                    $black_w = (intval(($t_width - $image_width) / 2) * 2) / 2;    //上下黑边高度

                    if ($area->area_type == 1) {
                        $t_width = $t_width;
                        $t_height = $t_height;
                    } else {
                        $t_width = $width;
                        $t_height = $height;
                    }
                } else {
                    if ($t_width / $t_height <= $image_width / $image_height) {
                        $width = $t_width;                                      //转成后图片宽度
                        $height = ceil($t_width * $image_height / $image_width);    //转成后图片高度
                        $height = intval($height / 2) * 2;

                        $black_w = 0;
                        $black_h = (intval(($t_height - $height) / 2) * 2) / 2; //上下黑边高度

                        if ($area->area_type == 1) {
                            $t_width = $t_width;
                            $t_height = $t_height;
                        } else {
                            $t_width = $width;
                            $t_height = $height;
                        }
                    } else {
                        $height = $t_height; //转成后图片高度
                        $width = ceil($image_width * $t_height / $image_height);   //转成后图片宽度
                        $width = intval($width / 2) * 2;

                        $black_w = (intval(($t_width - $width) / 2) * 2) / 2;  //左右黑边高度
                        $black_h = 0;

                        if ($area->area_type == 1) {
                            $t_width = $t_width;
                            $t_height = $t_height;
                        } else {
                            $t_width = $width;
                            $t_height = $height;
                        }
                    }
                }
            } else {
                $t_width = 2 * $t_width;
                $t_height = 2 * $t_height;
                $width = resize_width(ceil(($area->w * $template->width) / $template->w));
                $height = resize_height(ceil(($area->h * $template->height) / $template->h));
            }
            /*
            $t_width = 2 * $t_width;
            $t_height = 2 * $t_height;
            $width = resize_width(ceil(($area->w * $template->width) / $template->w));
            $height = resize_height(ceil(($area->h * $template->height) / $template->h));
            */
        }
        if ($media->media_type == $CI->config->item('media_type_image')) {
            $tmp = explode('.', $absPath);
            $ext = strtolower($tmp[count($tmp) - 1]); //获取图片文件后缀名
            if (!$is_fit) {
                if ($is_rotate && !$is_logo) {
                    //旋转后的文件
                    $degrees = 270;
                    $rabsPath = rename_media_name($absPath, "r", "r");
                    //--添加区域后缀
                    $arr = explode('.', $rabsPath);
                    if (count($arr) == 5) {
                        $rabsPath = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $is_fit . '.' . $arr[4];
                    }
                    if (count($arr) == 7) {
                        $rabsPath = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $arr[4] . '.' . $arr[5] . '.' . $is_fit . '.' . $arr[6];
                    }
                    if (file_exists($rabsPath)) {
                        $absPath = $rabsPath;
                    } else {
                        //$source = imagecreatefromjpeg($base.$absPath);
                        if ($ext == 'bmp') {
                            $absPath = $absPath;
                        } else {
                            if ($ext == 'png') {
                                $source = imagecreatefrompng($base . $absPath);
                            } else {
                                $source = imagecreatefromjpeg($base . $absPath);
                            }
                            // Rotate
                            $rotate = imagerotate($source, $degrees, 0);
                            if ($ext == 'png') {
                                $status = imagepng($rotate, $base . $rabsPath, 9);
                            } else {
                                $status = imagejpeg($rotate, $base . $rabsPath, 95);
                            }

                            @imagedestroy($rotate);
                            @imagedestroy($source);
                            if ($status) {
                                $absPath = $rabsPath;
                            } else {
                                return false;
                            }
                        }
                    }
                }
                //resize image
                $size = @getimagesize($base . $absPath);
                $dest = rename_media_name($absPath, $width, $height); //$absPath .'.'.$width.'.'.$height;
                //--添加区域后缀
                $arr = explode('.', $dest);
                if (count($arr) == 5) {
                    $dest = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $is_fit . '.' . $arr[4];
                }
                if (count($arr) == 7) {
                    $dest = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $arr[4] . '.' . $arr[5] . '.' . $is_fit . '.' . $arr[6];
                }

                if ($ext == 'bmp') {
                    $dest = $absPath;
                }

                if ($width == $size[0] && $height == $size[1]) {
                    //copy
                    @copy($absPath, $dest);
                } else {
                    //resize
                    $thumb = @imagecreatetruecolor($width, $height);
                    //load
                    if ($ext == 'png') {
                        $source = @imagecreatefrompng($base . $absPath);
                    } else {
                        $source = @imagecreatefromjpeg($base . $absPath);
                    }

                    //$source = @imagecreatefromjpeg($base.$absPath);
                    //resize
                    @imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                    if ($ext == 'png') {
                        @imagepng($thumb, $base . $dest, 9);
                    } else {
                        @imagejpeg($thumb, $base . $dest, 95);
                    }
                    //@imagejpeg($thumb, $base.$dest, 95);
                    @imagedestroy($thumb);
                    @imagedestroy($source);
                }
            } else {
                if (($template->width < $template->height) && !$is_logo) {
                    $degrees = 270;
                    $rabsPath = rename_media_name($absPath, $t_width, $t_height);
                    if ($is_rotate) {
                        $rabsPath = rename_media_name(rename_media_name($absPath, 'r', 'r'), $t_width, $t_height);
                    }

                    //--添加区域后缀
                    $arr = explode('.', $rabsPath);
                    if (count($arr) == 5) {
                        $rabsPath = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $is_fit . '.' . $arr[4];
                    }
                    if (count($arr) == 7) {
                        $rabsPath = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $arr[4] . '.' . $arr[5] . '.' . $is_fit . '.' . $arr[6];
                    }

                    if (file_exists($rabsPath)) {
                        $absPath = $rabsPath;
                    } else {
                        if ($is_rotate) {
                            if ($is_bg) {
                                //如果图片比例大于模块比例，压缩图片
                                $black_w = 0;
                                $black_h = 0;
                                $size = getimagesize($base . $absPath);
                                $thumb = @imagecreatetruecolor(1080, 1920);
                                //$source1 = @imagecreatefromjpeg($base.$absPath);
                                if ($ext == 'png') {
                                    $source1 = @imagecreatefrompng($base . $absPath);
                                } else {
                                    $source1 = @imagecreatefromjpeg($base . $absPath);
                                }
                                @imagecopyresized($thumb, $source1, 0, 0, 0, 0, 1080, 1920, $size[0], $size[1]);
                                if ($ext == 'png') {
                                    @imagepng($thumb, $base . rename_media_name($absPath, $t_width, $t_height), 9);
                                } else {
                                    @imagejpeg($thumb, $base . rename_media_name($absPath, $t_width, $t_height), 100);
                                }
                                //@imagejpeg($thumb, $base.rename_media_name($absPath, $t_width, $t_height), 100);
                                @imagedestroy($thumb);
                                @imagedestroy($source1);
                            } else {
                                //如果图片比例大于模块比例，压缩图片
                                $size = getimagesize($base . $absPath);
                                $thumb = @imagecreatetruecolor($width, $height);
                                //$source1 = @imagecreatefromjpeg($base.$absPath);
                                if ($ext == 'png') {
                                    $source1 = @imagecreatefrompng($base . $absPath);
                                } else {
                                    $source1 = @imagecreatefromjpeg($base . $absPath);
                                }
                                @imagecopyresized($thumb, $source1, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                                //@imagejpeg($thumb, $base.rename_media_name($absPath, $t_width, $t_height), 100);
                                if ($ext == 'png') {
                                    @imagepng($thumb, $base . rename_media_name($absPath, $t_width, $t_height), 9);
                                } else {
                                    @imagejpeg($thumb, $base . rename_media_name($absPath, $t_width, $t_height), 100);
                                }
                                @imagedestroy($thumb);
                                @imagedestroy($source1);
                            }

                            //创建黑色底图
                            $black_im = imagecreatetruecolor($t_width, $t_height);
                            //$result = imagejpeg($black_im, $base.$rabsPath , 100);
                            if ($ext == 'png') {
                                $result = imagepng($black_im, $base . $rabsPath, 9);
                            } else {
                                $result = imagejpeg($black_im, $base . $rabsPath, 100);
                            }

                            //根据黑边宽度合成图片
                            $dst = $base . $rabsPath;               //黑色底图
                            //$b_im = imagecreatefromjpeg($dst);
                            //$water = $base.rename_media_name($absPath, $t_width, $t_height);             //服务器上传的原图
                            //$in = imagecreatefromjpeg($water);
                            //imagecopy($b_im, $in, $black_w, $black_h, 0 , 0, $t_width, $t_height);
                            //imagejpeg($b_im, $dst, 100);
                            if ($ext == 'png') {
                                $b_im = imagecreatefrompng($dst);
                                $water = $base . rename_media_name($absPath, $t_width, $t_height);             //服务器上传的原图
                                $in = imagecreatefrompng($water);
                                imagecopy($b_im, $in, $black_w, $black_h, 0, 0, $t_width, $t_height);
                                imagepng($b_im, $dst, 9);
                            } else {
                                $b_im = imagecreatefromjpeg($dst);
                                $water = $base . rename_media_name($absPath, $t_width, $t_height);             //服务器上传的原图
                                $in = imagecreatefromjpeg($water);
                                imagecopy($b_im, $in, $black_w, $black_h, 0, 0, $t_width, $t_height);
                                imagejpeg($b_im, $dst, 100);
                            }
                            @imagedestroy($b_im);
                            @imagedestroy($in);
                            @imagedestroy($black_im);

                            //图片翻转
                            $degrees = 270;
                            //$source = imagecreatefromjpeg($base.$rabsPath);
                            //$rotate = imagerotate($source, $degrees, 0);
                            //$status = imagejpeg($rotate, $base.$rabsPath, 100);
                            if ($ext == 'png') {
                                $source = imagecreatefrompng($base . $rabsPath);
                                $rotate = imagerotate($source, $degrees, 0);
                                $status = imagepng($rotate, $base . $rabsPath, 9);
                            } else {
                                $source = imagecreatefromjpeg($base . $rabsPath);
                                $rotate = imagerotate($source, $degrees, 0);
                                $status = imagejpeg($rotate, $base . $rabsPath, 100);
                            }
                            @imagedestroy($source);
                            @imagedestroy($rotate);
                        } else {
                            //--添加区域后缀
                            $arr = explode('.', $rabsPath);
                            if (count($arr) == 5) {
                                $rabsPath = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $is_fit . '.' . $arr[4];
                            }
                            if (count($arr) == 7) {
                                $rabsPath = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $arr[4] . '.' . $arr[5] . '.' . $is_fit . '.' . $arr[6];
                            }
                            //如果图片比例大于模块比例，压缩图片
                            $size = getimagesize($base . $absPath);
                            $thumb = @imagecreatetruecolor($width, $height);
                            //$source1 = @imagecreatefromjpeg($base.$absPath);
                            //@imagecopyresized($thumb, $source1, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                            //@imagejpeg($thumb, $base.rename_media_name($absPath, 'w', 'w'), 100);
                            if ($ext == 'png') {
                                $source1 = @imagecreatefrompng($base . $absPath);
                                @imagecopyresized($thumb, $source1, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                                @imagepng($thumb, $base . rename_media_name($absPath, 'w', 'w'), 9);
                            } else {
                                $source1 = @imagecreatefromjpeg($base . $absPath);
                                @imagecopyresized($thumb, $source1, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                                @imagejpeg($thumb, $base . rename_media_name($absPath, 'w', 'w'), 100);
                            }

                            @imagedestroy($thumb);
                            @imagedestroy($source1);

                            //创建黑色底图
                            $black_im = imagecreatetruecolor($t_width, $t_height);

                            //$result = imagejpeg($black_im, $base.$rabsPath , 100);
                            if ($ext == 'png') {
                                $result = imagepng($black_im, $base . $rabsPath, 9);
                            } else {
                                $result = imagejpeg($black_im, $base . $rabsPath, 100);
                            }

                            //根据黑边宽度合成图片
                            $dst = $base . $rabsPath;               //黑色底图
                            //$b_im = imagecreatefromjpeg($dst);
                            //$water = $base.rename_media_name($absPath, 'w', 'w');             //服务器上传的原图
                            //$in = imagecreatefromjpeg($water);
                            //imagecopy($b_im, $in, $black_w, $black_h, 0 , 0, $t_width, $t_height);
                            //imagejpeg($b_im, $dst, 100);
                            if ($ext == 'png') {
                                $b_im = imagecreatefrompng($dst);
                                $water = $base . rename_media_name($absPath, 'w', 'w');             //服务器上传的原图
                                $in = imagecreatefrompng($water);
                                imagecopy($b_im, $in, $black_w, $black_h, 0, 0, $t_width, $t_height);
                                imagepng($b_im, $dst, 9);
                            } else {
                                $b_im = imagecreatefromjpeg($dst);
                                $water = $base . rename_media_name($absPath, 'w', 'w');             //服务器上传的原图
                                $in = imagecreatefromjpeg($water);
                                imagecopy($b_im, $in, $black_w, $black_h, 0, 0, $t_width, $t_height);
                                imagejpeg($b_im, $dst, 100);
                            }
                            @imagedestroy($b_im);
                            @imagedestroy($in);
                            @imagedestroy($black_im);
                        }
                    }
                    $dest = $rabsPath;
                }
            }

            if (($template->width > $template->height) || $is_logo) {
                if ($area->area_type == 1 && $is_fit) {
                    $rabsPath = rename_media_name($absPath, $t_width, $t_height);
                    //--添加区域后缀
                    $arr = explode('.', $rabsPath);
                    if (count($arr) == 5) {
                        $rabsPath = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $is_fit . '.' . $arr[4];
                    }
                    if (count($arr) == 7) {
                        $rabsPath = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $arr[4] . '.' . $arr[5] . '.' . $is_fit . '.' . $arr[6];
                    }

                    $size = getimagesize($base . $absPath);
                    $thumb = @imagecreatetruecolor($width, $height);
                    //$source1 = @imagecreatefromjpeg($base.$absPath);
                    //@imagecopyresized($thumb, $source1, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                    //@imagejpeg($thumb, $base.rename_media_name($absPath, 'w', 'w'), 100);
                    if ($ext == 'png') {
                        $source1 = @imagecreatefrompng($base . $absPath);
                        @imagecopyresized($thumb, $source1, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                        @imagepng($thumb, $base . rename_media_name($absPath, 'w', 'w'), 9);
                    } else {
                        $source1 = @imagecreatefromjpeg($base . $absPath);
                        @imagecopyresized($thumb, $source1, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                        @imagejpeg($thumb, $base . rename_media_name($absPath, 'w', 'w'), 100);
                    }
                    @imagedestroy($thumb);
                    @imagedestroy($source1);

                    //创建黑色底图
                    $black_im = imagecreatetruecolor($t_width, $t_height);
                    //$result = imagejpeg($black_im, $base.$rabsPath , 100);
                    if ($ext == 'png') {
                        $result = imagepng($black_im, $base . $rabsPath, 9);
                    } else {
                        $result = imagejpeg($black_im, $base . $rabsPath, 100);
                    }

                    //根据黑边宽度合成图片
                    $dst = $base . $rabsPath;               //黑色底图
                    //$b_im = imagecreatefromjpeg($dst);
                    //$water = $base.rename_media_name($absPath, 'w', 'w');             //服务器上传的原图
                    //$in = imagecreatefromjpeg($water);
                    //imagecopy($b_im, $in, $black_w, $black_h, 0 , 0, $t_width, $t_height);
                    //imagejpeg($b_im, $dst, 100);
                    if ($ext == 'png') {
                        $b_im = imagecreatefrompng($dst);
                        $water = $base . rename_media_name($absPath, 'w', 'w');             //服务器上传的原图
                        $in = imagecreatefrompng($water);
                        imagecopy($b_im, $in, $black_w, $black_h, 0, 0, $t_width, $t_height);
                        imagepng($b_im, $dst, 9);
                    } else {
                        $b_im = imagecreatefromjpeg($dst);
                        $water = $base . rename_media_name($absPath, 'w', 'w');             //服务器上传的原图
                        $in = imagecreatefromjpeg($water);
                        imagecopy($b_im, $in, $black_w, $black_h, 0, 0, $t_width, $t_height);
                        imagejpeg($b_im, $dst, 100);
                    }
                    @imagedestroy($b_im);
                    @imagedestroy($in);
                    @imagedestroy($black_im);
                    $dest = $rabsPath;
                } else {

                    //resize image
                    $size = @getimagesize($base . $absPath);
                    $dest = rename_media_name($absPath, $width, $height); //$absPath .'.'.$width.'.'.$height;
                    //--添加区域后缀
                    $arr = explode('.', $dest);
                    if (count($arr) == 5) {
                        $dest = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $is_fit . '.' . $arr[4];
                    }
                    if (count($arr) == 7) {
                        $dest = '.' . $arr[1] . '_' . $area->area_type . '.' . $arr[2] . '.' . $arr[3] . '.' . $arr[4] . '.' . $arr[5] . '.' . $is_fit . '.' . $arr[6];
                    }

                    if ($width == $size[0] && $height == $size[1]) {
                        //copy
                        @copy($absPath, $dest);
                    } else {
                        //resize
                        $thumb = @imagecreatetruecolor($width, $height);
                        //load
                        //$source = @imagecreatefromjpeg($base.$absPath);
                        //@imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                        //@imagejpeg($thumb, $base.$dest, 95);
                        if ($ext == 'png') {
                            $source = @imagecreatefrompng($base . $absPath);
                            @imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                            @imagepng($thumb, $base . $dest, 9);
                        } else {
                            $source = @imagecreatefromjpeg($base . $absPath);
                            @imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                            @imagejpeg($thumb, $base . $dest, 95);
                        }

                        @imagedestroy($thumb);
                        @imagedestroy($source);
                    }
                }
            }

            //如果是视频区域，则无需要转化图片，直接下发即可
            if ($video_area) {
                $ext = strtolower($media->ext);
                if (in_array($ext, array('ppt', 'pptx'))) {
                    return $this->office2video($base . substr($meidafull, 2), 5);
                } else {
                    return $dest;
                }
            }

            //Logo区域
            if ($is_logo) {
                $dest_bmp = $dest . ".bmp";
                if (file_exists($dest_bmp)) {
                    return $dest_bmp;
                }

                if (jpeg2bmp($dest, $dest_bmp)) {
                    return $dest_bmp;
                }
                return false;
            }

            //YUV转化库
            if (!isset($_SERVER['WINDIR'])) {
                if (!extension_loaded("jpeg2yuv")) {
                    if (@dl("jpeg2yuv.so") == false) {
                        return false;
                    }
                }
            }
            $destYuv = $dest . '.yuv';

            //已经存在则下载
            if ($video_area == false && file_exists($destYuv)) {
                //return $destYuv;
            }
            //$vertical=TRUE;
            //convert YUV
            if (function_exists("jpeg2yuv411")) {
                if ($is_fit) {
                    if ($is_rotate) {
                        $code = jpeg2yuv411($base . $dest, $base . $destYuv, $t_height, $t_width);
                    } else {
                        $code = jpeg2yuv411($base . $dest, $base . $destYuv, $t_width, $t_height);
                    }
                } else {
                    if (($template->width > $template->height)) {
                        if ($area->area_type == 1 && $is_fit) {
                            $code = jpeg2yuv411($base . $dest, $base . $destYuv, $t_width, $t_height);
                        } else {
                            $code = jpeg2yuv411($base . $dest, $base . $destYuv, $width, $height);
                        }
                    } else {
                        $code = jpeg2yuv411($base . $dest, $base . $destYuv, $width, $height);
                    }
                }

                if ($code != 0) {
                    //delete temp file
                    //@unlink($dest);
                    return $destYuv;
                }
            }
            //@unlink($dest);
            //echo $dest;
        } elseif ($media->media_type == $CI->config->item('media_type_video')) {
            //only mpg(interfaced) and wmv 需要用ffmpeg
            if ($vertical) {
                /**
                //$dest = rename_media_name($absPath, $width, $height);
                $dest = rename_media_name($absPath, 'r', 'r', TRUE);
                if (file_exists($dest)) {
                    return $dest;
                }
                $transpose = 1;
                if (generate_client_movie($base.$absPath, $base.$dest, $width, $height, $t_width, $t_height, $transpose)) {
                    return $dest;
                }*/
                $tmp = explode('.', $absPath);
                $dest = '';
                for ($i = 0; $i < count($tmp) - 1; $i++) {
                    $dest .= $tmp[$i] . '.';
                }
                $dest .= 'r.r.';
                if ($is_fit) {
                    $dest .= $t_width . '.' . $t_height . '.';
                }
                $ext = strtolower($tmp[count($tmp) - 1]);
                if (in_array($ext, array('mp4', 'wmv', 'flv', 'mov', 'mpg', 'mpeg', 'mkv', 'divx', 'avi'))) {
                    $dest .= 'mkv';
                } else {
                    $dest .= $ext;
                }
                if (file_exists($dest)) {
                    return $dest;
                }
                $transpose = 1;
                if (generate_client_movie($base . $absPath, $base . $dest, $width, $height, $t_width, $t_height, $transpose, $is_fit)) {
                    return $dest;
                }
            } else {
                $ext = strtoupper($media->ext);
                if ($ext == 'MP4' || $ext == 'MOV'  || $ext == 'FLV' || $ext == 'WMV') {
                    $dest = rename_media_name($absPath, $width, $height, true);
                    //已经存在则下载
                    if (file_exists($dest)) {
                        return $dest;
                    }

                    $transpose = -1;
                    //if (generate_client_movie($base.$absPath, $base.$dest, $width, $height, $t_width, $t_height, $transpose)) {
                    if (generate_client_movie($base . $absPath, $base . $dest, $width, $height, $t_width, $t_height, $transpose, $is_fit)) {
                        return $dest;
                    }
                } else {
                    return $absPath;
                }
            }
        }

        return false;
    }
}


function jpeg2bmp($jpeg_file, $bmp_file)
{
    if (!file_exists($jpeg_file)) {
        return false;
    }

    $im = imagecreatefromjpeg($jpeg_file);

    $w = imagesx($im);
    $h = imagesy($im);
    $result = '';

    if (!imageistruecolor($im)) {
        $tmp = imagecreatetruecolor($w, $h);
        imagecopy($tmp, $im, 0, 0, 0, 0, $w, $h);
        imagedestroy($im);
        $im = &$tmp;
    }

    $biBPLine = $w * 3;
    $biStride = ($biBPLine + 3) & ~3;
    $biSizeImage = $biStride * $h;
    $bfOffBits = 54;
    $bfSize = $bfOffBits + $biSizeImage;

    $result .= substr('BM', 0, 2);
    $result .= pack('VvvV', $bfSize, 0, 0, $bfOffBits);
    $result .= pack('VVVvvVVVVVV', 40, $w, $h, 1, 24, 0, $biSizeImage, 0, 0, 0, 0);

    $numpad = $biStride - $biBPLine;
    for ($y = $h - 1; $y >= 0; --$y) {
        for ($x = 0; $x < $w; ++$x) {
            $col = imagecolorat($im, $x, $y);
            $result .= substr(pack('V', $col), 0, 3);
        }
        for ($i = 0; $i < $numpad; ++$i) {
            $result .= pack('C', 0);
        }
    }

    $file = fopen($bmp_file, "wb");
    fwrite($file, $result);
    fclose($file);
    return true;
}
if (!function_exists("rename_media_name")) {

    /**
     * 重命名视频文件
     *
     * @param object $path
     * @param object $width
     * @param object $height
     * @param object $video [optional] default FALSE
     * @return
     */
    function rename_media_name($path, $width, $height, $video = false)
    {
        $tmp = explode('.', $path);
        $dest = '';
        for ($i = 0; $i < count($tmp) - 1; $i++) {
            $dest .= $tmp[$i] . '.';
        }
        $dest .= $width . '.' . $height . '.';
        if ($video) {
            $ext = strtolower($tmp[count($tmp) - 1]);
            if (in_array($ext, array('mp4', 'mov', 'wmv', 'flv'))) {
                $dest .= 'mkv';
            } else {
                $dest .= $ext;
            }
        } else {
            $dest .= $tmp[count($tmp) - 1];
        }
        return $dest;
    }
}

if (!function_exists("resize_width")) {
    /**
     * 对齐，必须是4的倍数
     *
     * @param object $size
     * @return
     */
    function resize_width($size)
    {
        return intval($size / 4) * 4;
    }
}

if (!function_exists("resize_height")) {
    /**
     * 对齐，必须是2的倍数
     *
     * @param object $size
     * @return
     */
    function resize_height($size)
    {
        return intval($size / 2) * 2;
    }
}

if (!function_exists("check_ftp_url")) {
    /**
     * 验证是否为合法的URL
     *
     * @param object $server
     * @return
     */
    function check_ftp_url($server)
    {
        return true;
        /*
        $server = strtolower($server);
        //ftp://
        if (substr($server, 0, 6) == 'ftp://') {
            return true;
        } elseif (preg_match('/^([A-Za-z0-9]{1,}\.){1,}[A-Za-z0-9]{1,}$/', $server, $match)) {
            //print_r($match);
            return true;
        }

        return false;
        */
    }
}

if (!function_exists('rotating_movie')) {
    /**
     * 逆时针90度旋转flv视频
     */
    function rotating_movie($movie, $outfile)
    {
        if (empty($movie) || empty($outfile)) {
            return false;
        }
        $CI = &get_instance();
        $absPath = $CI->config->item('ffmpeg');
        $command = $absPath . ' -i ' . $movie . ' -vf transpose=2 ' . $outfile;
        exec($command, $output, $return);
        if ($return == 0) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('rotating_image')) {
    /**
     * 逆时针90度旋转图片
     */
    function rotating_image($movie, $outfile)
    {
        if (empty($movie) || empty($outfile)) {
            return false;
        }
        $CI = &get_instance();
        //旋转后的文件
        $degrees = 90;
        $source = imagecreatefromjpeg($movie);
        // Rotate
        $rotate = imagerotate($source, $degrees, 0);
        $status = imagejpeg($rotate, $outfile, 100);
        @imagedestroy($rotate);
        @imagedestroy($source);
        if ($status) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists("generate_client_image")) {
    /**
     * 生成客户端图片预览文件
     *
     * @param object $media
     * @param object $template
     * @param object $area
     * @param object $is_rotate
     * @param object $is_fit this will be working if is_rotate be true
     * @return 成功文件路径，否则为false
     */
    function generate_client_image($media, $template, $area, $t_width, $t_height, $is_rotate = false, $is_fit = false)
    {
        if ($media === false) {
            return false;
        }

        if ($template === false) {
            return false;
        }

        if ($area === false) {
            return false;
        }
        $CI = &get_instance();
        $base = $CI->config->item('base_path') . '/';
        $resources = $CI->config->item('resources');

        $video_area = false;
        if ($area->area_type == $CI->config->item('area_type_movie')) {
            $video_area = true;
        }
        //支持非Local的图片文件
        if ($media->source != $CI->config->item('media_source_local')) {
            if ($media->media_type == $CI->config->item('media_type_image')) {
                $download_path = $resources . $media->company_id . '/download/';
                if (!file_exists($base . $download_path)) {
                    mkdir($base . $download_path, 0777, true);
                }
                $absPath = downloadRemoteFile($media->full_path, $download_path);
                if ($absPath === false) {
                    return false;
                }
            } else {
                return $media->full_path;
            }
        } else {
            $absPath = $media->full_path;
        }

        if (!$absPath) {
            return false;
        }
        $is_logo = $area->area_type == $CI->config->item('area_type_logo');
        $is_bg = $area->area_type == $CI->config->item('area_type_bg');
        $vertical = ($template->width < $template->height) && $is_rotate && !$is_logo;
        $tmp = explode('.', $absPath);
        $ext = strtolower($tmp[count($tmp) - 1]); //获取图片文件后缀名
        //图片预览，只有当图片本事是歪着的时候  才需要旋转
        // fit 加边 ； fill 填充
        if ($template->width < $template->height && !$is_rotate) {
            $rabsPath = rename_media_name($absPath, 'p', 'p');
            if ($ext == 'png') {
                $source = imagecreatefrompng($base . $absPath);
            } else {
                $source = imagecreatefromjpeg($base . $absPath);
            }
            // Rotate
            $rotate = imagerotate($source, 90, 0);
            if ($ext == 'png') {
                $status = imagepng($rotate, $base . $rabsPath, 9);
            } else {
                $status = imagejpeg($rotate, $base . $rabsPath, 95);
            }
            imagedestroy($rotate);
            imagedestroy($source);
            $absPath = $rabsPath;
        }
        $info = get_movie_info($absPath);
        if ($info) {
            $image_width = $info['width'];
            $image_height = $info['height'];
        } else {
            return false;
        }

        if ($is_fit) {
            //根据图片尺寸缩放图片
            $t_width = 2 * $t_width;
            $t_height = 2 * $t_height;
            $height = resize_width(ceil(($area->w * $template->width) / $template->w));
            $width = resize_height(ceil(($area->h * $template->height) / $template->h));
            if ($image_width < $t_width && $image_height < $t_height) {
                $width = $image_width;
                $height = $image_height;
                $black_h = (intval(($t_height - $image_height) / 2) * 2) / 2;  //左右黑边高度
                $black_w = (intval(($t_width - $image_width) / 2) * 2) / 2;    //上下黑边高度
            } else {
                if ($t_width / $t_height <= $image_width / $image_height) {
                    $width = $t_width;                                      //转成后图片宽度
                    $height = ceil($t_width * $image_height / $image_width);    //转成后图片高度
                    $height = intval($height / 2) * 2;

                    $black_w = 0;
                    $black_h = (intval(($t_height - $height) / 2) * 2) / 2; //上下黑边高度
                } else {
                    $height = $t_height; //转成后图片高度
                    $width = ceil($image_width * $t_height / $image_height);   //转成后图片宽度
                    $width = intval($width / 2) * 2;

                    $black_w = (intval(($t_width - $width) / 2) * 2) / 2;  //左右黑边高度
                    $black_h = 0;
                }
            }

            $rabsPath = rename_media_name($absPath, $t_width, $t_height);
            $size = getimagesize($base . $absPath);
            $thumb = imagecreatetruecolor($width, $height);
            if ($ext == 'png') {
                $source1 = imagecreatefrompng($base . $absPath);
                imagecopyresized($thumb, $source1, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                imagepng($thumb, $base . rename_media_name($absPath, 'w', 'w'), 9);
            } else {
                $source1 = imagecreatefromjpeg($base . $absPath);
                imagecopyresized($thumb, $source1, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                imagejpeg($thumb, $base . rename_media_name($absPath, 'w', 'w'), 100);
            }
            imagedestroy($thumb);
            imagedestroy($source1);

            //创建黑色底图
            $black_im = imagecreatetruecolor($t_width, $t_height);
            if ($ext == 'png') {
                $result = imagepng($black_im, $base . $rabsPath, 9);
            } else {
                $result = imagejpeg($black_im, $base . $rabsPath, 95);
            }

            //根据黑边宽度合成图片
            $dst = $base . $rabsPath;               //黑色底图
            if ($ext == 'png') {
                $b_im = imagecreatefrompng($dst);
                $water = $base . rename_media_name($absPath, 'w', 'w');             //服务器上传的原图
                $in = imagecreatefrompng($water);
                imagecopy($b_im, $in, $black_w, $black_h, 0, 0, $t_width, $t_height);
                imagepng($b_im, $dst, 9);
            } else {
                $b_im = imagecreatefromjpeg($dst);
                $water = $base . rename_media_name($absPath, 'w', 'w');             //服务器上传的原图
                $in = imagecreatefromjpeg($water);
                imagecopy($b_im, $in, $black_w, $black_h, 0, 0, $t_width, $t_height);
                imagejpeg($b_im, $dst, 100);
            }

            imagedestroy($b_im);
            imagedestroy($in);
            imagedestroy($black_im);
            $dest = $rabsPath;
        } else {
            $t_width = 2 * $t_width;
            $t_height = 2 * $t_height;
            $width = resize_width(ceil(($area->w * $template->width) / $template->w));
            $height = resize_height(ceil(($area->h * $template->height) / $template->h));

            //resize image
            $size = @getimagesize($base . $absPath);
            $dest = rename_media_name($absPath, $width, $height); //$absPath .'.'.$width.'.'.$height;

            //resize
            $thumb = @imagecreatetruecolor($width, $height);

            if ($ext == 'png') {
                $source = @imagecreatefrompng($base . $absPath);
                @imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                @imagepng($thumb, $base . $dest, 9);
            } else {
                $source = @imagecreatefromjpeg($base . $absPath);
                @imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                @imagejpeg($thumb, $base . $dest, 95);
            }

            @imagedestroy($thumb);
            @imagedestroy($source);
        }

        return $dest;
    }
}
if (!function_exists("generate_android_client_area_media")) {
    /**
     * 生成Android终端视频文件
     * @param object $movie
     * @param object $area
     * @param object $is_fit this will be working if is_rotate be true
     * @return 成功文件路径，否则为false
     */
    function generate_android_client_area_media($movie, $area, $t_width, $t_height)
    {
        if ($movie === false) {
            return false;
        }
        $CI = &get_instance();

        $info = get_movie_info($movie);
        if (!isset($info['fps']) || $info['fps'] > 30) {
            $info['fps'] = 30;
        }
        $vcodec = "-b " . $info['bitrate'] . "k -r " . $info['fps'];

        $trans = "";
        $absPath = $CI->config->item('ffmpeg');

        $t_height = 2 * $t_height;  //template高度
        $t_width = 2 * $t_width;    //template宽度
        $video_width = $info['width'];
        $video_height = $info['height'];
        if ($t_height == $video_height && $t_width == $video_width) {
            return $movie;
        }

        $tmp = explode('.', $movie);
        $dest = '';
        for ($i = 0; $i < count($tmp) - 1; $i++) {
            $dest .= $tmp[$i] . '.';
        }
        $dest .= $t_width . '.' . $t_height . '.';
        $ext = strtolower($tmp[count($tmp) - 1]);
        if (in_array($ext, array('mp4', 'wmv', 'flv', 'mov', 'mpg', 'mpeg', 'mkv', 'divx', 'avi'))) {
            $dest .= 'mkv';
        } else {
            $dest .= $ext;
        }
        if (file_exists($dest)) {
            return $dest;
        }

        if ($video_width < $t_width && $video_height < $t_height) {
            $black_w = (intval(($t_height - $video_height) / 2) * 2) / 2;  //左右黑边高度
            $black_h = (intval(($t_width - $video_width) / 2) * 2) / 2;    //上下黑边高度
            $trans = ' -vf "scale=' . $video_width . ':' . $video_height . ',pad=' . $t_width . ':' . $t_height . ':' . $black_h . ':' . $black_w . ':black"';
        } else {
            if ($t_width / $t_height < $video_width / $video_height) {
                $v_width = $t_width;                                      //转成厚视频宽度
                $v_height = ceil($t_width * $video_height / $video_width);    //转成后视频高度
                $black_w = 0;
                $black_h = (intval(($t_height - $v_height) / 2) * 2) / 2; //上下黑边高度
                $trans = ' -vf "scale=' . $v_width . ':' . $v_height . ',pad=' . $t_width . ':' . $t_height . ':' . $black_w . ':' . $black_h . ':black"';
            } else {
                $v_width = ceil($video_width * $t_height / $video_height);   //转成厚视频宽度
                $v_height = $t_height;                                   //转成后视频高度
                $black_w = (intval(($t_width - $v_width) / 2) * 2) / 2;  //左右黑边高度
                $black_h = 0;
                $trans = ' -vf "scale=' . $v_width . ':' . $v_height . ',pad=' . $t_width . ':' . $t_height . ':' . $black_w . ':' . $black_h . ':black"';
            }
        }
        $command = $absPath . ' -i ' . $movie . ' -y -acodec copy ' . $vcodec . ' ' . $trans . ' ' . $dest;
        //echo $command;
        exec($command, $output, $return);
        if ($return == 0) {
            return $dest;
        } else {
            return false;
        }
    }
}
