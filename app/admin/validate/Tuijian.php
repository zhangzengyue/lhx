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
class Tuijian extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'b_id'              => 'require|unique:tuijian',

    ];

    // 验证提示
    protected $message  =   [
        
        'b_id.require'              => '推荐不能为空',
    ];

    // 应用场景
    protected $scene = [
        
        'tuijian' =>  ['b_id'],
    ];
    
}
