<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Poweror     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\logic;

/**
 * 授权逻辑
 */
class PowerGroupAccess extends AdminBase
{
    
    /**
     * 获得管理菜单列表
     */
    public function getPowerMenuList($jurisdiction_id = 0)
    {
        
        $sort = 'sort';
        
        if (IS_ROOT) {
            
            return $this->logicMenu->getMenuList([], true, $sort);
        }
        
        // 获取用户组列表
        $group_list = $this->getJurisdictionGroupInfo($jurisdiction_id);
        
        $menu_ids = [];
        
        foreach ($group_list as $group_info) {
            
            // 合并多个分组的管理节点并去重
            !empty($group_info['rules']) && $menu_ids = array_unique(array_merge($menu_ids, explode(',', trim($group_info['rules'], ','))));
        }
        
        // 若没有管理节点则返回
        if (empty($menu_ids)) {
            
            return $menu_ids;
        }
        
        // 查询条件
        $where = ['id' => ['in', $menu_ids]];
        
        return $this->logicMenu->getMenuList($where, true, $sort);
    }
    
    /**
     * 获得管理菜单URL列表
     */
    public function getPowerMenuUrlList($power_menu_list = [])
    {
        
        $power_list = [];
        
        foreach ($power_menu_list as $info) {
            
            $power_list[] = $info['url'];
        }

        return $power_list;
    }
    
    /**
     * 获取会员所属管理组信息
     */
    public function getJurisdictionGroupInfo($jurisdiction_id = 0)
    {
        
        $this->modelPowerGroupAccess->alias('a');
        
        is_array($jurisdiction_id) ? $where['a.jurisdiction_id'] = ['in', $jurisdiction_id] : $where['a.jurisdiction_id'] = $jurisdiction_id;
        
        $where['a.status']    = DATA_NORMAL;
        
        $field = 'a.jurisdiction_id, a.group_id, g.name, g.describe, g.rules';
        
        $join = [
                    [SYS_DB_PREFIX . 'power_group g', 'a.group_id = g.id'],
                ];
        
        $this->modelPowerGroupAccess->join = $join;
        
        return $this->modelPowerGroupAccess->getList($where, $field, '', false);
    }
    
    /**
     * 获取授权列表
     */
    public function getPowerGroupAccessList($where = [], $field = 'jurisdiction_id,group_id', $order = 'jurisdiction_id', $paginate = false)
    {
        
        return $this->modelPowerGroupAccess->getList($where, $field, $order, $paginate);
    }
}
