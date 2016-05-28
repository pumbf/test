<?php
namespace Home\Controller;
use Think\Controller;
use Home\Controller\DebugController;
class IndexController extends Controller {
    public function index($id = 2015210001){
       $M = M('Lesson');
       $data = $M->where('stdentid='.$id)->find();
       $this->assign($data);
       $this->display();

    }
    public function getData(){
    	 $Debug = new DebugController('2015210001',100);
    }
}