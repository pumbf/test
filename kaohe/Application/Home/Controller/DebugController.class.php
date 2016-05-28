<?php

namespace Home\Controller;

use Think\Controller;

class DebugController //extends Controller
{
	//匹配信息
	private $pattern = array(
		'/<td[^>]*?>&nbsp;<\\/td>/',
		'/<td[^>]*?>&nbsp;(?<time>(?<lesson>(?<classid>\\d*)<br>(?<classname>.*?)<br>(?<teacher>.*?)<br>(?<classroom>.*?)<br><font[^>]*?>(?<method>.*?)<\\/font><br>(?<weeks>.*?)<br>选课状态:(?<status>.*?)<font[^>]*?>(?<special>.*?)<\\/font><br>)*?)<\\/td>/',
		'/(?<classid>\\d*)<br>(?<classname>.*?)<br>(?<teacher>.*?)<br>(?<classroom>.*?)<br><font[^>]*?>(?<method>.*?)<\\/font><br>(?<weeks>.*?)<br>选课状态:(?<status>.*?)<font[^>]*?>(?<special>.*?)<\\/font><br>/'

	);
	//过滤
	private $annotation = array(
		'/\\t/',
		'/\\n/',
		'/\\r/',
		'/<!--(?s).*?-->/'
	);
	private $id;

	/**
	 * 根据开始学号和结尾学号进行查询
	 * @param int $startid 第一个需要查询的学号
	 * @param int $num     [description]
	 */
	public function __construct($startid, $num)
	{
		for ($i = 0; $i < $num; $i++) {
			$id = $startid + $i;
			$this->runIt($id);
		}
	}

	private function runIt($id)
	{
		$targetUrl = 'http://jwzx.cqupt.edu.cn/pubStuKebiao.php?xh=' . $id;
		$this->id = $id;
		$text = $this->runCurl($targetUrl);
		if($text) 
			$this->analyze($text); 
	}

	private function runCurl($url)
	{
		$curl = curl_init();
		//参数
		$opt = array(
			CURLOPT_RETURNTRANSFER  => TRUE,	//不显示在屏幕上
			CURLOPT_URL             => $url,    //目标地址
			CURLOPT_HEADER          => false, 	//是否显示请求头
			CURLOPT_NOBODY          => false,	//显示<body>标签
		);
		curl_setopt_array($curl, $opt);
		$text = curl_exec($curl);
		if(!$text){
			return NULL;
		}
		curl_close($curl);
		
		return $text;
	}

	private function analyze($text)
	{
		$model = M('Lesson');
		$text = preg_replace($this->annotation, '', $text);
		$text = iconv('GBK', 'UTF-8', $text);
		//file_put_contents('../../../../test/test.html', $text);
		$message= array();
		$text = preg_replace($this->pattern['0'], '<td height=58 width=11% align=center>&nbsp;0<br>null<br>null<br>null<br><font color=#ff0000>null</font><br>null<br>选课状态:null<br><font color=336699></font><br></td>', $text);
		
		preg_match_all($this->pattern['1'], $text, $subject);
		file_put_contents('../../../../test/test.html', $subject[0][14]);
		$lesson = array();
		$time_lesson = array();
		$n = 0;
		//var_dump($subject[0][11]);
		foreach ($subject[0] as $key => $value) {
			$week = ($key+1)%7;
			$time = ($key+1)/7;
			$num = preg_match_all($this->pattern['1'], $value, $message);
			if ($message['classid']['0'] != '0') {

				for($i=0; $i<$num; $i++, $n++) {
					$lesson['classid'] = $message['classid'][$i];
					$lesson['teacher'] = $message['teacher'][$i];
					$lesson['classname'] = $message['classname'][$i];
					$lesson['classroom'] = $message['classroom'][$i];
					$lesson['method'] = $message['method'][$i];
					$lesson['weeks'] = $message['weeks'][$i];
					$lesson['week'] = $message['week'][$i];
					$lesson['status'] = $message['status'][$i];
					$lesson['time']  = $time;
					$lesson['week']	= $week;
					$lesson['special']	= $message['special'][$i];
					$lesson['studentid']  = $this->id;
					//var_dump($lesson);
					$model->create($lesson);
					$model->add();
				}
				
			}
			
		}

	}
}

