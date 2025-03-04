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

namespace app\admin\validate;

/**
 * 管理组验证器
 */
class PowerGroup extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'name' => 'require',
    ];

    // 验证提示
    protected $message  =   [
        
        'name.require' => '管理组名称不能为空',
    ];

    // 应用场景
    protected $scene = [
        
        'add'  => ['name'],
        'edit' => ['name'],
    ];
    
}
