<?php
// +----------------------------------------------------------------------
// | Author: Bigotry <3162875@qq.com>
// +----------------------------------------------------------------------

namespace app\common\behavior;

use think\Loader;

/**
 * 初始化基础信息行为
 */
class InitBase
{

    /**
     * 行为入口
     */
    public function run()
    {
        
        // 初始化系统常量
        $this->initConst();
        
        // 初始化配置信息
        $this->initConfig();
        
        // 初始化数据库信息
        $this->initDbInfo();
        
        // 注册命名空间
        $this->registerNamespace();
    }
    
    /**
     * 初始化数据库信息
     */
    private function initDbInfo()
    {
        
        $database_config = config('database');
        
        $list_rows = config('list_rows');
    
        define('DB_PREFIX', $database_config['prefix']);
        
        empty($list_rows) ? define('DB_LIST_ROWS', 10) : define('DB_LIST_ROWS', $list_rows);
    }
    
    /**
     * 初始化系统常量
     */
    private function initConst()
    {
        
        // 通用模块名称
        define('MODULE_COMMON_NAME', 'common');
        
        // 逻辑层名称
        define('LAYER_LOGIC_NAME', 'logic');

        // 数据模型层名称
        define('LAYER_MODEL_NAME', 'model');

        // 系统服务层名称
        define('LAYER_SERVICE_NAME', 'service');

        // 系统控制器层名称
        define('LAYER_CONTROLLER_NAME', 'controller');

        // 返回结果集key
        define('RESULT_SUCCESS' , 'success');
        define('RESULT_ERROR'   , 'error');
        define('RESULT_REDIRECT', 'redirect');
        define('RESULT_MESSAGE' , 'message');
        define('RESULT_URL'     , 'url');
        define('RESULT_DATA'    , 'data');

        // 数据状态
        define('DATA_STATUS' ,  'status');
        define('DATA_NORMAL' ,  1);
        define('DATA_DISABLE',  0);
        define('DATA_DELETE' , -1);
        
        //时间常量
        define('DATA_CREATE_TIME' ,  'create_time');
        define('DATA_UPDATE_TIME' ,  'update_time');
        define('NOW_TIME' , time());
        
        //插件目录名称及插件目录路径
        define('ADDON_DIR_NAME', 'addon');
        define('ADDON_PATH', ROOT_PATH . ADDON_DIR_NAME . DS);
        
        // 系统超级管理员ID
        define('ADMINISTRATOR_ID', 1);
        
        // 系统加密KEY
        define('DATA_ENCRYPT_KEY', '}a!vI9wX>l2V|gfZp{8`;jzR~6Y1_q-e,#"MN=r:');
    }
    
    /**
     * 初始化配置信息
     */
    private function initConfig()
    {
        
        // 配置模型
        $model = model(MODULE_COMMON_NAME.'/Config');
        
        // 获取所有配置信息
        $config_list = $model->all();
        
        // 写入配置
        foreach ($config_list as $info) {
            
           config($info['name'], $info['value']);
        }
    }
    
    /**
     * 注册命名空间
     */
    private function registerNamespace()
    {
        
        // 注册插件根命名空间
        Loader::addNamespace(ADDON_DIR_NAME, ADDON_PATH);
    }
}
