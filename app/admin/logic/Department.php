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
class Department extends AdminBase
{
    
    /**
     * 获取友情链接列表
     */
    public function getDepartmentList($where = [], $field = true, $order = '', $paginate = 0)
    {
        
        return $this->modelDepartment->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 友情链接信息编辑
     */
    public function departmentEdit($data = [])
    {
        
        $validate_result = $this->validateDepartment->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateDepartment->getError()];
        }
        
        $url = url('departmentList');
        
        $result = $this->modelDepartment->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '友情链接' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelDepartment->getError()];
    }

    /**
     * 获取友情链接信息
     */
    public function getDepartmentInfo($where = [], $field = true)
    {
        
        return $this->modelDepartment->getInfo($where, $field);
    }
    
    /**
     * 友情链接删除
     */
    public function departmentDel($where = [])
    {
        
        $result = $this->modelDepartment->deleteInfo($where);
        
        $result && action_log('删除', '友情链接删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelDepartment->getError()];
    }
}
