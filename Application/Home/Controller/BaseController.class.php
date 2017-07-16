<?php
/**
 * 基类
 * @author sunxin
 *
 */

namespace Home\Controller;
use Think\Controller;

class BaseController extends Controller{

	protected $agency_user;

    public function _initialize() {
//     	$admin_id=$_SESSION['admin_id'];
//     	if($admin_id>0){
//     		return true;
//     	}else{
//     		$this->redirect('/Login/index');
//     	}
    	$this->agency_user = D('Users')->field('user_id,agency_uid')->find(3);//用户获取
    }

    //获取代理商折扣率
    protected function getAgencyDiscount($agency_uid){
    	//其与其所有下属的数组
    	$agency_id[] = $agency_uid;

    	//获取当前代理商的子级代理商id
    	$map = array('parent_id' => $agency_uid);
    	$tmp = D('agency_user')->field('id')->where($map)->select();

    	// 如果子级代理商不为空，则循环查询子级代理商的子级代理商
    	while (!empty($tmp)) {
	    	$ids = array_column($tmp, 'id'); //将tmp结果数组转为一维数组
    		# code...
    		$agency_id = array_merge($agency_id,$ids); //将结果合并至其与其所有下属的数组中
	    	$where['parent_id'] = array('in', implode($ids, ','));
	    	$tmp = D('agency_user')->field('id')->where($where)->select();
    	}

    	//获取充值总额
    	unset($map);
    	$map['agency_uid'] = array('in', implode($agency_id, ','));
    	$tmp = D('agency_pay_log')->field('SUM(price) AS price')->where($map)->find();
    	$agency_user_price = $tmp['price'];

    	//获取代理商折扣配置，并与之比较;
    	$map = array('type' => 0);
    	$tmp = D('agents_config')->field('name,config_value')->where($map)->order('grade asc')->select();

    	//初始化等级
		$result = 0;
    	foreach ($tmp as $key => $value) {
    		# code...
    		if($agency_user_price < $value['name']){
    			//如果小于，则不是此级，则交给循环判断是否是下一级
				$result = $key + 1;
    		}else{
    			//如果不小于，则退出循环
    			break;
    		}
    	}
    	if($result > $key){ //如果连最低标准都不到，则不打折
    		$discount = 1;
    	}else{
    		$discount = $tmp[$result]['config_value'];
    	}

    	//返回折扣
    	return $discount;
    }
    
    /**
     * 获取地址
     * @param int $id
     */
    public function get_area($id){
    	$area_name = D("Region")->getAreaName($id);
    	return $area_name;
    }
}