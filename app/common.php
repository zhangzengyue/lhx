<?php

/**
 * 应用公共（函数）文件
 */

use think\Db;
use think\Response;
use think\exception\HttpResponseException;

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    
    $member = session('member_auth');
    
    if (empty($member)) {
        
        return DATA_DISABLE;
    } else {
        
        return session('member_auth_sign') == data_auth_sign($member) ? $member['member_id'] : DATA_DISABLE;
    }
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string 
 */
function data_md5($str, $key = 'OneBase')
{
    
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 使用上面的函数与系统加密KEY完成字符串加密
 * @param  string $str 要加密的字符串
 * @return string 
 */
function data_md5_key($str, $key = '')
{
    
    return empty($key) ? data_md5($str, SYS_ENCRYPT_KEY) : data_md5($str, $key);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    
    // 数据类型检测
    if (!is_array($data)) : $data = (array)$data; endif;
    
    // 排序
    ksort($data);
    
    // url编码并生成query字符串
    $code = http_build_query($data);
    
    // 生成签名
    $sign = sha1($code);
    
    return $sign;
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($member_id = null)
{
    
    $return_id = is_null($member_id) ? is_login() : $member_id;
    
    return $return_id && (intval($return_id) === SYS_ADMINISTRATOR_ID);
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0)
{
    
    // 创建Tree
    $tree = [];
    
    if (!is_array($list)): return false; endif;
    
    // 创建基于主键的数组引用
    $refer = [];

    foreach ($list as $key => $data) {

        $refer[$data[$pk]] =& $list[$key];
    }

    foreach ($list as $key => $data) {

        // 判断是否存在parent
        $parentId =  $data[$pid];

        if ($root == $parentId) {

            $tree[] =& $list[$key];

        } else if(isset($refer[$parentId])){

            is_object($refer[$parentId]) && $refer[$parentId] = $refer[$parentId]->toArray();
            
            $parent =& $refer[$parentId];

            $parent[$child][] =& $list[$key];
        }
    }
    
    return $tree;
}

/**
 * 分析数组及枚举类型配置值 格式 a:名称1,b:名称2
 * @return array
 */
function parse_config_attr($string)
{
    
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    
    if (strpos($string, ':')) {
        
        $value = [];
        
        foreach ($array as $val) {
            
            list($k, $v) = explode(':', $val);
            
            $value[$k] = $v;
        }
        
    } else {
        
        $value = $array;
    }
    
    return $value;
}

/**
 * 解析数组配置
 */
function parse_config_array($name = '')
{
    
    return parse_config_attr(config($name));
}

/**
 * 获取单例对象
 */
function get_sington_object($object_name = '', $class = null)
{

    $request = request();
    
    $request->__isset($object_name) ?: $request->bind($object_name, new $class());
    
    return $request->__get($object_name);
}

/**
 * 将二维数组数组按某个键提取出来组成新的索引数组
 */
function array_extract($array = [], $key = 'id')
{
    
    $count = count($array);
    
    $new_arr = [];
     
    for($i = 0; $i < $count; $i++) {
        
        if (!empty($array) && !empty($array[$i][$key])) {
            
            $new_arr[] = $array[$i][$key];
        }
    }
    
    return $new_arr;
}

/**
 * 根据某个字段获取关联数组
 */
function array_extract_map($array = [], $key = 'id'){
    
    
    $count = count($array);
    
    $new_arr = [];
     
    for($i = 0; $i < $count; $i++) {
        
        $new_arr[$array[$i][$key]] = $array[$i];
    }
    
    return $new_arr;
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 */
function str2arr($str, $glue = ',')
{
    
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 */
function arr2str($arr, $glue = ',')
{
    
    return implode($glue, $arr);
}

/**
 * 获取目录下所有文件
 */
function file_list($path = '')
{
    
    $file = scandir($path);
    
    foreach ($file as $k => $v) {
        
        if (is_dir($path . SYS_DS_PROS . $v)) : unset($file[$k]); endif;
    }
    
    return array_values($file);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name = '')
{
    
    $lower_name = strtolower($name);
    
    $class = SYS_ADDON_DIR_NAME. SYS_DS_CONS . $lower_name . SYS_DS_CONS . $name;
    
    return $class;
}

/**
 * 钩子
 */
function hook($tag = '', $params = [])
{
    
    \think\Hook::listen($tag, $params);
}

/**
 * 保存文件
 */
function sf($arr = [], $fpath = 'D:\test.php')
{
    
    $data = "<?php\nreturn ".var_export($arr, true).";\n?>";
    
    file_put_contents($fpath, $data);
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 */
function addons_url($url, $param = array())
{

    $parse_url  =  parse_url($url);
    $addons     =  $parse_url['scheme'];
    $controller =  $parse_url['host'];
    $action     =  $parse_url['path'];

    /* 基础参数 */
    $params_array = array(
        'addon_name'      => $addons,
        'controller_name' => $controller,
        'action_name'     => substr($action, 1),
    );

    $params = array_merge($params_array, $param); //添加额外参数
    
    return url('addon/execute', $params);
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type=0)
{
    
    if ($type) {
        
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
    } else {
        
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

/**
 * 获取目录列表
 */
function get_dir($dir_name)
{
    
    $dir_array = [];
    
    if (false != ($handle = opendir($dir_name))) {
        
        $i = 0;
        
        while (false !== ($file = readdir($handle))) {
            
            if ($file != "." && $file != ".."&&!strpos($file,".")) {
                
                $dir_array[$i] = $file;
                
                $i++;
            }
        }
        
        closedir($handle);
    }
    
    return $dir_array;
}

/**
 * 获取图片url
 */
function get_picture_url($id = 0)
{
    
    $info = model('Picture')->where(['id' => $id])->field('path,url')->find();

    if (!empty($info['url']))  : return config('static_domain') . SYS_DS_PROS . $info['url'];  endif;

    if (!empty($info['path'])) : return '/upload/picture/'.$info['path'];  endif;

    return '/static/admin/img/onimg.png';
}

/**
 * 获取文件url
 */
function get_file_url($id = 0)
{
    
    $info = model('File')->where(['id' => $id])->field('path,url')->find();

    if (!empty($info['url']))  : return config('static_domain') . SYS_DS_PROS . $info['url'];  endif;

    if (!empty($info['path'])) : return '/upload/file/'.$info['path'];  endif;

    return '暂无文件';
}

/**
 * 页面数组提交后格式转换 
 */
function transform_array($array)
{

    $new_array = array();
    $key_array = array();

    foreach ($array as $key=>$val) {

        $key_array[] = $key;
    }

    $key_count = count($key_array);

    foreach ($array[$key_array[0]] as $i => $val) {
        
        $temp_array = array();

        for( $j=0;$j<$key_count;$j++ ){

            $key = $key_array[$j];
            $temp_array[$key] = $array[$key][$i];
        }

        $new_array[] = $temp_array;
    }

    return $new_array;
}

/**
 * 页面数组转换后的数组转json
 */
function transform_array_to_json($array)
{
    
    return json_encode(transform_array($array));
}

/**
 * 关联数组转索引数组
 */
function relevance_arr_to_index_arr($array)
{
    
    $new_array = [];
    
    foreach ($array as $v)
    {
        
        $temp_array = [];
        
        foreach ($v as $vv)
        {
            $temp_array[] = $vv;
        }
        
        $new_array[] = $temp_array;
    }
    
    return $new_array;
}

/**
 * 获取页面范围
 */
function get_page_number_scope($current_page = 0, $last_page = 0, $offset = 3, $page_number = 7)
{
    
    $init = DATA_SUCCESS;

    $max = $last_page;

    if($last_page > $page_number)
    {
        if($current_page <= $offset){

            $max    = $page_number;
        }else if($current_page + $offset >= $last_page){

            $init   = $last_page - $page_number + $init;
            $max    = $last_page;
        } else {

            $init   = $current_page - $offset;
            $max    = $current_page + $offset;
        }
    }
    
    return ['init' => $init, 'max' => $max];
}

/**
 * 获取分页HTML
 */
function get_page_html($current_page = 0, $last_page = 0, $offset = 3, $page_number = 7)
{
    
    $data = get_page_number_scope($current_page, $last_page, $offset, $page_number);

    $spans  = '';

    for($i = $data['init']; $i <= $data['max']; $i++) :  $spans .= $i == $current_page ? "<span class='current'>".$i."</span>" : "<a href='?page=$i'><span>$i</span></a>"; endfor;

    $next   = '';
    $current_page_next = $current_page + DATA_SUCCESS;

    if ($current_page < $last_page) : $next =  "<a class='next' href='?page=$current_page_next'>下一页</a>"; endif;

    $prev = '';
    $current_page_prev = $current_page - DATA_SUCCESS;
    
    if ($current_page > DATA_SUCCESS) : $prev =  "<a class='prev' href='?page=$current_page_prev'>上一页</a>"; endif;

    $tmpl = "<div class='onebase pagination pagination-right pagination-large'>
                <div>
                    $prev
                    $spans
                    $next
                </div>
            </div>";
    
    return $tmpl;
}

// 获取访问token
function get_access_token()
{

    return md5('OneBase' . date("Ymd") . API_KEY);
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    
    for ($i = 0; $size >= 1024 && $i < 5; $i++) : $size /= 1024; endfor;
    
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 */
function format_time($time = null, $format='Y-m-d H:i:s')
{
    
    if (null === $time) : $time = TIME_NOW; endif;
    
    return date($format, intval($time));
}

/**
 * 删除所有空目录 
 * @param String $path 目录路径 
 */
function rm_empty_dir($path)
{
    
    if (!(is_dir($path) && ($handle = opendir($path))!==false)) : return false; endif;
      
    while(($file = readdir($handle))!==false)
    {

        if(!($file != '.' && $file != '..')) : continue; endif;
        
        $curfile = $path . SYS_DS_PROS . $file;// 当前目录

        if(!is_dir($curfile)) : continue; endif;

        rm_empty_dir($curfile);

        if(count(scandir($curfile)) == 2) : rmdir($curfile); endif;
    }

    closedir($handle); 
}

/**
 * 通过类创建逻辑闭包
 */
function create_closure($class = null, $method_name = '', $parameter = [])
{
    
    $func = function() use($class, $method_name, $parameter)
            {
        
                $object = get_sington_object($class, $class);
        
                return call_user_func_array([$object, $method_name], $parameter);
            };
            
    return $func;
}

/**
 * 通过闭包列表控制事务
 */
function closure_list_exe($list = [])
{
    
    Db::startTrans();
    
    try {
        
        foreach ($list as $closure) : $closure(); endforeach;
        
        Db::commit();
        
        return true;
    } catch (\Exception $e) {
        
        Db::rollback();
        
        throw $e;
    }
}

/**
 * 写入执行信息记录
 */
function write_exe_log($begin = 'app_begin', $end = 'app_end', $type = 0)
{
    
    if(empty(config('is_write_exe_log'))) : return false; endif;
    
    $source_url = empty($_SERVER["HTTP_REFERER"]) ? '未知来源' : $_SERVER["HTTP_REFERER"];
    
    $exe_log['ip']              = request()->ip();
    $exe_log['exe_url']         = request()->url();
    $exe_log['exe_time']        = debug($begin, $end);
    $exe_log['exe_memory']      = debug($begin, $end, 'm');
    $exe_log['exe_os']          = get_os();
    $exe_log['source_url']      = $source_url;
    $exe_log['session_id']      = session_id();
    $exe_log['browser']         = browser_info();
    $exe_log['status']          = DATA_NORMAL;
    $exe_log['create_time']     = TIME_NOW;
    $exe_log['update_time']     = TIME_NOW;
    $exe_log['type']            = $type;
    $exe_log['login_id']        = is_login();
    
    $exe_log_path = "../runtime/log/exe_log.php";
    
    file_exists($exe_log_path) && $now_contents = file_get_contents($exe_log_path);
    
    $arr = var_export($exe_log, true);
    
    empty($now_contents) ? $contents = "<?php\nreturn array (".$arr.");\n" : $contents = str_replace(');', ','. $arr . ');', $now_contents);
    
    file_put_contents($exe_log_path, $contents);
}

/**
 * 获得操作系统
 */
function get_os()
{
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $os = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/win/i', $os)) {
            $os = 'Windows';
        } else if (preg_match('/mac/i', $os)) {
            $os = 'MAC';
        } else if (preg_match('/linux/i', $os)) {
            $os = 'Linux';
        } else if (preg_match('/unix/i', $os)) {
            $os = 'Unix';
        } else if (preg_match('/bsd/i', $os)) {
            $os = 'BSD';
        } else {
            $os = 'Other';
        }
        return $os;
    } else {
        return 'unknow';
    }
}

/**
 * 获取指定日期段内每一天的日期
 * @param Date $startdate 开始日期
 * @param Date $enddate  结束日期
 * @return Array
 */
function get_date_from_range($startdate, $enddate)
{
    
  $stimestamp = strtotime($startdate);
  $etimestamp = strtotime($enddate);
  
  // 计算日期段内有多少天
  $days = ($etimestamp-$stimestamp)/86400+1;
  
  // 保存每天日期
  $date = [];
  
  for($i=0; $i<$days; $i++) : $date[] = date('Y-m-d', $stimestamp+(86400*$i)); endfor;
  
  return $date;
}

/**
 * 获得浏览器
 */
function browser_info()
{
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $br = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE/i', $br)) {
            $br = 'MSIE';
        } else if (preg_match('/Firefox/i', $br)) {
            $br = 'Firefox';
        } else if (preg_match('/Chrome/i', $br)) {
            $br = 'Chrome';
        } else if (preg_match('/Safari/i', $br)) {
            $br = 'Safari';
        } else if (preg_match('/Opera/i', $br)) {
            $br = 'Opera';
        } else {
            $br = 'Other';
        }
        return $br;
    } else {
        return 'unknow';
    }
}

/**
 * 抛出响应异常
 */
function throw_response_exception($data = [], $type = 'json')
{
    
    $response = Response::create($data, $type);

    throw new HttpResponseException($response);
}

/**
 * 字符串前缀验证
 */
function str_prefix($str, $prefix)
{
    
    return strpos($str, $prefix) === DATA_DISABLE ? true : false;
}