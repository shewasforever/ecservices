<?php
namespace GetAccessToken\Controller;//获取accesstoken接口
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	//header("Content-type:text/html;charset=utf-8");//防止中文乱码
        $appkey_server = "hndhkm2017";
    	$appsecret_server = "23e0635d472e822e6d6a50cade0f4e1a";//appsecret
        $client_decode = json_decode(stripcslashes(htmlspecialchars_decode(I('post.data'))),true);//接受数据并转数组
        // $client_date = I('post.');
        // print_r($client_date);
       
        // exit;
        $client_appkey = $client_decode["appKey"];
        $client_appsecret = $client_decode["appSecret"];
        // $client_appkey = I('post.appKey');
        // $client_appsecret = I('post.appSecret');       
        //验证appkey
    	if($client_appkey){
    		if ($appkey_server !== $client_appkey) {
    			$row["returnMsg"] = "This appkey don't have permission";
				$row["accessToken"] = "null";
				$row["isSuccess"] = 0;
               // $row["post"] = $client_appkey;
				$row_json = json_encode($row);
    			exit($row_json);
    		}  		
    	}else{
    		$row["returnMsg"] = "appkey can't be empty";
			$row["accessToken"] = "null";
			$row["isSuccess"] = 0;
           // $row["post"] = I('post');
			$row_json = json_encode($row);
    		exit($row_json);
    	}
    	//验证appsecret
    	if($client_appsecret){
    		if ($appsecret_server !== $client_appsecret) {
    			$row["returnMsg"] = "The appsecret is error";
				$row["accessToken"] = "null";
				$row["isSuccess"] = 0;
				$row_json = json_encode($row);
    			exit($row_json);
    		}    		
    	}else{
    		$row["returnMsg"] = "appsecret can't be empty";
			$row["accessToken"] = "null";
			$row["isSuccess"] = 0;
			
			$row_json = json_encode($row);
    		exit($row_json);
    	}
    	//生成accessToken
    	$server = "engineer-spf-2017-11-02";
    	//$api_token = md5($appkey . date('Y-m-d', time()) .  $appsecret_server . $server); 
		$api_token = md5($appkey  .  $appsecret_server . $server); 
        $row["returnMsg"] = "success";
		$row["accessToken"] = $api_token;
		$row["isSuccess"] = true; 
		$a_json = json_encode($row);
		exit($a_json);
    }

}