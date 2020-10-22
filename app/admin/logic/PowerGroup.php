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
 * 权限组逻辑
 */
class PowerGroup extends AdminBase
{
    
    /**
     * 获取权限分组列表
     */
    public function getPowerGroupList($where = [], $field = true, $order = '', $paginate = false)
    {
        
        return $this->modelPowerGroup->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 权限组添加
     */
    public function groupAdd($data = [])
    {
        
        $validate_result = $this->validatePowerGroup->scene('add')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validatePowerGroup->getError()];
        }
        
        $url = url('groupList');
        
        $data['jurisdiction_id'] = JURISDICTION_ID;
        
        $result = $this->modelPowerGroup->setInfo($data);
        
        $result && action_log('新增', '新增权限组，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '权限组添加成功', $url] : [RESULT_ERROR, $this->modelPowerGroup->getError()];
    }
    
    /**
     * 权限组编辑
     */
    public function groupEdit($data = [])
    {
        
        $validate_result = $this->validatePowerGroup->scene('edit')->check($data);
        
        if (!$validate_result) {
         
            return [RESULT_ERROR, $this->validatePowerGroup->getError()];
        }
        
        $url = url('groupList');
        
        $result = $this->modelPowerGroup->setInfo($data);
        
        $result && action_log('编辑', '编辑权限组，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '权限组编辑成功', $url] : [RESULT_ERROR, $this->modelPowerGroup->getError()];
    }
    
    /**
     * 权限组删除
     */
    public function groupDel($where = [])
    {
        
        $result = $this->modelPowerGroup->deleteInfo($where);
        
        $result && action_log('删除', '删除权限组，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '权限组删除成功'] : [RESULT_ERROR, $this->modelPowerGroup->getError()];
    }
    
    /**
     * 获取权限组信息
     */
    public function getGroupInfo($where = [], $field = true)
    {
        
        return $this->modelPowerGroup->getInfo($where, $field);
    }

    /**
     * 设置用户组权限节点
     */
    public function setGroupRules($data = [])
    {
        
        $data['rules'] = !empty($data['rules']) ? implode(',', array_unique($data['rules'])) : '';
        
        $url = url('groupList');
        
        $result = $this->modelPowerGroup->setInfo($data);
        
        if ($result) {
            
            action_log('授权', '设置权限组权限，id：' . $data['id']);
            
            $this->updateSubPowerByGroup($data['id']);

            return [RESULT_SUCCESS, '权限设置成功', $url];
        } else {
            
            return [RESULT_ERROR, $this->modelPowerGroup->getError()];
        }
    }
    
    /**
     * 选择权限组
     */
    public function selectPowerGroupList($group_list = [], $jurisdiction_group_list = [])
    {
        
        $jurisdiction_group_ids = array_extract($jurisdiction_group_list, 'group_id');
        
        foreach ($group_list as &$info) {
            
            in_array($info['id'], $jurisdiction_group_ids) ? $info['tag'] = 'active' :  $info['tag'] = '';
        }
            
        return $group_list;
    }
    
    /**
     * 递归更新下级权限节点，确保下级权限不能超越上级
     * 若上级某权限被收回，则下级对应的权限同样被收回
     * 按会员更新
     */
    public function updateSubPowerByJurisdiction($jurisdiction_id = 0)
    {
        
        $group_list = $this->logicPowerGroupAccess->getJurisdictionGroupInfo($jurisdiction_id);
        
        $rules_str_list = array_extract($group_list, 'rules');
        
        $rules_array_list = array_map("str2arr", $rules_str_list);
        
        $rules_array = [];
        
        foreach ($rules_array_list as $v) {
            
            $rules_array = array_merge($rules_array, $v);
        }
        
        // 当前授权会员的所有权限节点数组
        $rules_unique_array = array_unique($rules_array);
        
        $sub_jurisdiction_ids = $this->logicJurisdiction->getSubJurisdictionIds($jurisdiction_id);
        
        $sub_group_list = $this->logicPowerGroupAccess->getJurisdictionGroupInfo($sub_jurisdiction_ids);
        
        // 所有下级的权限组id集合
        $sub_group_ids = array_unique(array_extract($sub_group_list, 'group_id'));
        
        $this->updateGroupRulesByStandard($rules_unique_array, $sub_group_ids);
    }
    
    /**
     * 递归更新下级权限节点，确保下级权限不能超越上级
     * 若上级某权限被收回，则下级对应的权限同样被收回
     * 按权限组更新
     */
    public function updateSubPowerByGroup($group_id = 0)
    {
        
        $group_list = $this->logicPowerGroupAccess->getPowerGroupAccessList(['group_id' => $group_id]);
        
        $jurisdiction_arr_ids = array_unique(array_extract($group_list, 'jurisdiction_id'));
        
        foreach ($jurisdiction_arr_ids as $id) {
            
            $this->updateSubPowerByJurisdiction($id);
        }
    }
    
    /**
     * 按参数$standard_rules_array权限节点数组标准，将参数$group_ids权限组ID数组下的权限节点全部更新
     */
    public function updateGroupRulesByStandard($standard_rules_array = [], $group_ids = [])
    {
        
        $group_list = $this->getPowerGroupList(['id' => ['in', $group_ids]]);
        
        foreach ($group_list as $v)
        {
            
            $rules_arr = str2arr($v['rules']);
            
            foreach ($rules_arr as $kk => $vv)
            {
                if (!in_array($vv, $standard_rules_array)) {
                    
                    unset($rules_arr[$kk]);
                }
            }
            
            $v['rules'] = arr2str(array_values($rules_arr));
            
            $this->modelPowerGroup->setFieldValue(['id' => $v['id']], 'rules', $v['rules']);
        }
    }
}
