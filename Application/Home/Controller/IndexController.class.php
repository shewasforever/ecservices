<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	header("Content-type:text/html;charset=utf-8");//防止中文乱码
    	//phpinfo();
    	$goods = M('Goods');
    	// $condition["goods_name"] = array('like',"%激光%打印机%");
    	// $condition["cat_id"] = array('in','208,372');
    	$condition["goods_id"] = "118977";
    	// $date["cat_id"] = '419';
    	$goods_arr = $goods->where($condition)->select();
    	// $goods_sql = $goods->where($condition)->save($date);
    	echo '<pre>';
    	print_r($goods_arr);
    	echo '</pre>';
    	// print_r($goods_sql);

    }
    

}