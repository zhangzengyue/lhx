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

namespace app\admin\controller;

/**
 * 会员控制器
 */
class Jurisdiction extends AdminBase
{

    /**
     * 会员授权
     */
    public function jurisdictionPower()
    {
        
        IS_POST && $this->jump($this->logicJurisdiction->addToGroup($this->param));
        
        // 所有的权限组
        $group_list = $this->logicPowerGroup->getPowerGroupList(['jurisdiction_id' => JURISDICTION_ID]);
        
        // 会员当前权限组
        $jurisdiction_group_list = $this->logicPowerGroupAccess->getJurisdictionGroupInfo($this->param['id']);

        // 选择权限组
        $list = $this->logicPowerGroup->selectPowerGroupList($group_list, $jurisdiction_group_list);
        
        $this->assign('list', $list);
        
        $this->assign('id', $this->param['id']);
        
        return $this->fetch('jurisdiction_power');
    }
    
    /**
     * 会员列表
     */
    public function jurisdictionList()
    {
        
        $where = $this->logicJurisdiction->getWhere($this->param);
        
        $this->assign('list', $this->logicJurisdiction->getJurisdictionList($where));
        
        return $this->fetch('jurisdiction_list');
    }
    
    /**
     * 会员导出
     */
    public function exportJurisdictionList()
    {
        
        $where = $this->logicJurisdiction->getWhere($this->param);
        
        $this->logicJurisdiction->exportJurisdictionList($where);
    }
    
    /**
     * 会员添加
     */
    public function jurisdictionAdd()
    {
        
        IS_POST && $this->jump($this->logicJurisdiction->jurisdictionAdd($this->param));
        
        return $this->fetch('jurisdiction_add');
    }
    
    /**
     * 会员编辑
     */
    public function jurisdictionEdit()
    {
        
        IS_POST && $this->jump($this->logicJurisdiction->jurisdictionEdit($this->param));
        
        $info = $this->logicJurisdiction->getJurisdictionInfo(['id' => $this->param['id']]);
        
        $this->assign('info', $info);
        
        return $this->fetch('jurisdiction_edit');
    }
    
    /**
     * 会员删除
     */
    public function jurisdictionDel($id = 0)
    {
        
        return $this->jump($this->logicJurisdiction->jurisdictionDel(['id' => $id]));
    }
    
    /**
     * 修改密码
     */
    public function editPassword()
    {
        
        IS_POST && $this->jump($this->logicJurisdiction->editPassword($this->param));
        
        $info = $this->logicJurisdiction->getJurisdictionInfo(['id' => JURISDICTION_ID]);
        
        $this->assign('info', $info);
        
        return $this->fetch('edit_password');
    }
}
