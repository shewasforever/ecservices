<?php
namespace GetProductDetail\Controller;//获取商品详情接口
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
    	//$server_accesstoken = md5($appkey . date('Y-m-d', time()) .  $appsecret_server . $server);
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
				$condition['goods_id'] = array(eq,$client_sku);       
                $good_datail = $Goods->where($condition)->select();
                $Category = M("Category");
				// $categoryid = $Category
    //                         ->field("categoryId")
    //                         ->join('ecs_goods on ecs_category.cat_id = ecs_goods.cat_id')
    //                         ->where($condition)
    //                         ->select();
                $goods_cat = $Goods->field("cat_id")->where($condition)->select();
                $goods_cat_if = $goods_cat[0]['cat_id'];
                if ($goods_cat_if >= 56 && $goods_cat_if <= 60) {
                    $con['cat_id'] =array('eq',55);
                } elseif($goods_cat_if >= 46 && $goods_cat_if <= 48) {
                    $con['cat_id'] =array('eq',45);
                }elseif($goods_cat_if >= 51 && $goods_cat_if <= 53){
                    $con['cat_id'] =array('eq',50);
                }elseif($goods_cat_if >= 398 && $goods_cat_if <= 403){
                    $con['cat_id'] =array('eq',44);
                }else{
                    $con['cat_id'] =array('eq',$goods_cat_if);
                }
                
                
                $categoryid = $Category->field("categoryId")->where($con)->select();
               
                
                //exit($categoryid[0]["categoryid"]);

                $cdt["brand_id"] = array(eq,$good_datail[0]["brand_id"]);
                $Brand = M("Brand");
                $brandname_arr = $Brand->field("brand_name")->where($cdt)->select();                                
                $row["category"] = $categoryid[0]["categoryid"];               
                $row["brand"] = $brandname_arr[0]["brand_name"];              
                $row["sku"] = $client_sku;
                $row["weight"] = $good_datail[0]["goods_weight"];
                $row["state"] = $good_datail[0]["is_on_sale"];
                $row["name"] = $good_datail[0]["goods_name"];
                $row["model"] = " ";
                $row["productArea"] = " ";
                $row["upc"] = " ";
                $row["saleUnit"] = " ";
                $row["introduction"] = $good_datail[0]["goods_desc"];
                $row["param"]["商品名称"] = $good_datail[0]["goods_name"];
                $row["param"]["上架时间"] = date("Y-m-d",$good_datail[0]["add_time"]);
                $row["param"]["商品编号"] = $good_datail[0]["goods_sn"];
                $row["param"]["商品毛重"] = $good_datail[0]["goods_weight"];
                $row["param"]["品牌"] = $brandname_arr[0]["brand_name"];
                $row["param"]["库存"] = $good_datail[0]["goods_number"];
                if (strlen($client_sku) < 4) {
                    $row["image"] ='www.hndhkm.com/' . $good_datail[0]["original_img"];
                } else {
                    $row["image"] = $good_datail[0]["original_img"];
                }                               
                $row["returnMsg"] = "商品详情信息";
                $row["isSuccess"] = true;
                // echo '<pre>';
                // print_r($good_datail);
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