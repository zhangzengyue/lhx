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
class Tuijian extends AdminBase
{
    
    /**
     * 获取友情链接列表
     */
    public function getTuijianList($where = [], $field = true, $order = '', $paginate = 0)
    {
        
        return $this->modelTuijian->getList($where, $field, $order, $paginate);
    }

        /**
     * 友情链接信息编辑
     */
    public function tuijianAdd($data = [])
    {
        
        $validate_result = $this->validateStaff->scene('edit')->check($data);
        

        
        $url = url('Tuijian/tuijianList');
        
        $result = $this->modelTuijian->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '友情链接' . $handle_text . '，name：' . $data['id']);

        $result=true;
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelStaff->getError()];
    }
}
