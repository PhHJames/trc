<?php
use Pheanstalk\Pheanstalk;
require  'vendor/autoload.php' ;
$pheanstalk = new Pheanstalk('120.25.144.215');
$d = $pheanstalk->listTubeUsed();
for($k = 0 ; $k<13 ; $k++){
$job = $pheanstalk
	  ->watch('testtube')
	  ->ignore('default')
	  ->reserve(5);
	  echo "<pre>";
	print_r($job);	
}



 ;exit; 
while(true ){
	$job = $pheanstalk
	  ->watch('testtube')
	  ->ignore('default')
	  ->reserve();
	$data =  $job->getData();
	print_r($data);
	if($data){
		//write_log($data);
	}	
	$pheanstalk->delete($job);

	//sleep(1);	
}

function write_log($message){
	$file = fopen("../log.txt","a+");
	fwrite($file,$message);
	fclose($file);
}


//echo $job->getData();

//$pheanstalk->delete($job);

// ----------------------------------------
// check server availability

//echo $pheanstalk->getConnection()->isServiceListening(); // true or false