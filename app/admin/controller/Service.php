<?php
// +----------------------------------------------------------------------
// | Author: Bigotry <3162875@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\logic\Service as LogicService;

/**
 * 服务控制器
 */
class Service extends AdminBase
{
    
    // 服务逻辑
    private static $serviceLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
        
        self::$serviceLogic = get_sington_object('serviceLogic', LogicService::class);
    }
    
    /**
     * 服务 or 驱动 列表
     */
    public function serviceList($service_name = null)
    {
        
        $title = is_null($service_name) ? '系统服务列表' : '服务驱动列表';
        
        $this->setTitle($title);
        
        $this->assign('list', self::$serviceLogic->getServiceList($service_name));
        
        $view = is_null($service_name) ? 'service_list' : 'driver_list';
        
        return $this->fetch($view);
    }
    
}
