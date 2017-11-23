<?php
// +----------------------------------------------------------------------
// | Author: Bigotry <3162875@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

/**
 * 会员逻辑
 */
class Member extends AdminBase
{
    
    // 会员模型
    public static $memberModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$memberModel = model($this->name);
    }
    
    /**
     * 获取会员信息
     */
    public function getMemberInfo($where = [], $field = true)
    {
        
        return self::$memberModel->getInfo($where, $field);
    }
    
    /**
     * 获取会员列表
     */
    public function getMemberList($where = [], $field = true, $order = '')
    {
        
        return self::$memberModel->getList($where, $field, $order);
    }
    
    /**
     * 导出会员列表
     */
    public function exportMemberList($where = [], $field = true, $order = '')
    {
        
        $list = self::$memberModel->getList($where, $field, $order, false);
        
        foreach ($list as $info)
        {
            
            $info['leader_nickname'] = self::$memberModel->getValue(['id' => $info['leader_id']], 'nickname', '无');
        }
        
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
        
        !empty($data['search_data']) && $where['nickname|username|email|mobile'] = ['like', '%'.$data['search_data'].'%'];
        
        if (!is_administrator()) {
            
            $member = session('member_info');
            
            if ($member['is_share_member']) {
                
                $ids = $this->getInheritMemberIds(MEMBER_ID);
                
                $ids[] = MEMBER_ID;
                
                $where['leader_id'] = ['in', $ids];
                
            } else {
                
                $where['leader_id'] = MEMBER_ID;
            }
        }
        
        return $where;
    }
    
    /**
     * 获取存在继承关系的会员ids
     */
    public function getInheritMemberIds($id = 0, $data = [])
    {
        
        $member_id = self::$memberModel->getValue(['id' => $id, 'is_share_member' => DATA_NORMAL], 'leader_id');
        
        if (empty($member_id)) {
            
            return $data;
        } else {
            
            $data[] = $member_id;
            
            return $this->getInheritMemberIds($member_id, $data);
        }
    }
    
    /**
     * 会员添加到用户组
     */
    public function addToGroup($data = [])
    {
        
        if (SYS_ADMINISTRATOR_ID == $data['id']) : return [RESULT_ERROR, '天神不能授权哦~']; endif;
        
        $model = model('AuthGroupAccess');
        
        $where = ['member_id' => ['in', $data['id']]];
        
        $model->deleteInfo($where, true);
        
        $url = url('memberList');
        
        if (empty($data['group_id'])) : return [RESULT_SUCCESS, '会员授权成功', $url]; endif;
        
        $add_data = [];
        
        foreach ($data['group_id'] as $group_id) {
            
            $add_data[] = ['member_id' => $data['id'], 'group_id' => $group_id];
        }
        
        $result = $model->setList($add_data);
        
        $result && action_log('授权', '会员授权，id：' . $data['id']);
        
        return $result ? [RESULT_SUCCESS, '会员授权成功', $url] : [RESULT_ERROR, $model->getError()];
    }
    
    /**
     * 会员添加
     */
    public function memberAdd($data = [])
    {
        
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('add')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('memberList');
        
        $data['nickname']  = $data['username'];
        $data['leader_id'] = MEMBER_ID;
        $data['is_inside'] = DATA_NORMAL;
        
        $result = self::$memberModel->setInfo($data);
        
        $result && action_log('新增', '新增会员，username：' . $data['username']);
        
        return $result ? [RESULT_SUCCESS, '会员添加成功', $url] : [RESULT_ERROR, self::$memberModel->getError()];
    }
    
    /**
     * 设置会员信息
     */
    public function setMemberValue($where = [], $field = '', $value = '')
    {
        
        return self::$memberModel->setFieldValue($where, $field, $value);
    }
    
    /**
     * 会员删除
     */
    public function memberDel($where = [])
    {
        
        if (SYS_ADMINISTRATOR_ID == $where['id'] || MEMBER_ID == $where['id']) : return [RESULT_ERROR, '天神和自己不能删除哦~']; endif;
        
        $result = self::$memberModel->deleteInfo($where);
                
        $result && action_log('删除', '删除会员，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '会员删除成功'] : [RESULT_ERROR, self::$memberModel->getError()];
    }
}
