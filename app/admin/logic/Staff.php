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

namespace app\admin\logic;

/**
 * 友情链接逻辑
 */
class Staff extends AdminBase
{
    
    /**
     * 获取友情链接列表
     */
    public function getStaffList($where = [], $field = true, $order = '', $paginate = 0)
    {
        // s:Staff表
        $join=$this->modelStaff->alias('s');
        // sf($this->modelStaff->alias('s'));
        // 进行表关联，关联数据s表中的codo和department表中的department_id
        $join=[['department e','s.code=e.deparment_id']];
        // sf($join);
        // 将department表中重命名数据放到s表中
        $field='s.*,e.name as sname';
        // sf($field);
        // 将数据放到$hoin中去
        $this->modelStaff->join=$join;
        return $this->modelStaff->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 友情链接信息编辑
     */
    public function staffEdit($data = [])
    {
        
        
        $validate_result = $this->validateStaff->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateStaff->getError()];
        }
        
        $url = url('staffList');
        
        $result = $this->modelStaff->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '友情链接' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelStaff->getError()];
    }

    /**
     * 获取友情链接信息
     */
    public function getStaffInfo($where = [], $field = true)
    {
        
        return $this->modelStaff->getInfo($where, $field);
    }
    
    /**
     * 友情链接删除
     */
    public function staffDel($where = [])
    {
        $join=$this->modelStaff->alias('s');
        // sf($this->modelStaff->alias('s'));
        $join=[['department e','s.code=e.deparment_id']];
        // sf($join);
        $field='s.*,e.name as sname';
        // sf($field);
        $this->modelStaff->join=$join;
        
        $result = $this->modelStaff->deleteInfo($where);
        
        $result && action_log('删除', '友情链接删除' . '，where：' . http_build_query($where));
       
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelStaff->getError()];
    }
    /**
     * 获得推荐列表
     */
    public function getTuijianList($where = [], $field = true, $order = '', $paginate = 0)
    {
        
        return $this->modelTuijian->getList($where, $field, $order, $paginate);
    }
}
