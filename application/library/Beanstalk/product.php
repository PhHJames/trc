<?php
use Pheanstalk\Pheanstalk;
require  'vendor/autoload.php' ;
$pheanstalk = new Pheanstalk('127.0.0.1');
$time = time() ;
for($k = 0 ; $k<300 ; $k++){
	 $pheanstalk
  ->useTube('testtube')
  ->put("job".rand(1111 , 9999 )."\n");
}
$two = time() ;
$m = ($two - $time)/60 ; 
echo "success\n" ;

echo "time is ".($m) ;
