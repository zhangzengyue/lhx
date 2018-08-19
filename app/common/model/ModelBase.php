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

namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 模型基类
 */
class ModelBase extends Model
{
    
    // 查询对象
    private static $ob_query = null;

    /**
     * 状态获取器
     */
    public function getStatusTextAttr()
    {
        
        $status = [DATA_DELETE => '删除', DATA_DISABLE => "<span class='badge bg-red'>禁用</span>", DATA_NORMAL => "<span class='badge bg-green'>启用</span>"];
        
        return $status[$this->data[DATA_STATUS_NAME]];
    }
    
    /**
     * 设置数据
     */
    final protected function setInfo($data = [], $where = [])
    {
        
        $pk = $this->getPk();
        
        if (empty($data[$pk])) {
            
            $this->allowField(true)->save($data, $where);
            
            return $this->getQuery()->getLastInsID();
            
        } else {
            
            is_object($data) && $data = $data->toArray();
            
            !empty($data[TIME_CT_NAME]) && is_string($data[TIME_CT_NAME]) && $data[TIME_CT_NAME] = strtotime($data[TIME_CT_NAME]);
            
            $default_where[$pk] = $data[$pk];
            
            return $this->updateInfo(array_merge($default_where, $where), $data);
        }
    }
    
    /**
     * 更新数据
     */
    final protected function updateInfo($where = [], $data = [])
    {
        
        $data[TIME_UT_NAME] = TIME_NOW;
        
        return $this->allowField(true)->save($data, $where);
    }
    
    /**
     * 统计数据
     */
    final protected function stat($where = [], $stat_type = 'count', $field = 'id')
    {
        
        return $this->where($where)->$stat_type($field);
    }
    
    /**
     * 设置数据列表
     */
    final protected function setList($data_list = [], $replace = false)
    {
        
        $return_data = $this->saveAll($data_list, $replace);
        
        return $return_data;
    }
    
    /**
     * 设置某个字段值
     */
    final protected function setFieldValue($where = [], $field = '', $value = '')
    {
        
        return $this->updateInfo($where, [$field => $value]);
    }
    
    /**
     * 删除数据
     */
    final protected function deleteInfo($where = [], $is_true = false)
    {
        
        if ($is_true) {
            
            $return_data = $this->where($where)->delete();
            
        } else {
            
            $return_data = $this->setFieldValue($where, DATA_STATUS_NAME, DATA_DELETE);
        }
        
        return $return_data;
    }
    
    /**
     * 获取某个列的数组
     */
    final protected function getColumn($where = [], $field = '', $key = '')
    {
        
        return Db::name($this->name)->where($where)->column($field, $key);
    }
    
    /**
     * 获取某个字段的值
     */
    final protected function getValue($where = [], $field = '', $default = null, $force = false)
    {
        
        return Db::name($this->name)->where($where)->value($field, $default, $force);
    }
    
    /**
     * 获取单条数据
     */
    final protected function getInfo($where = [], $field = true)
    {
        
        self::$ob_query = $this->where($where)->field($field);
        
        !empty($this->join)  && self::$ob_query = self::$ob_query->join($this->join);
        
        return $this->getResultData(DATA_DISABLE);
    }
    
    /**
     * 获取列表数据
     * 若不需要分页 $paginate 设置为 false
     */
    final protected function getList($where = [], $field = true, $order = '', $paginate = 0)
    {
        
        if (empty($this->join) && !isset($where[DATA_STATUS_NAME])) {
            
            $where[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }
        
        self::$ob_query = $this->where($where)->order($order)->field($field);
        
        !empty($this->join)  && self::$ob_query = self::$ob_query->join($this->join);
        
        !empty($this->group) && self::$ob_query = self::$ob_query->group($this->group);
    
        !empty($this->limit) && self::$ob_query = self::$ob_query->limit($this->limit);
        
        return $this->getResultData($paginate);
    }
    
    /**
     * 获取结果数据
     */
    final protected function getResultData($paginate = 0)
    {
        
        $result_data = null;
        
        $backtrace = debug_backtrace(false, 2);

        array_shift($backtrace);

        $function = $backtrace[0]['function'];

        if ($function == 'getList') {

            $list_rows = input('list_rows');
            
            if (false != $paginate && !empty($list_rows)) {
                
                $paginate = $list_rows;
                
            } elseif (false != $paginate && empty($paginate)) {
                
                $paginate = DB_LIST_ROWS;
            }

            $result_data = false !== $paginate ? self::$ob_query->paginate($paginate, false, ['query' => request()->param()]) : self::$ob_query->select();

        } else {

            $result_data = self::$ob_query->find();
        }

        self::$ob_query->removeOption();

        return $result_data;
    }
    
    /**
     * 原生查询
     */
    final protected function query($sql = '')
    {
        
        return Db::query($sql);
    }
    
    /**
     * 原生执行
     */
    final protected function execute($sql = '')
    {
        
        return Db::execute($sql);
    }
    
    /**
     * 重写获取器 兼容 模型|逻辑|验证|服务 层实例获取
     */
    public function __get($name)
    {
        
        $layer = $this->getLayerPrefix($name);
        
        if (false === $layer) {
            
            return parent::__get($name);
        }
        
        $model = sr($name, $layer);
        
        return LAYER_VALIDATE_NAME == $layer ? validate($model) : model($model, $layer);
    }
    
    /**
     * 获取层前缀
     */
    public function getLayerPrefix($name)
    {
        
        $layer = false;
        
        $layer_array = [LAYER_MODEL_NAME, LAYER_LOGIC_NAME, LAYER_VALIDATE_NAME, LAYER_SERVICE_NAME];
        
        foreach ($layer_array as $v)
        {
            if (str_prefix($name, $v)) {
                
                $layer = $v;
                
                break;
            }
        }
        
        return $layer;
    }
}
