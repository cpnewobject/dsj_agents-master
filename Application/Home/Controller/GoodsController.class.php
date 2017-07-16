<?php
/**
 * 商品
 * @author sunxin
 *
 */
namespace Home\Controller;

class GoodsController extends BaseController{
    public function index(){
        //实例化表明
        $goods = D('goods');
        // 组合查询条件
        //$datas = $_POST;
        //print_r($datas);
        $goods_name = !empty($_POST['goods_name']) ? trim($_POST['goods_name']) : '';
        $goods_sn = !empty($_POST['goods_sn']) ? trim($_POST['goods_sn']) : '';
        $catergory = !empty($_POST['catergory']) ? trim($_POST['catergory']) : '';
        $catergory_name = !empty($_POST['catergory_name']) ? trim($_POST['catergory_name']) : '';
        if(!empty($goods_name)){
            $where['goods_name'] = array("like","%$goods_name%");
        }
        if(!empty($goods_sn)){
            $where['goods_sn'] = array("like","%$goods_sn%");
        }
        if(!empty($catergory)){
            $where['cat_id'] = $catergory;
        }
        //查询所有的分类
        $category = D('category');
        $categorylist = $category->where(array("is_show"=>1))->select();

        $this->assign('categorylist',$categorylist);
        $count = $goods->where('is_on_sale=1')->where($where)->count();
        $Page = new\Think\Page($count,2);
        $show = $Page->show();
        $list = $goods->where('is_on_sale=1')->where($where)->order('goods_id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $this->assign('catergory_name',$catergory_name);
        $this->assign('goods_name',$goods_name);
        $this->assign('goods_sn',$goods_sn);
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display('index');
    }

}