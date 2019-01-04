<?php
namespace GetProductPool\Controller;//获取商品池接口
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	header("Content-type:text/html;charset=utf-8");//防止中文乱码
    	$client_decode = json_decode(stripcslashes(htmlspecialchars_decode(I('post.data'))),true);//接受数据并转数组
        $client_appkey = $client_decode["appKey"];
        $client_accesstoken = $client_decode["accessToken"];
        $clicent_categoryid = $client_decode["categoryId"];
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
			 	$Category = M("Category"); 			
				$condition['categoryId'] = array(eq,$clicent_categoryid);       
                $cat_index = $Category->field("cat_id")->where($condition)->select();
                
                $cat_id = $cat_index[0]["cat_id"];
                switch ($cat_id) {
                    case '55':
                        $condi['cat_id'] =array('between','56,60');
                        break;
                    case '45':
                        $condi['cat_id'] =array('between','46,48');
                        break;
                    case '50':
                        $condi['cat_id'] =array('between','51,53');
                        break;
                    case '44':
                        $condi['cat_id'] =array('between','398,403');
                        break;
                    default:
                        $condi['cat_id'] = array('eq',$cat_id);
                        break;
                }
                //$condi['cat_id'] = array('eq',$cat_id);

                $Goods = M("Goods");               
                $goodsid_list = $Goods->field("goods_id")->where($condi)->select();
				foreach ($goodsid_list as $key => $value) {
                    $arr[] = $value["goods_id"];
                }
                $row["sku"] = $arr;
                $row["returnMsg"] = "分类商品编码信息";
                $row["isSuccess"] = true;
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