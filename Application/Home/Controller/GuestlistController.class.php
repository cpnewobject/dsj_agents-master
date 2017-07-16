<?php
/**
 * 订单
 * @author sunxin
 *
 */
namespace Home\Controller;

use Home\Model\RegionMode;

class GuestlistController extends BaseController{

	//首页
    public function index(){
//     	$uid= $_SESSION['uid'];
		$uid = 2;
    	$consignee = I('param.consignee','','trim');
    	$mobile = I('param.mobile');
    	$where = array();
    	if(!empty($consignee)){
    		$where['consignee'] =  array("like","%$consignee%");
    	}
    	if(!empty($mobile)){
    		$where['mobile'] = array("like","%$mobile%");;
    	}
    	$where['user_id'] = $uid;
    	$count = M("user_address")->where($where)->count();
    	$page = new \Think\Page($count,4);
    	$list = M("user_address")->where($where)->order("address_id desc")->limit($page->firstRow.','.$page->listRows)->select();
    	foreach ($list as $k=>$l){
    		$list[$k]['province_name'] = $this->get_area($l['province']);
    		$list[$k]['city_name'] = $this->get_area($l['city']);
    		$list[$k]['district_name'] = $this->get_area($l['district']);
    	}
    	$search_where = array(
    			'consignee'=>$consignee,
    			'mobile'=>$mobile,
    			);
		$this->assign($search_where);
    	$this->assign('list',$list);
	    $this->assign('page',$page->show());
	    $this->display();
    }

    public function guestadd(){
    	$areas = D('Region')->getlist(1);
    	$city = D('Region')->getlist($province_id);
    	$district = D('Region')->getlist($city_id);

    	$this->assign('areas',$areas);
    	$this->assign('city',$city);
    	$this->assign('district',$district);
    	$this->display('guestadd');
    }

	/**
     *  获取城市列表
	 */
	public function getcityList()
	{
		if(IS_AJAX)
		{
			$parent_id = I('param.parent_id',0,'intval');
			$city_list = D('Region')->getlist($parent_id);
			$str="<option value='0'>请选择市</option>";
			if(!empty($city_list))
			{
				foreach ($city_list as $key => $value) {
					$str.='<option value='.$value['id'].'>'.$value['area'].'</option>';
				}
			}
			$res = array('code'=>'0','city'=>$str);
			$this->ajaxReturn($res);
		}
	}

	/**
     *  获取区\县
	 */
	public function getdistrictList()
	{
		if(IS_AJAX)
	    {
	    	$parent_id = I('param.parent_id',0,'intval');
	    	$district_list = D('Region')->getlist($parent_id);
	    	$str="<option value='0'>请选择区</option>";
	    	if(!empty($district_list))
	    	{
	    		foreach ($district_list as $key => $value) {
	    			$str.='<option value='.$value['id'].'>'.$value['area'].'</option>';
	    		}
	    	}

	    	$res = array('code'=>'0','district'=>$str);

	    	$this->ajaxReturn($res);

	    }
	}

}