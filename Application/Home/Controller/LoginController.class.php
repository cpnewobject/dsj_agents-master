<?php
/**
 * 登录注册
 * @author sunxin
 *
 */
namespace Home\Controller;

class LoginController extends BaseController{

	//首页
    public function index(){
    	$this->display();
    }
    
    public function logout(){
    	session(null);
    	$this->redirect('Login/index');
    }
    
    public function set_pwd(){
    	
    }

}