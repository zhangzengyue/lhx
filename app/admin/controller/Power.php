<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Powrtor     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\controller;

/**
 * 权限控制器
 */
class Power extends AdminBase
{
    
    /**
     * 权限组列表
     */
    public function groupList()
    {
        
        $this->assign('list', $this->logicPowerGroup->getPowerGroupList(['jurisdiction_id' => JURISDICTION_ID], true, '', DB_LIST_ROWS));
        
        return $this->fetch('group_list');
    }
    
    /**
     * 权限组添加
     */
    public function groupAdd()
    {
        
        IS_POST && $this->jump($this->logicPowerGroup->groupAdd($this->param));
        
        return $this->fetch('group_edit');
    }
    
    /**
     * 权限组编辑
     */
    public function groupEdit()
    {
        
        IS_POST && $this->jump($this->logicPowerGroup->groupEdit($this->param));
        
        $info = $this->logicPowerGroup->getGroupInfo(['id' => $this->param['id']]);
        
        $this->assign('info', $info);
        
        return $this->fetch('group_edit');
    }
    
    /**
     * 权限组删除
     */
    public function groupDel($id = 0)
    {
        
        $this->jump($this->logicPowerGroup->groupDel(['id' => $id]));
    }
    
    /**
     * 菜单授权
     */
    public function menuPower()
    {
        
        IS_POST && $this->jump($this->logicPowerGroup->setGroupRules($this->param));
        
        // 获取未被过滤的菜单树
        $menu_tree = $this->logicAdminBase->getListTree($this->powerMenuList);
        
        // 菜单转换为多选视图，支持无限级
        $menu_view = $this->logicMenu->menuToCheckboxView($menu_tree);
        
        $this->assign('list', $menu_view);
        
        $this->assign('id', $this->param['id']);
        
        return $this->fetch('menu_power');
    }
}
