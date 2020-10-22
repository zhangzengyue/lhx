<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\controller;

/**
 * 友情链接控制器
 */
class Department extends AdminBase
{
    
    /**
     * 友情链接列表
     */
    public function departmentList()
    {
        
        $this->assign('list', $this->logicDepartment->getDepartmentList());
        
        return $this->fetch('department_list');
    }
    
    /**
     * 友情链接添加
     */
    public function departmentAdd()
    {
        
        IS_POST && $this->jump($this->logicDepartment->departmentEdit($this->param));
        
        return $this->fetch('department_edit');
    }
    
    /**
     * 友情链接编辑
     */
    public function departmentEdit()
    {
        
        IS_POST && $this->jump($this->logicDepartment->departmentEdit($this->param));
        
        $info = $this->logicDepartment->getDepartmentInfo(['id' => $this->param['id']]);
        
        $this->assign('info', $info);
        
        return $this->fetch('department_edit');
    }
    
    /**
     * 友情链接删除
     */
    public function departmentDel($id = 0)
    {
        
        $this->jump($this->logicDepartment->departmentDel(['id' => $id]));
    }
}
