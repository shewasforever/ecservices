<?php
namespace GetProductInventory\Controller;//获取商品库存查询接口
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	header("Content-type:text/html;charset=utf-8");//防止中文乱码
    	$client_decode = json_decode(stripcslashes(htmlspecialchars_decode(I('post.data'))),true);//接受数据并转数组
        $client_appkey = $client_decode["appKey"];
        $client_accesstoken = $client_decode["accessToken"];
        $client_sku = $client_decode["sku"];
        $appkey_server = "hndhkm2017";
        //验证appkey
		if($client_appkey){
    		if ($appkey_server !== $client_appkey) {
    			$row["returnMsg"] = "This appkey don't have permission";
				$row["accessToken"] = "null";
				$row["isSuccess"] = 0;
				$row_json = json_encode($row);
    			exit($row_json);
    		}  		
    	}else{
    		$row["returnMsg"] = "appkey can't be empty";
			$row["accessToken"] = "null";
			$row["isSuccess"] = 0;
            $row["post"] = $client_appkey;
			$row_json = json_encode($row);
    		exit($row_json);
    	}
    	//验证accesstoken
    	$server = "engineer-spf-2017-11-02";
    	$appsecret_server = "23e0635d472e822e6d6a50cade0f4e1a";
        //服务端accesstoken有效期24小时	
    	// $server_accesstoken = md5($appkey . date('Y-m-d', time()) .  $appsecret_server . $server); 
        $server_accesstoken = md5($appkey  .  $appsecret_server . $server); 
    	if($client_accesstoken){
    		if($server_accesstoken !== $client_accesstoken){
    			$row["returnMsg"] = "This accessToken has expired";
				$row["accessToken"] = "null";
				$row["isSuccess"] = 0;
				$row_json = json_encode($row);
    			exit($row_json);
    		}else{
			 	$Goods = M("Goods"); 							
				$condition['goods_id'] = array(EQ,$client_sku);       
                $state = $Goods->field("goods_number")->where($condition)->select();
                $state_code = $state[0]["goods_number"];
                if ($state_code > 0) {
                    $state_code = "00";
                } else {
                   $state_code = "02";
                }
                
                
                $row["skuId"] = $client_sku;
                $row["state"] = $state_code;				
                $row["returnMsg"] = "省份信息";
                $row["isSuccess"] = true; 

                // echo '<pre>';
                // print_r($row);
                // echo '</pre>';
				$a_json = json_encode($row);
				exit($a_json);
    		}
    	}else{
    		$row["returnMsg"] = "accessToken can't be empty";
			$row["accessToken"] = "null";
			$row["isSuccess"] = 0;
            $row["post"] = $client_appkey;
			$row_json = json_encode($row);
    		exit($row_json);
    	}
    }

}