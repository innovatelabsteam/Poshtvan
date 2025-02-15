<?php
namespace poshtvan\app;
class file_uploader
{
    private static function getUploadBaseDir($dir=false, $base='poshtvan')
    {
        $base_dir = wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $base . DIRECTORY_SEPARATOR;
        if($dir)
        {
            $base_dir .= $dir . DIRECTORY_SEPARATOR;
        }
        return $base_dir;
    }
    static function getUploadedFileUrl($fileName)
    {
        $upload_dir = wp_upload_dir();
        return file_exists($upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'poshtvan' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $fileName))
            ? $upload_dir['baseurl'] . '/poshtvan/' . $fileName
            : $upload_dir['baseurl'] . '/mihanticket/' . $fileName;
    }
    static function getUploadDir(&$month_dir='')
    {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        $wp_upload_dir = self::getUploadBaseDir();
        $current_time = current_time('timestamp');
        $month_dir = gmdate('m', $current_time);

        if (!$wp_filesystem->is_dir($wp_upload_dir)) {
            $wp_filesystem->mkdir($wp_upload_dir);
        }
        $dir = $wp_upload_dir . $month_dir . DIRECTORY_SEPARATOR;
        if(!$wp_filesystem->is_dir($dir))
        {
            $wp_filesystem->mkdir($dir);
        }
        return $dir;
    }
    static function validate_file_type($fileType)
    {
        $whiteList = self::getFileUploadingMimeTypesWhiteList();
        return in_array($fileType, $whiteList);
    }
    static function valdiate_file_size($fileSize)
    {
        return $fileSize <= options::get_file_uploading_max_size_bytes();
    }
    static function uploadFile($fileData, &$msg='')
    {
        $validate_type = self::validate_file_type($fileData['type']);
        $validate_size = self::valdiate_file_size($fileData['size']);
        if(!$validate_type)
        {
            $msg = esc_html__('Invalid File Type', 'poshtvan');
            return false;
        }
        if(!$validate_size)
        {
            $msg = esc_html__('Invalid File Size', 'poshtvan');
            return false;
        }
        $upload_dir = self::getUploadDir($month_dir);
        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $current_user_id = get_current_user_id();
        $fileName = md5($current_user_id . wp_rand(1, 9999) . $fileData['name']) . '.' . $extension;
        $filePath = $upload_dir . $fileName;
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        $fileContent = $wp_filesystem->get_contents($fileData['tmp_name']);
        if(!$fileContent)
        {
            $msg = esc_html__('Missing file content', 'poshtvan');
            return false;
        }
        $upload_res = $wp_filesystem->put_contents($filePath, $fileContent, FS_CHMOD_FILE);
        return $upload_res ? $month_dir  . DIRECTORY_SEPARATOR . $fileName : false;
    }
    static function getFileTypesName()
    {
        return [
            'image' => __('Image', 'poshtvan'),
            'video' => __('Video', 'poshtvan'),
            'audio' => __('Audio', 'poshtvan'),
            'text' => __('Text', 'poshtvan'),
            'compressed' => __('Compressed', 'poshtvan'),
            'pdf' => __("PDF", 'poshtvan'),
        ];
    }
    static function getFileUploadingMimeTypesWhiteList()
    {
        $activeFileTypes = \poshtvan\app\options::getFileUploadingValidTypes();
        $allMimeTypes = self::getAllMimeTypes(true);
        $whiteList = [];
        foreach($activeFileTypes as $fileType)
        {
            $whiteList = array_merge($whiteList, $allMimeTypes[$fileType]);
        }
        return $whiteList;
    }
    static function getAllMimeTypes($sorted=false)
    {
        $image = [
            'image/gif',
            'image/jpeg',
            'image/png',
            'image/webp',
        ];
        $video =
        [
            'video/x-msvideo',
            'video/mp4',
            'video/quicktime',
            'video/x-ms-wmv',
            'video/x-ms-asf',
            'video/mpeg',
            'video/ogg',
            'video/webm',
            'video/3gpp',
        ];
        $audio =
        [
            'audio/aac',
            'audio/mpeg',
            'audio/ogg',
            'audio/wav',
            'audio/webm',
            'audio/3gpp',
        ];
        $text =
        [
            'text/css',
            'text/csv',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
        ];
        $compressed =
        [
            // compressed file
            'application/zip',
            'application/gzip',
            'application/vnd.rar',
        ];
        $pdf =
        [
            'application/pdf',
        ];
        
        if($sorted)
        {
            return[
                'image' => $image,
                'audio' => $audio,
                'video' => $video,
                'text' => $text,
                'compressed' => $compressed,
                'pdf' => $pdf
            ];
        }
        return array_merge($image, $video, $audio, $text, $compressed, $pdf);
    }
    static function deleteUploadDir($context='poshtvan')
    {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        
        $uploadBaseDir = self::getUploadBaseDir(false, $context);
        if(!$wp_filesystem->is_dir($uploadBaseDir))
        {
            return false;
        }
        $it = new \RecursiveDirectoryIterator($uploadBaseDir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveTreeIterator::CHILD_FIRST);
        foreach($files as $file)
        {
            if($file->isDir())
            {
                $wp_filesystem->rmdir($file->getPathname());
            }else{
                $wp_upload_dir->delete($file->getPathname());
            }
        }
        $wp_filesystem->rmdir($uploadBaseDir);
        return true;
    }
}