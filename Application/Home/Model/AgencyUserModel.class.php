<?php
/**
 * 代理商
 * @author sunxin
 *
 */
namespace Home\Model;
use Think\Model;

class AgencyUserModel extends Model {

    //获取代理商折扣率
    public function getAgencyDiscount($agency_uid){
		//获取团队（/其与其所有下属）充值总额
    	$agency_user_price = $this->getAgencyTeamPay($agency_uid);

    	//获取代理商折扣配置，并与之比较
    	$tmp = $this->getAgentsConfigDiscount();

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

    //获取代理商等级
    public function getAgencyLV($agency_uid){
		//获取团队（/其与其所有下属）充值总额
    	$agency_user_price = $this->getAgencyTeamPay($agency_uid);

    	//获取代理商折扣配置，并与之比较
    	$tmp = $this->getAgentsConfigDiscount();

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
    		$lv = 1;
    	}else{
    		$lv = $tmp[$result]['grade'];
    	}

    	//返回等级
    	return $lv;
    }

	//其与其所有下属的数组
    public function getAgencyChild($agency_uid){
    	//其与其所有下属的数组
    	$agency_id[] = $agency_uid;

    	//获取当前代理商的子级代理商id
    	$map = array('parent_id' => $agency_uid);
    	$tmp = M('agency_user')->field('id')->where($map)->select();

    	// 如果子级代理商不为空，则循环查询子级代理商的子级代理商
    	while (!empty($tmp)) {
	    	$ids = array_column($tmp, 'id'); //将tmp结果数组转为一维数组
    		# code...
    		$agency_id = array_merge($agency_id,$ids); //将结果合并至其与其所有下属的数组中
	    	$where['parent_id'] = array('in', implode($ids, ','));
	    	$tmp = M('agency_user')->field('id')->where($where)->select();
    	}

    	return $agency_id;
    }

	//获取团队（/其与其所有下属）充值总额
    public function getAgencyTeamPay($agency_uid){
    	//其与其所有下属的数组
    	$agency_id = $this->getAgencyChild($agency_uid);

    	//获取充值总额
    	$map['agency_uid'] = array('in', implode($agency_id, ','));
    	$tmp = M('agency_pay_log')->field('SUM(price) AS price')->where($map)->find();
    	return $tmp['price'];
    }

	//获取代理商折扣配置
    private function getAgentsConfigDiscount(){
    	$map = array('type' => 0);
    	$tmp = M('agents_config')->field('grade,name,config_value')->where($map)->order('grade asc')->select();
    	return $tmp;
    }
}