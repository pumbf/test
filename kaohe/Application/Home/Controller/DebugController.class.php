<?php

namespace Home\Controller;

use Think\Controller;

class DebugController //extends Controller
{
	//匹配信息
	private $pattern = array(
		'/<td[^>]*?>.*?<\\/td>/',
		'/(?<classid>\\d*)<br>(?<classname>.*?)<br>(?<teacher>.*?)<br>(?<classroom>.*?)<br><font[^>]*?>(?<method>.*?)<\\/font><br>(?<weeks>.*?)<br>选课状态:(?<status>.*?)<br><font[^>]*?>(?<special>.*?)<\\/font><br>/'

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
			CURLOPT_TIMEOUT			=> 60
		);
		curl_setopt_array($curl, $opt);
		try {
			$text = curl_exec($curl);		
		} catch (Exception $e) {
			return NULL;
		}
		if(!$text){
			return NULL;
		}
		curl_close($curl);
		
		return $text;
	}

	private function analyze($text)
	{
		$model = M('Lesson');
		//去注释
		$text = preg_replace($this->annotation, '', $text);
		//转码
		$text = iconv('GBK', 'UTF-8', $text);
		//把<td>内容单独拿出来
		preg_match_all($this->pattern['0'], $text, $subject);
		$lesson = array();
		$lesson['studentid']  = $this->id;
		foreach ($subject[0] as $key => $value) {
			$week = ($key+1)%7;
			$time = ($key+1)/7;
			//分析数据
			$num  = preg_match_all($this->pattern['1'], $value, $message);
			if ($message['classid']['0']) {
				//添加数据
				for($i=0; $i<$num; $i++, $n++) {
					$model 				 = 	M('Lesson');
					$lesson['classid'] 	 = $message['classid'][$i];
					$lesson['teacher'] 	 = $message['teacher'][$i];
					$lesson['classname'] = $message['classname'][$i];
					$lesson['classroom'] = $message['classroom'][$i];
					$lesson['method']    = $message['method'][$i];
					$lesson['weeks'] 	 = $message['weeks'][$i];
					$lesson['week'] 	 = $message['week'][$i];
					$lesson['status'] 	 = $message['status'][$i];
					$lesson['time']  	 = $time;
					$lesson['week']		 = $week;
					$lesson['special']	 = $message['special'][$i];
					try{
						$model->create($lesson);
						$model->add();
					} catch(Exception $e) {
						continue;
					}
					unset($model);
					sleep(0.1);
				}
				
			}
			
		}

	}
}

