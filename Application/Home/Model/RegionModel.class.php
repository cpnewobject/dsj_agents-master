<?php

/**
 *  获取地址信息
 *  @author sunxin
 */

 namespace Home\Model;
 use Think\Model;

 class RegionModel extends Model{
    //获取省市县
    public function getlist($parent_id=1,$isselect=''){
        $list = array();
        $list = M('region')->where(array('parent_id'=>$parent_id))
                ->field('region_id,region_name')->select();
        if(!empty($list))
        {
            foreach($list as $key=>$val)
            {
                if(!empty($isselect) && $val['region_id'] == $isselect )
                {
                    $list[$key]['select'] = '1';
                }
                else
                {
                    $list[$key]['select'] = '0';
                }
            }
        }
        return $list;
    }

    //根据ID 获取地区名称
    public function getAreaName($id){
        return M('region')->where(array('region_id'=>$id))->getField('region_name');
    }

 }





?>