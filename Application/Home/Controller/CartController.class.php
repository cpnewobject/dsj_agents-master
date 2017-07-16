<?php
/**
 * 购物车
 * @author sunxin
 *
 */
namespace Home\Controller;

class CartController extends BaseController{

	//首页
    public function index(){
    	// 查询满足要求的总记录数 $map表示查询条件
	    $count = $this->getCartCount();
	    $Page = new \Think\Page($count,4);// 实例化分页类 传入总记录数
	    $show = $Page->show();// 分页显示输出
	    // 进行分页数据查询
	    $list = $this->getCartList($Page->firstRow, $Page->listRows);
	    $this->assign('list',$list);// 赋值数据集
	    $this->assign('page',$show);// 赋值分页输出

        //收货人信息
        $consignee = M('user_address')
        ->table("__USER_ADDRESS__ AS a")
        ->field('a.user_id, a.consignee, b.region_name as country, c.region_name as province, d.region_name as city, a.address, a.tel, a.mobile')
        ->join('__REGION__ AS b ON a.country = b.region_id')
        ->join('__REGION__ AS c ON a.province = c.region_id')
        ->join('__REGION__ AS d ON a.city = d.region_id')
        ->where(array('user_id' => $this->agency_user['user_id']))
        ->select();

        $this->assign('consignee',$consignee);
	    $this->display(); // 输出模板
    }

    //改变数量
    public function updateConut(){
		$map = array(
            'rec_id' => I('post.rec_id'),
            'user_id' => $this->agency_user['agency_uid']
            );
		self::getCartModel()->goods_number = I('post.count');
		if(self::getCartModel()->where($map)->save()){ // 根据条件更新记录
	    	$this->ajaxReturn(['code' => 1, 'message' => '操作成功']);
		}else{
	    	$this->ajaxReturn(['code' => -1, 'message' => '操作失败']);
		}
    }

    //移出购物车
    public function remove(){
		$map = array(
            'rec_id' => I('param.rec_id'),
            'user_id' => $this->agency_user['user_id'],
            );
		if(self::getCartModel()->where($map)->delete()){ // 删除
	    	$this->ajaxReturn(['code' => 1, 'message' => '操作成功']);
		}else{
	    	$this->ajaxReturn(['code' => -1, 'message' => '此条记录不存在或您无权操作']);
		}
    }

    //加入购物车
    public function addCount(){
        // $map = array(
        //     'goods_id' => I('get.goods_id'),
        //     );
        //调取商品信息
        $goods = M('goods')->find(I('param.goods_id'));
// var_dump($goods);
        if(empty($goods)){
            return $this->ajaxReturn(['code' => -2, 'message' => '商品不存在']);
        }else{
            //判断购物车内是否已经有此货品
            $cart = self::getCartModel()
            ->where(array('user_id' => $this->agency_user['user_id'],
                'goods_id' => I('param.goods_id'),
                ))
            ->find();
            if(empty($cart)){ //没有则新增
                self::getCartModel()->user_id = $this->agency_user['user_id'];
                self::getCartModel()->session_id = session_id();
                self::getCartModel()->goods_id = $goods['goods_id'];
                self::getCartModel()->goods_sn = $goods['goods_sn'];
                self::getCartModel()->product_id = 0;
                self::getCartModel()->goods_name = $goods['goods_name'];
                self::getCartModel()->market_price = $goods['market_price'];
                self::getCartModel()->goods_price = $goods['shop_price'];
                self::getCartModel()->goods_number = I('param.count');
                self::getCartModel()->goods_attr = 0;//!!!
                self::getCartModel()->is_real = $goods['is_real'];
                self::getCartModel()->extension_code = $goods['extension_code'];
                self::getCartModel()->rec_type = 0;//!!!
                self::getCartModel()->parent_id = 0;//!!!
                self::getCartModel()->is_gift = 0;//!!!
                self::getCartModel()->can_handsel = 0;//!!!
                self::getCartModel()->goods_attr_id = '';//!!!

                $result = self::getCartModel()->add();

            }else{ //有则添加个数
                self::getCartModel()->goods_number = self::getCartModel()->goods_number + trim(I('param.count'));
                // self::getCartModel()->create();

                $result = self::getCartModel()->save();
            }


            if($result){ // 根据条件更新记录
                return $this->ajaxReturn(['code' => 1, 'message' => '操作成功']);
            }else{
                return $this->ajaxReturn(['code' => -1, 'message' => '操作失败']);
            }
        }

    }

	//购物车AJAX用
    public function json_list(){
    	$tmp = $this->getCartList(0, $this->getCartCount());
    	$this->ajaxReturn($tmp);
    }

	//获取购物车商品列表个数
    private function getCartCount(){
    	$map = $this->selectMap();
    	$count = self::getCartModel()
    	->table("__CART__ AS a")
		->where($map)
		->count();// 查询满足要求的总记录数 $map表示查询条件

		return $count;
    }

	//获取购物车商品列表
    private function getCartList($firstRow, $listRows){
    	$map = $this->selectMap();
    	$discount = $this->getAgencyDiscount($this->agency_user['agency_uid']);
    	$list = self::getCartModel()
    	->table("__CART__ AS a")
    	->field('a.user_id, a.rec_id, a.goods_id, a.goods_sn, a.goods_number as count, a.goods_name, a.goods_price, b.goods_thumb, b.goods_number, b.goods_weight, b.pv_price,'
    		.$discount.' as discount,'
    		.'a.goods_price*'.$discount.' as discount_price,'
    		.'a.goods_price-goods_price*'.$discount.' as del_price')
    	->join('__GOODS__ AS b ON a.goods_id = b.goods_id')
		->where($map)
		->order('rec_id')
		->limit($firstRow.','.$listRows)
		->select();

		return $list;
    }

	//查询列表的where条件
    private function selectMap(){
    	return ['a.user_id' => $this->agency_user['user_id']];
    }

	//查询列表的where条件
    static private function getCartModel(){
    	return M('cart');
    }
}