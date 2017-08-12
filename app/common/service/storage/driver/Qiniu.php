<?php
// +----------------------------------------------------------------------
// | Author: Bigotry <3162875@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service\storage\driver;

use app\common\service\storage\Driver;
use app\common\service\Storage;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/**
 * 七牛云
 */
class Qiniu extends Storage implements Driver
{
    
    /**
     * 驱动基本信息
     */
    public function driverInfo()
    {
        
        return ['driver_name' => '七牛云驱动', 'driver_class' => 'Qiniu', 'driver_describe' => '七牛云存储', 'author' => 'Bigotry', 'version' => '1.0'];
    }
    
    /**
     * 获取驱动参数
     */
    public function getDriverParam()
    {
        
        return ['access_key' => '七牛云密钥AK', 'secret_key' => '七牛云密钥SK', 'bucket_name' => '上传空间名Bucket'];
    }
    
    /**
     * 获取配置信息
     */
    public function config()
    {
        
        return $this->driverConfig('Qiniu');
    }
    
    /**
     * 上传文件
     */
    public function upload($file_id = 0)
    {
        
        $config = $this->config();
        
        $auth = new Auth($config['access_key'], $config['secret_key']);

        $token = $auth->uploadToken($config['bucket_name']);
        
        $uploadMgr = new UploadManager();

        $info = model('picture')->getInfo(['id' => $file_id]);
        
        $path_arr = explode(SYS_DSS, $info['path']); 
  
        $file_path = PATH_PICTURE . $path_arr[0] . DS . $path_arr[1];
        
        $save_path = 'upload' . SYS_DSS . 'picture' . SYS_DSS . $path_arr[0] . SYS_DSS . $path_arr[1];
        
        $result = $uploadMgr->putFile($token, $save_path, $file_path);
        
        $thumb_file_path = PATH_PICTURE . $path_arr[0] . DS . 'thumb' . DS;
        $thumb_save_path = 'upload' . SYS_DSS . 'picture' . SYS_DSS . $path_arr[0] . SYS_DSS . 'thumb' . SYS_DSS;
        
        $uploadMgr->putFile($token, $thumb_save_path . 'small_'   . $path_arr[1]   , $thumb_file_path . 'small_'   . $path_arr[1]);
        $uploadMgr->putFile($token, $thumb_save_path . 'medium_'  . $path_arr[1]   , $thumb_file_path . 'medium_'  . $path_arr[1]);
        $uploadMgr->putFile($token, $thumb_save_path . 'big_'     . $path_arr[1]   , $thumb_file_path . 'big_'     . $path_arr[1]);
        
        if ($result[1] !== null) : return false; endif;
        
        return $result[0]['key'];
    }
}
