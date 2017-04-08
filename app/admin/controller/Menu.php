<?php

namespace app\admin\controller;

class Menu extends AdminBase
{
    
    public static $menuLogic = null;
    
    //基类初始化
    public function _initialize()
    {
        
        parent::_initialize();
        
        !isset(self::$menuLogic) && self::$menuLogic = load_model('Menu');
    }
    
    /**
     * 菜单列表
     */
    public function menuList()
    {
        
        $this->setTitle('菜单管理');
        
        $where = empty($this->param['pid']) ? array('pid' => 0) : array('pid' => $this->param['pid']);
        
        $this->assign('list', self::$menuLogic->getMenuList($where));
        
        $this->assign('pid', $where['pid']);
        
        return $this->fetch('menu_list');
    }
    
    
    /**
     * 菜单树获取
     */
    public function getMenuTreeData()
    {
        
        $menu_tree_data = load_model('AdminBase')->getMenuList();
        
        $menu_tree = self::$menuLogic->menuToTree($menu_tree_data);
        
        $this->assign('menu_tree', $menu_tree);
    }
    
    
    /**
     * 菜单添加
     */
    public function menuAdd()
    {
        
        $this->setTitle('菜单添加');
        
        $this->param['module'] = MODULE_NAME;
        
        IS_POST && $this->jump(self::$menuLogic->menuAdd($this->param));
        
        $this->getMenuTreeData();
        
        !empty($this->param['pid']) && $this->assign('info', array('pid'=> $this->param['pid']));
        
        return  $this->fetch('menu_edit');
    }
    
    /**
     * 菜单编辑
     */
    public function menuEdit()
    {
        
        $this->setTitle('菜单编辑');
        
        IS_POST && $this->jump(self::$menuLogic->menuEdit($this->param));
        
        $info = self::$menuLogic->getMenuInfo(array('id' => $this->param['id']));
        
        $this->assign('info', $info);
        
        $this->getMenuTreeData();
        
        return $this->fetch('menu_edit');
    }
    
    /**
     * 菜单删除
     */
    public function menuDel($id = 0)
    {
        
        $this->jump(self::$menuLogic->menuDel(array('id' => $id)));
    }
}
