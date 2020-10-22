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
class Tuijian extends AdminBase
{
    
    /**
     * 友情链接列表
     */
    public function tuijianList()
    {
        
        $this->assign('list', $this->logicTuijian->getTuijianList());
        
        return $this->fetch('Staff/tuijian_list');
    }
    
    /**
     * 友情链接添加
     */
    public function tuijianAdd()
    {
        
        IS_POST && $this->jump($this->logicTuijian->tuijianEdit($this->param));
        
        return $this->fetch('tuijian_edit');
    }
    
    /**
     * 友情链接编辑
     */
    public function tuijianEdit()
    {
        
        IS_POST && $this->jump($this->logicTuijian->tuijianEdit($this->param));
        
        $info = $this->logicTuijian->getTuijianInfo(['id' => $this->param['id']]);
        
        $this->assign('info', $info);
        
        return $this->fetch('tuijian_edit');
    }
    
    /**
     * 友情链接删除
     */
    public function tuijianDel($id = 0)
    {
        
        $this->jump($this->logicTuijian->tuijianDel(['id' => $id]));
    }
}
