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

namespace app\admin\validate;

/**
 * 友情链接验证器
 */
class Staff extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'name'              => 'require|unique:staff',
        'url'               => 'require',
    ];

    // 验证提示
    protected $message  =   [
        
        'name.require'              => '链接名称不能为空',
        'name.unique'               => '链接名称已存在',
        'url.require'               => '链接URL不能为空',

    ];

    // 应用场景
    protected $scene = [
        
        'edit' =>  [],
    ];
    
}
