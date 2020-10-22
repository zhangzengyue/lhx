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
class Staff extends AdminBase
{
    
    /**
     * 友情链接列表
     */
    public function staffList()
    {
        $where['s.status']=['neq',-1];
        
        $this->assign('list', $this->logicStaff->getStaffList($where));
        
        return $this->fetch('staff_list');
    }
    
    /**
     * 友情链接添加
     */
    public function staffAdd()
    {
        
        IS_POST && $this->jump($this->logicStaff->staffEdit($this->param));
        $this->assign('list', $this->logicDepartment->getDepartmentList());
        return $this->fetch('staff_edit');
    }
    
    /**
     * 友情链接编辑
     */
    public function staffEdit()
    {
        
        IS_POST && $this->jump($this->logicStaff->staffEdit($this->param));
        
        $info = $this->logicStaff->getStaffInfo(['id' => $this->param['id']]);
        
        $this->assign('info', $info);
        $this->assign('list', $this->logicStaff->getStaffList());
        
        return $this->fetch('staff_edit');
    }
    
    /**
     * 友情链接删除
     */
    public function staffDel($id = 0)
    {
        
        $this->jump($this->logicStaff->staffDel(['id' => $id]));
    }

    /**
     * 推荐列表
     */
    public function staffTuijian()
    {

        //获取当前用户传递的id号
        $info = $this->logicStaff->getStaffInfo(['id' => $this->param['id']]);
        $data['s_id']=$info['id'];

        $this->jump($this->logicTuijian->tuijianAdd($data));

        
    }
}
