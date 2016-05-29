<?php
namespace Home\Controller;
use Think\Controller;
use Home\Controller\DebugController;
class IndexController extends Controller {
    public function index($id = 2015210001){
    	try{
       		$M = M('Lesson');
       		$data = $M->where('stdentid='.$id)->find();
       		$message;
       		foreach ($data  as $value) {
       		$message[$value['week']+7*$value['time']] = $data;
       		}
       		$this->assign($data);
       		$this->display();
       	} catch(Exception $e) {
       		$this->error('数据为空，获取数据中....',U('Index/getData'));
       	}
       	

    }
    public function getData(){
    	var_dump('获取数据中:)....');
    	 $Debug = new DebugController('2015210001',100);
    }
}