<?php


class TaskClass{
	
	protected $app;
	protected $ch;
	protected $configArr;
	
	function __construct($app, $configArr){
		$this->app = $app;
		$this->ch = $app->ch;
		$this->configArr = $configArr;
		
	}
	
	function run(){
		while(1){
	
			echo date('Y-m-d H:i:s')."\n";
			

			$res = $this->getList(); 
			if(is_object($res)){
				print_r($res);
			} else {
				echo $res."\n";
			}
		
/* 			$taskList = array_slice($res->list, 0, 3);	
			$task = $this->selectOrder($taskList);	
			if($task != null){ //如果接到了任务，打印任务信息，不再继续循环
				$r = $this->grabTask($task->id, $task->not_match);
				if($r->code == '000'){
					$this->prompt('淘宝浏览单',$task);
					break;
				}		
			} */
			if($res == '操作太频繁啦!! 请休息一下再试.'){
				sleep(mt_rand(3,6)*60);
			}else {
				sleep(mt_rand(5,10));
			}
			
	
			
		}
	}
	
	
	function getList(){
		$url = 'http://www.lefenshou.com/user/Task/taskListData.html';
		$res = $this->get($url);
		$arr = $this->decode($res);
		return empty($arr)?$res:$arr;
	}

	//选出订单
	function selectOrder($taskList, $momeylimit=0){
		$shop_name = array('精品刺绣馆','远航汽车导航直销店','全国企业彩铃定制中心','涵生珠宝','倍乐熊旗舰店','一诺能量水晶','情简时尚女装','美之缘家居护理体验馆');
		$task = null;
		foreach($taskList as $v){
			//not_match意思是单子还没被抢完，然后在选出金额最大的那单
			if($v->not_match == 0 && (!in_array($v->name, $shop_name)) && ((float)$v->money >= $momeylimit) && ($task == null || (float)$v->money > (float)$task->money)){
				$task = $v;  
			}
		}
		return $task;
	}


	function grabTask($id, $not_match){
		$url = 'http://api-wx.firstblog.cn/case/getcase';
		$fields = array(
			'type' => 'tasks',
			'id' => $id,
			'not_match' => $not_match,
			'consumer_id' => $this->configArr['user_id']
		);
		
		return $this->decode($this->post($url, $fields));
	}

	function prompt($name,$task){
		echo $this->configArr['user_name'].'接到任务'.$name.' 佣金：'.$task->money.'   任务id：'.$task->id.'  店铺名：'.$task->name;
		exec($this->app->env['prompt']['type'][$this->app->env['prompt']['type_id']]);
	}

	function post($url, $fields=array()){
		
		$cookie=dirname(__FILE__)."/cookie.txt";
		curl_setopt ($this->ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt ($this->ch, CURLOPT_COOKIEFILE, $cookie);
		
		curl_setopt($this->ch,CURLOPT_URL, $url);

		//$header = array('token:'.$this->configArr['token']);
		$header = array();

		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($this->ch,CURLOPT_POST,true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);

		curl_setopt($this->ch,CURLOPT_HTTPHEADER,$header);
		curl_setopt($this->ch,CURLOPT_COOKIE,$this->configArr['cookie']);
		curl_setopt($this->ch, CURLOPT_REFERER, $url); 
		
		return curl_exec($this->ch);
	}
	
	function get($url){		
		curl_setopt($this->ch,CURLOPT_URL, $url);

		$header = array('User-Agent: Mozilla/5.0 (Linux; Android 9; Redmi K20 Pro Build/PKQ1.181121.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136');

		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);

		curl_setopt($this->ch,CURLOPT_HTTPHEADER,$header);
		curl_setopt($this->ch,CURLOPT_COOKIE,$this->configArr['cookie'].time());
		curl_setopt($this->ch, CURLOPT_REFERER, $url); 	
		return curl_exec($this->ch);
	}
	 
	function decode($content){
		return json_decode($content);
	}
	
	
}
