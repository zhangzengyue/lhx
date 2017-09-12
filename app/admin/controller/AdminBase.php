<?php

namespace app\admin\controller;

use app\common\controller\ControllerBase;
use app\admin\logic\AdminBase as LogicAdminBase;
use app\admin\logic\Menu as LogicMenu;
use app\admin\logic\AuthGroupAccess as LogicAuthGroupAccess;


/**
 * 后台基类控制器
 * Class AdminBase
 * @package app\admin\controller
 * @author  Bigotry <3162875@qq.com>
 * @version 1.0
 */
class AdminBase extends ControllerBase
{
    
    // 后台基础逻辑
    protected $adminBaseLogic       = null;
    
    // 菜单逻辑
    protected $menuLogic            = null;
    
    // 授权逻辑
    protected $authGroupAccessLogic = null;
    
    // 授权过的菜单列表
    protected $authMenuList         = [];
    
    // 授权过的菜单url列表
    protected $authMenuUrlList      = [];
    
    // 授权过的菜单树
    protected $authMenuTree         = [];
    
    // 菜单视图
    protected $menuView             = '';
    
    // 面包屑视图
    protected $crumbsView           = '';
    
    /**
     * AdminBase constructor                                      构造方法
     * @param   LogicAdminBase          $adminBaseLogic          后台基础逻辑
     * @param   LogicMenu               $menuLogic               菜单逻辑
     * @param   LogicAuthGroupAccess    $authGroupAccessLogic    权限组授权逻辑
     */
    public function __construct(LogicAdminBase $adminBaseLogic, LogicMenu $menuLogic, LogicAuthGroupAccess $authGroupAccessLogic)
    {
        
        // 执行父类构造方法
        parent::__construct();
        
        // 注入后台逻辑
        $this->adminBaseLogic = $adminBaseLogic;
        
        // 注入菜单逻辑
        $this->menuLogic = $menuLogic;
        
        // 注入授权逻辑
        $this->authGroupAccessLogic = $authGroupAccessLogic;
        
        // 初始化后台模块常量
        $this->initAdminConst();
        
        // 初始化后台模块信息
        $this->initAdminInfo();
    }
    
    /**
     * 初始化后台模块信息
     */
    final private function initAdminInfo()
    {
        
        // 验证登录
        !MEMBER_ID && $this->redirect('Login/login');
        
        // 获取授权菜单列表
        $this->authMenuList = $this->authGroupAccessLogic->getAuthMenuList(MEMBER_ID);
        
        // 获得权限菜单URL列表
        $this->authMenuUrlList = $this->authGroupAccessLogic->getAuthMenuUrlList($this->authMenuList);
        
        // 检查菜单权限
        list($jump_type, $message) = $this->adminBaseLogic->authCheck(URL_MODULE, $this->authMenuUrlList);
        
        // 权限验证不通过则跳转提示
        RESULT_SUCCESS == $jump_type ?: $this->jump($jump_type, $message);
        
        // 初始化基础数据
        IS_AJAX ?: $this->initBaseInfo();
    }
    
    /**
     * 初始化基础数据
     */
    final private function initBaseInfo()
    {
        
        // 获取过滤后的菜单树
        $this->authMenuTree = $this->adminBaseLogic->getMenuTree($this->authMenuList, $this->authMenuUrlList);
       
        // 菜单转换为视图
        $this->menuView = $this->menuLogic->menuToView($this->authMenuTree);
        
        // 菜单自动选择
        $this->menuView = $this->menuLogic->selectMenu($this->menuView);
        
        // 获取面包屑
        $this->crumbsView = $this->menuLogic->getCrumbsView();
        
        // 获取默认标题
        $this->assign('ob_title', $this->menuLogic->getDefaultTitle());
        
        // 菜单视图
        $this->assign('menu_view', $this->menuView);
        
        // 面包屑视图
        $this->assign('crumbs_view', $this->crumbsView);
        
        // 登录会员信息
        $this->assign('member_info', session('member_info'));
    }
    
    /**
     * 初始化后台模块常量
     */
    final private function initAdminConst()
    {
        
        // 会员ID
        defined('MEMBER_ID') or define('MEMBER_ID', is_login());
        
        // 是否为超级管理员
        defined('IS_ROOT')   or define('IS_ROOT', is_administrator());
    }
    
    /**
     * 设置指定标题
     */
    final protected function setTitle($title = '')
    {
        
        $this->assign('ob_title', $title);
    }
    
    /**
     * 重写fetch方法支持权限过滤
     */
    final protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        
        $content = parent::fetch($template, $vars, $replace, $config);
        
        return $this->adminBaseLogic->filter($content, $this->authMenuUrlList);
    }
}
