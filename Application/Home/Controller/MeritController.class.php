<?php
/**
 * 订单
 * @author sunxin
 *
 */
namespace Home\Controller;

class MeritController extends BaseController{

	//首页
    public function index(){
        $agencyUserModel = new \Home\Model\AgencyUserModel;
        $teamList = $agencyUserModel->getAgencyChild($this->agency_user['agency_uid']); //获取团队数组
        foreach ($teamList as $key => $value) {
        	# code...
        	$tmp[] = $value; //传参给查看名称用
        }

    	//获取代理商名称和编号（用户昵称和手机号）
    	$map['agency_uid'] = array('in', implode($tmp, ','));
    	$agencyInfo = D('users')
    	->field('user_id, alias, mobile_phone as number')
    	->where($map)
    	->select();

    	foreach ($teamList as $key => $value) {
    		# code...
        	$team[$key]['agency_uid'] = $value; //代理商ID
	        $team[$key]['lv'] = $agencyUserModel->getAgencyLV($value); //代理商等级
	        $team[$key]['teamPay'] = $agencyUserModel->getAgencyTeamPay($value); //团队充值金额
	        $team[$key]['user_id'] = $agencyInfo[$key]['user_id']; //团队充值金额
	        $team[$key]['alias'] = $agencyInfo[$key]['alias']; //团队充值金额
	        $team[$key]['number'] = $agencyInfo[$key]['number']; //团队充值金额
    	}

    	$this->assign('team',$team);
    	$this->display();
    }

}