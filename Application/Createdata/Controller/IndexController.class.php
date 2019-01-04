<?php
namespace Createdata\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        header("Content-type:text/html;charset=utf-8");//防止中文乱码
    	$Category = M("Category");  
        $condition['categoryId'] = array('NEQ',"000000");       
        $cate_list = $Category->field("cat_name,categoryId")->where($condition)->select();
        foreach ($cate_list as $key => $value) {
                    foreach ($value as $k => $v) {
                        $arr_name[$key]["name"] = $value["cat_name"];
                        $arr_name[$key]["categoryId"] = $value['categoryid'];
                       
                    }                   
                }
        echo '<pre>';
        //print_r($);
        echo '</pre>';
    }
    public function getcurl(){
        header("Content-type:text/html;charset=utf-8");//防止中文乱码
        $url = "http://127.0.0.1/ecservice/getAccessToken";

        $post_data = array (
          "appKey" => "hndhkm2017",         
          "appSecret" => "23e0635d472e822e6d6a50cade0f4e1a"
        );
        //$post_b["data"] = $post_data; 
        //$post_a = json_encode($post_b);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 设置请求为post类型
        curl_setopt($ch, CURLOPT_POST, 1);
        // 添加post数据到请求中
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        // 执行post请求，获得回复
        $response= curl_exec($ch);
        curl_close($ch);
        // echo '<pre>';
        // print_r($post_b);
        // echo '</pre>';
        echo $response;

    }

}