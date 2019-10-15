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
			if(is_object($res) && !empty($res->task_list)){
				
				$task = $this->selectOrder($res->task_list);print_r($res);
				$res = grabTask($task);
				break;
			} else {
				print_r($res);
				echo "\n";
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
		$max = 0;
		foreach($taskList as $v){
			if($v->commission > $max){
				$task = $v;
			}
		}
		return $task;
	}


	function grabTask($task){
		$url = 'http://www.lefenshou.com/User/Task/grabTask.html';
		$fields = array(
			'taskId' => $task->task_id,
			'taskType' => $task->task_type,
			'encrypt' => $task->encrypt,
			'latitude' => '22.798689',
			'altitude' => '108.422663'
		);
		$res = $this->post($url, $fields);echo $res;
		return $this->decode($res);
	}

	function prompt($name,$task){
		echo $this->configArr['user_name'].'接到任务'.$name.' 佣金：'.$task->money.'   任务id：'.$task->id.'  店铺名：'.$task->name;
		exec($this->app->env['prompt']['type'][$this->app->env['prompt']['type_id']]);
	}

	function post($url, $fields=array()){

		curl_setopt($this->ch,CURLOPT_URL, $url);

		$header = array('User-Agent: Mozilla/5.0 (Linux; Android 9; Redmi K20 Pro Build/PKQ1.181121.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136');

		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($this->ch,CURLOPT_POST,true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);

		curl_setopt($this->ch,CURLOPT_HTTPHEADER,$header);
		curl_setopt($this->ch, CURLOPT_REFERER, 'http://www.lefenshou.com/user/Task/taskList.html'); 
		
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
		curl_setopt($this->ch, CURLOPT_REFERER, 'http://www.lefenshou.com/user/Task/taskList.html'); 	
		return curl_exec($this->ch);
	}
	 
	function decode($content){
		return json_decode($content);
	}
	
	
}
