<?php require_once("../include/db_info.inc.php");
if (!(isset($_SESSION['http_judge']))){
	echo "0";
	exit(1);
}
if(isset($_POST['update_solution'])){
	//require_once("../include/check_post_key.php");
	$sid=intval($_POST['sid']);
	$result=intval($_POST['result']);
	$time=intval($_POST['time']);
	$memory=intval($_POST['memory']);
	$sim=intval($_POST['sim']);
	$simid=intval($_POST['simid']);
	$pass_rate=floatval($_POST['pass_rate']);
	$sql="UPDATE solution SET result=$result,time=$time,memory=$memory,judgetime=NOW(),pass_rate=$pass_rate WHERE solution_id=$sid LIMIT 1";
	echo $sql;
	mysql_query($sql);
	
    if ($sim) {
		$sql="insert into sim(s_id,sim_s_id,sim) values($sid,$simid,$sim) on duplicate key update  sim_s_id=$simid,sim=$sim";
		mysql_query($sql);
	}
	
}else if(isset($_POST['checkout'])){
	
	$sid=intval($_POST['sid']);
	$result=intval($_POST['result']);
	$sql="UPDATE solution SET result=$result,time=0,memory=0,judgetime=NOW() WHERE solution_id=$sid and (result<2 or (result<4 and NOW()-judgetime>60)) LIMIT 1";
	mysql_query($sql);
	if(mysql_affected_rows()>0)
		echo "1";
	else
		echo "0";
}else if(isset($_POST['getpending'])){
	$max_running=intval($_POST['max_running']);
	$sql="SELECT solution_id FROM solution WHERE result<2 or (result<4 and NOW()-judgetime>60) ORDER BY result ASC,solution_id ASC limit $max_running";
	$result=mysql_query($sql);
	while ($row=mysql_fetch_object($result)){
		echo $row->solution_id."\n";
	}
	mysql_free_result($result);
	
}else if(isset($_POST['getsolutioninfo'])){
	
	$sid=intval($_POST['sid']);
	$sql="select problem_id, user_id, language from solution WHERE solution_id=$sid ";
	$result=mysql_query($sql);
	if ($row=mysql_fetch_object($result)){
		echo $row->problem_id."\n";
		echo $row->user_id."\n";
		echo $row->language."\n";
		
	}
	mysql_free_result($result);
	
}else if(isset($_POST['getsolution'])){
	
	$sid=intval($_POST['sid']);
	$sql="SELECT source FROM source_code WHERE solution_id=$sid ";
	$result=mysql_query($sql);
	if ($row=mysql_fetch_object($result)){
		echo $row->source."\n";
	}
	mysql_free_result($result);
	
}else if(isset($_POST['getprobleminfo'])){
	
	$pid=intval($_POST['pid']);
	$sql="SELECT time_limit,memory_limit,spj FROM problem where problem_id=$pid ";
	$result=mysql_query($sql);
	if ($row=mysql_fetch_object($result)){
		echo $row->time_limit."\n";
		echo $row->memory_limit."\n";
		echo $row->spj."\n";
		
	}
	mysql_free_result($result);
	
}else if(isset($_POST['addceinfo'])){
	
	$sid=intval($_POST['sid']);
	$sql="DELETE FROM compileinfo WHERE solution_id=$sid ";
	mysql_query($sql);
	$ceinfo=mysql_real_escape_string($_POST['ceinfo']);
	$sql="INSERT INTO compileinfo VALUES($sid,'$ceinfo')";
	mysql_query($sql);
	
}else if(isset($_POST['addreinfo'])){
	
	$sid=intval($_POST['sid']);
	$sql="DELETE FROM runtimeinfo WHERE solution_id=$sid ";
	mysql_query($sql);
	$reinfo=mysql_real_escape_string($_POST['reinfo']);
	$sql="INSERT INTO runtimeinfo VALUES($sid,'$reinfo')";
	mysql_query($sql);
	
}else if(isset($_POST['updateuser'])){
	
	$user_id=mysql_real_escape_string($_POST['user_id']);
	$sql="UPDATE `users` SET `solved`=(SELECT count(DISTINCT `problem_id`) FROM `solution` WHERE `user_id`=\'$user_id\' AND `result`=\'4\') WHERE `user_id`=\'$user_id\'";
	mysql_query($sql);
	
	$sql="UPDATE `users` SET `submit`=(SELECT count(*) FROM `solution` WHERE `user_id`=\'$user_id\') WHERE `user_id`=\'$user_id\'";
	mysql_query($sql);
	
}else if(isset($_POST['updateproblem'])){
	
	$pid=intval($_POST['pid']);
	$sql="UPDATE `problem` SET `accepted`=(SELECT count(1) FROM `solution` WHERE `problem_id`=$pid AND `result`=4) WHERE `problem_id`=$pid";
	//echo $sql;
	mysql_query($sql);
	
	$sql="UPDATE `problem` SET `submit`=(SELECT count(1) FROM `solution` WHERE `problem_id`=$pid) WHERE `problem_id`=$pid";
	//echo $sql;
	mysql_query($sql);
	
	
}else if(isset($_POST['checklogin'])){
	echo "1";
}else if(isset($_POST['gettestdatalist'])){


	$pid=intval($_POST['pid']);
      
  	if($OJ_SAE){
          //echo $OJ_DATA."$pid";
         
           $store = new SaeStorage();
           $ret = $store->getList("data", "$pid" );
            foreach($ret as $file) {
              if(!strstr($file,"sae-dir-tag")){
                     $file=pathinfo($file);
                     $file=$file['basename'];
                    		 echo $file."\n";   
              }
                    
            }


        } else{
        
            $dir=opendir($OJ_DATA."$pid");
            while (($file = readdir($dir)) != "")
            {
              if(!is_dir($file)){
               	    $file=pathinfo($file);
                    $file=$file['basename'];
                    echo "$file\n";
              }
            }
            closedir($dir);
        }
        
	
}else if(isset($_POST['gettestdata'])){
	$file=$_POST['filename'];
        if($OJ_SAE){ 
		$store = new SaeStorage();
                if($store->fileExists("data",$file)){
                       
                		echo $store->read("data",$file);
                }
                
        }else{
          	echo file_get_contents($OJ_DATA.'/'.$file);
        }
           
}else{
?>

<form action='problem_judge.php' method=post>
	<b>HTTP Judge:</b><br />
	sid:<input type=text size=10 name="sid" value=1244><br />
	pid:<input type=text size=10 name="pid" value=1000><br />
	result:<input type=text size=10 name="result" value=4><br />
	time:<input type=text size=10 name="time" value=500><br />
	memory:<input type=text size=10 name="memory" value=1024><br />
	sim:<input type=text size=10 name="sim" value=100><br />
	simid:<input type=text size=10 name="simid" value=0><br />
  	gettestdata:<input type=text size=10 name="filename" value="1000/test.in"><br />
	
        <input type='hidden' name='gettestdatalist' value='do'>
	<input type=submit value='Judge'>
</form>
<?php 
}
?>