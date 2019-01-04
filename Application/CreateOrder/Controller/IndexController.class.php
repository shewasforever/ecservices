<?php
namespace CreateOrder\Controller;//下单接口
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	header("Content-type:text/html;charset=utf-8");//防止中文乱码
    	$client_decode = json_decode(stripcslashes(htmlspecialchars_decode(I('post.data'))),true);//接受数据并转数组
        $client_appkey = $client_decode["appKey"];
        $client_accesstoken = $client_decode["accessToken"];
        $client_sku = $client_decode["sku"];
        $appkey_server = "hndhkm2017";
        //exit($client_decode["tradeNo"]);
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
                $Order_info = M("Order_info");                              
                $date_order["order_sn"] = $client_decode["tradeNO"];
                $date_order["consignee"] = $client_decode["name"];
                $date_order["province"] = $client_decode["provinceId"];
                $date_order["city"] = $client_decode["cityId"];
                $date_order["district"] = $client_decode["countyId"];
                $date_order["xiangcun"] = $client_decode["townId"];
                $date_order["address"] = $client_decode["address"];
                $date_order["tel"] = $client_decode["phone"];
                $date_order["mobile"] = $client_decode["mobile"];
                $date_order["email"] = $client_decode["email"];
                $date_order["goods_amount"] = $client_decode["amount"];
                $date_order["pay_id"] = $client_decode["payment"];                                              
                if($date_order["province"] !== "6"){
                    $row["errorCode"] = "ORDER001_01";
                    $row["returnMsg"] = "城市编码不正确，请重新获取！";
                    $row["isSuccess"] = false;
                    $a_json = json_encode($row);
                    exit($a_json);
                }
                $payment_id = array(1,2,3,4,5,6,7);
                if (!in_array(intval($date_order["pay_id"]), $payment_id)) {
                    $row["errorCode"] = "ORDER002";
                    $row["returnMsg"] = "支付方式不正确，请检查后重新下单！";
                    $row["isSuccess"] = false;
                    $a_json = json_encode($row);
                    exit($a_json);
                }
                $condition_trade["order_sn"] = array('eq',$date_order["order_sn"]);
                $select_tradenumber = $Order_info->where($condition_trade)->field('order_id')->select();
                $already_id = $select_tradenumber[0]['order_id'];
                if ($already_id) {
                        $Goods = M("Goods");
                        foreach ($client_decode["sku"] as $key => $value) {
                            $goods_order[$key]["order_id"] = $already_id;
                            $goods_order[$key]["goods_id"] = $value["skuId"];
                            $con["goods_id"] = array('eq',$value["skuId"]); 
                            $res = $Goods->field('goods_sn,goods_name')->where($con)->select();
                            $goods_order[$key]["goods_sn"] = $res[0]["goods_sn"];
                            $goods_order[$key]["goods_name"] = $res[0]["goods_name"];
                            $goods_order[$key]["goods_number"] = $value["num"];
                            $goods_order[$key]["goods_price"] = $value["price"];

                            $Order_goods = M("Order_goods");
                            if ($info = $Order_goods->add($goods_order[$key])) {
                                $row["orderId"] = $client_decode["tradeNo"];
                                $row["arriveData"] = date('Y-m-d', strtotime("+1 week"));
                                $row["amount"] = $client_decode["amount"];
                                $row["freight"] = "0";
                                foreach ($client_decode["sku"] as $key => $value) {
                                    $row["sku"][$key]["skuId"] = $value["skuId"];
                                    $row["sku"][$key]["num"] = $value["num"];
                                }                              
                                $row["returnMsg"] = "创建订单完成";
                                $row["isSuccess"] = true;
                                $a_json = json_encode($row);
                                exit($a_json);
                            } else {
                                $row["errorCode"] = "order_gooods add error";
                                $row["returnMsg"] = "订单校验接口异常";
                                $row["isSuccess"] = false;
                                
                                $a_json = json_encode($row);
                                exit($a_json);
                            }                           
                        }
                } else {
                    $sql_order_info = @$Order_info->add($date_order);
                    if ($sql_order_info) {
                            $Goods = M("Goods");
                            foreach ($client_decode["sku"] as $key => $value) {
                                $goods_order[$key]["order_id"] = $sql_order_info;
                                $goods_order[$key]["goods_id"] = $value["skuId"];
                                $con["goods_id"] = array('eq',$value["skuId"]); 
                                $res = $Goods->field('goods_sn,goods_name')->where($con)->select();
                                $goods_order[$key]["goods_sn"] = $res[0]["goods_sn"];
                                $goods_order[$key]["goods_name"] = $res[0]["goods_name"];
                                $goods_order[$key]["goods_number"] = $value["num"];
                                $goods_order[$key]["goods_price"] = $value["price"];

                                $Order_goods = M("Order_goods");
                                if ($info = $Order_goods->add($goods_order[$key])) {
                                    $row["orderId"] = $client_decode["tradeNo"];
                                    $row["arriveData"] = date('Y-m-d', strtotime("+1 week"));
                                    $row["amount"] = $client_decode["amount"];
                                    $row["freight"] = "0";
                                    foreach ($client_decode["sku"] as $key => $value) {
                                        $row["sku"][$key]["skuId"] = $value["skuId"];
                                        $row["sku"][$key]["num"] = $value["num"];
                                    }
                                   
                                    $row["returnMsg"] = "创建订单完成";
                                    $row["isSuccess"] = true;                                    
                                    $a_json = json_encode($row);
                                    exit($a_json);
                                } else {
                                    $row["errorCode"] = "ORDER008_01";
                                    $row["returnMsg"] = "订单校验接口异常";
                                    $row["isSuccess"] = false;
                                    
                                    $a_json = json_encode($row);
                                    exit($a_json);
                                }                          
                            }
                    }else {
                        $row["errorCode"] = "ORDER008_01";
                        $row["returnMsg"] = "订单校验接口异常";
                        $row["isSuccess"] = false;
                        $row["sql"] = $sql_order_info;
                        $row["city"] = $date_order["city"];
                        $a_json = json_encode($row);
                        exit($a_json);
                    }
                }                
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