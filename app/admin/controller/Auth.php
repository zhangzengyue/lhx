<?php
// +----------------------------------------------------------------------
// | Author: Bigotry <3162875@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\logic\AuthGroup as LogicAuthGroup;

/**
 * 权限控制器
 */
class Auth extends AdminBase
{
    
    // 权限组逻辑
    public static $authGroupLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
        
        self::$authGroupLogic = get_sington_object('authGroupLogic', LogicAuthGroup::class);
    }
    
    /**
     * 权限组列表
     */
    public function groupList()
    {
        
        $this->assign('list', self::$authGroupLogic->getAuthGroupList(['member_id' => MEMBER_ID], true, '', DB_LIST_ROWS));
        
        return $this->fetch('group_list');
    }
    
    /**
     * 权限组添加
     */
    public function groupAdd()
    {
        
        IS_POST && $this->jump(self::$authGroupLogic->groupAdd($this->param));
        
        return $this->fetch('group_edit');
    }
    
    /**
     * 权限组编辑
     */
    public function groupEdit()
    {
        
        IS_POST && $this->jump(self::$authGroupLogic->groupEdit($this->param));
        
        $info = self::$authGroupLogic->getGroupInfo(['id' => $this->param['id']]);
        
        $this->assign('info', $info);
        
        return $this->fetch('group_edit');
    }
    
    /**
     * 权限组删除
     */
    public function groupDel($id = 0)
    {
        
        $this->jump(self::$authGroupLogic->groupDel(['id' => $id]));
    }
    
    /**
     * 菜单授权
     */
    public function menuAuth()
    {
        
        IS_POST && $this->jump(self::$authGroupLogic->setGroupRules($this->param));
        
        // 获取未被过滤的菜单树
        $menu_tree = $this->adminBaseLogic->getListTree($this->authMenuList);
        
        // 菜单转换为多选视图，支持无限级
        $menu_view = $this->menuLogic->menuToCheckboxView($menu_tree);
        
        $this->assign('list', $menu_view);
        
        $this->assign('id', $this->param['id']);
        
        return $this->fetch('menu_auth');
    }
}
