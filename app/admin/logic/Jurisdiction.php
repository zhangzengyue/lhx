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
 * 会员逻辑
 */
class Jurisdiction extends AdminBase
{
    
    /**
     * 获取会员信息
     */
    public function getJurisdictionInfo($where = [], $field = true)
    {
        
        $info = $this->modelJurisdiction->getInfo($where, $field);
        
        $info['leader_nickname'] = $this->modelJurisdiction->getValue(['id' => $info['leader_id']], 'nickname');
        
        return $info;
    }
    
    /**
     * 获取会员列表
     */
    public function getJurisdictionList($where = [], $field = 'm.*,b.nickname as leader_nickname', $order = '', $paginate = DB_LIST_ROWS)
    {
        
        $this->modelJurisdiction->alias('m');
        
        $join = [
                    [SYS_DB_PREFIX . 'jurisdiction b', 'm.leader_id = b.id', 'LEFT'],
                ];
        
        $where['m.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        
        $this->modelJurisdiction->join = $join;
        
        return $this->modelJurisdiction->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 导出会员列表
     */
    public function exportJurisdictionList($where = [], $field = 'm.*,b.nickname as leader_nickname', $order = '')
    {
        
        $list = $this->getJurisdictionList($where, $field, $order, false);
        
        $titles = "昵称,用户名,邮箱,手机,注册时间,上级";
        $keys   = "nickname,username,email,mobile,create_time,leader_nickname";
        
        action_log('导出', '导出会员列表');
        
        export_excel($titles, $keys, $list, '会员列表');
    }
    
    /**
     * 获取会员列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['m.nickname|m.username|m.email|m.mobile'] = ['like', '%'.$data['search_data'].'%'];
        
        if (!is_administrator()) {
            
            $jurisdiction = session('jurisdiction_info');
            
            if ($jurisdiction['is_share_jurisdiction']) {
                
                $ids = $this->getInheritJurisdictionIds(JURISDICTION_ID);
                
                $ids[] = JURISDICTION_ID;
                
                $where['m.leader_id'] = ['in', $ids];
                
            } else {
                
                $where['m.leader_id'] = JURISDICTION_ID;
            }
        }
        
        return $where;
    }
    
    /**
     * 获取存在继承关系的会员ids
     */
    public function getInheritJurisdictionIds($id = 0, $data = [])
    {
        
        $jurisdiction_id = $this->modelJurisdiction->getValue(['id' => $id, 'is_share_jurisdiction' => DATA_NORMAL], 'leader_id');
        
        if (empty($jurisdiction_id)) {
            
            return $data;
        } else {
            
            $data[] = $jurisdiction_id;
            
            return $this->getInheritJurisdictionIds($jurisdiction_id, $data);
        }
    }
    
    /**
     * 获取会员的所有下级会员
     */
    public function getSubJurisdictionIds($id = 0, $data = [])
    {
        
        $jurisdiction_list = $this->modelJurisdiction->getList(['leader_id' => $id], 'id', 'id asc', false);
        
        foreach ($jurisdiction_list as $v)
        {
            
            if (!empty($v['id'])) {
                
                $data[] = $v['id'];
            
                $data = array_unique(array_merge($data, $this->getSubJurisdictionIds($v['id'], $data)));
            }
            
            continue;
        }
            
        return $data;
    }
    
    /**
     * 会员添加到用户组
     */
    public function addToGroup($data = [])
    {
        
        $url = url('jurisdictionList');
        
        if (SYS_ADMINISTRATOR_ID == $data['id']) {
            
            return [RESULT_ERROR, '天神不能授权哦~', $url];
        }
        
        $where = ['jurisdiction_id' => ['in', $data['id']]];
        
        $this->modelPowerGroupAccess->deleteInfo($where, true);
        
        if (empty($data['group_id'])) {
            
            return [RESULT_SUCCESS, '会员授权成功', $url];
        }
        
        $add_data = [];
        
        foreach ($data['group_id'] as $group_id) {
            
            $add_data[] = ['jurisdiction_id' => $data['id'], 'group_id' => $group_id];
        }
        
        if ($this->modelPowerGroupAccess->setList($add_data)) {
            
            action_log('授权', '会员授权，id：' . $data['id']);
        
            $this->logicPowerGroup->updateSubPowerByJurisdiction($data['id']);
            
            return [RESULT_SUCCESS, '会员授权成功', $url];
        } else {
            
            return [RESULT_ERROR, $this->modelPowerGroupAccess->getError()];
        }
    }
    
    /**
     * 会员添加
     */
    public function jurisdictionAdd($data = [])
    {
        
        $validate_result = $this->validateJurisdiction->scene('add')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateJurisdiction->getError()];
        }
        
        $url = url('jurisdictionList');
        
        $data['nickname']  = $data['username'];
        $data['leader_id'] = JURISDICTION_ID;
        $data['is_inside'] = DATA_NORMAL;
        
        $data['password'] = data_md5_key($data['password']);
        
        $result = $this->modelJurisdiction->setInfo($data);
        
        $result && action_log('新增', '新增会员，username：' . $data['username']);
        
        return $result ? [RESULT_SUCCESS, '会员添加成功', $url] : [RESULT_ERROR, $this->modelJurisdiction->getError()];
    }
    
    /**
     * 会员编辑
     */
    public function jurisdictionEdit($data = [])
    {
        
        $validate_result = $this->validateJurisdiction->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateJurisdiction->getError()];
        }
        
        $url = url('jurisdictionList');
        
        $result = $this->modelJurisdiction->setInfo($data);
        
        $result && action_log('编辑', '编辑会员，id：' . $data['id']);
        
        return $result ? [RESULT_SUCCESS, '会员编辑成功', $url] : [RESULT_ERROR, $this->modelJurisdiction->getError()];
    }
    
    /**
     * 修改密码
     */
    public function editPassword($data = [])
    {
        
        $validate_result = $this->validateJurisdiction->scene('password')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateJurisdiction->getError()];
        }
        
        $jurisdiction = $this->getJurisdictionInfo(['id' => $data['id']]);
        
        if (data_md5_key($data['old_password']) != $jurisdiction['password']) {
            
            return [RESULT_ERROR, '旧密码输入不正确'];
        }
        
        $data['id'] = JURISDICTION_ID;
        
        $url = url('index/index');
        
		$data['password'] = data_md5_key($data['password']);
		
        $result = $this->modelJurisdiction->setInfo($data);
        
        $result && action_log('编辑', '会员密码修改，id：' . $data['id']);
        
        return $result ? [RESULT_SUCCESS, '密码修改成功', $url] : [RESULT_ERROR, $this->modelJurisdiction->getError()];
    }
    
    /**
     * 设置会员信息
     */
    public function setJurisdictionValue($where = [], $field = '', $value = '')
    {
        
        return $this->modelJurisdiction->setFieldValue($where, $field, $value);
    }
    
    /**
     * 会员删除
     */
    public function jurisdictionDel($where = [])
    {
        
        $url = url('jurisdictionList');
        
        if (SYS_ADMINISTRATOR_ID == $where['id'] || JURISDICTION_ID == $where['id']) {
            
            return [RESULT_ERROR, '天神和自己不能删除哦~', $url];
        }
        
        $result = $this->modelJurisdiction->deleteInfo($where);
                
        $result && action_log('删除', '删除会员，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '会员删除成功', $url] : [RESULT_ERROR, $this->modelJurisdiction->getError(), $url];
    }
}
