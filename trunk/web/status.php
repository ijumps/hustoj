<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        @session_start();
        require_once("./include/db_info.inc.php");
  $cache_time=2;
        require_once("./include/cache_start.php");
        
require_once("./include/my_func.inc.php");
require_once("./include/db_info.inc.php");
if(isset($OJ_LANG)){
                require_once("./lang/$OJ_LANG.php");
        }
require_once("./include/const.inc.php");

?>


<meta http-equiv='refresh' content='60'>
<title>Submission Status</title>
<center>

<?php $str2="";

$sql="SELECT * FROM `solution` WHERE 1 ";
if (isset($_GET['cid'])){
        $cid=intval($_GET['cid']);
        $sql=$sql." AND `contest_id`='$cid' and num>=0 ";
        $str2=$str2."&cid=$cid";
        require_once("contest-header.php");
}else{
        require_once("oj-header.php");
}
?>
<div>
<?php $order_str=" ORDER BY `solution_id` DESC ";
$start_first=1;
// check the top arg
if (isset($_GET['top'])){
        $top=strval(intval($_GET['top']));
        if ($top!=-1) $sql=$sql."AND `solution_id`<='".$top."' ";
}

// check the problem arg
$problem_id="";
if (isset($_GET['problem_id'])&&$_GET['problem_id']!=""){
	
	if(isset($_GET['cid'])){
		$problem_id=$_GET['problem_id'];
		$num=strpos($PID,$problem_id);
		$sql=$sql."AND `num`='".$num."' ";
        $str2=$str2."&problem_id=".$problem_id;
        
	}else{
        $problem_id=strval(intval($_GET['problem_id']));
        if ($problem_id!='0'){
                $sql=$sql."AND `problem_id`='".$problem_id."' ";
                $str2=$str2."&problem_id=".$problem_id;
        }
        else $problem_id="";
	}
}
// check the user_id arg
$user_id="";
if (isset($_GET['user_id'])){
        $user_id=trim($_GET['user_id']);
        if (is_valid_user_name($user_id) && $user_id!=""){
                $sql=$sql."AND `user_id`='".$user_id."' ";
                if ($str2!="") $str2=$str2."&";
                $str2=$str2."user_id=".$user_id;
        }else $user_id="";
}
if (isset($_GET['language'])) $language=intval($_GET['language']);
else $language=-1;

if ($language>9 || $language<0) $language=-1;
if ($language!=-1){
        $sql=$sql."AND `language`='".strval($language)."' ";
        $str2=$str2."&language=".$language;
}
?>
<form id=simform action="status.php" method="get">
<?php echo $MSG_PROBLEM_ID?>:<input type=text size=4 name=problem_id value='<?php echo $problem_id?>'>
<?php echo $MSG_USER?>:<input type=text size=4 name=user_id value='<?php echo $user_id?>'>
<?php if (isset($cid)) echo "<input type='hidden' name='cid' value='$cid'>";?>
<?php echo $MSG_LANG?>:<select size="1" name="language">
<?php if (isset($_GET['language'])) $language=$_GET['language'];
else $language=-1;
if ($language<0||$language>9) $language=-1;
if ($language==-1) echo "<option value='-1' selected>All</option>";
else echo "<option value='-1'>All</option>";
for ($i=0;$i<10;$i++){
        if ($i==$language) echo "<option value=$i selected>$language_name[$i]</option>";
        else echo "<option value=$i>$language_name[$i]</option>";
}
?>
</select>
<?php echo $MSG_RESULT?>:<select size="1" name="jresult">
<?php if (isset($_GET['jresult'])) $jresult_get=intval($_GET['jresult']);
else $jresult_get=-1;
if ($jresult_get>=12||$jresult_get<0) $jresult_get=-1;
if ($jresult_get!=-1){
        $sql=$sql."AND `result`='".strval($jresult_get)."' ";
        $str2=$str2."&jresult=".strval($jresult_get);
}
if ($jresult_get==-1) echo "<option value='-1' selected>All</option>";
else echo "<option value='-1'>All</option>";
for ($j=0;$j<12;$j++){
        $i=($j+4)%12;
        if ($i==$jresult_get) echo "<option value='".strval($jresult_get)."' selected>".$judge_result[$i]."</option>";
        else echo "<option value='".strval($i)."'>".$judge_result[$i]."</option>"; 
}
echo "</select>";
?>
</select>

<?php if(isset($_SESSION['administrator'])||isset($_SESSION['source_browser'])){
        if(isset($_GET['showsim']))
                $showsim=intval($_GET['showsim']);
        else
                $showsim=0;
        echo "SIM:
                        <select name=showsim onchange=\"document.getElementById('simform').submit();\">
                        <option value=0 ".($showsim==0?'selected':'').">All</option>
                        <option value=50 ".($showsim==50?'selected':'').">50</option>
                        <option value=60 ".($showsim==60?'selected':'').">60</option>
                        <option value=70 ".($showsim==70?'selected':'').">70</option>
                        <option value=80 ".($showsim==80?'selected':'').">80</option>
                        <option value=90 ".($showsim==90?'selected':'').">90</option>
                        <option value=100 ".($showsim==100?'selected':'').">100</option>
                  </select>";
/*      if (isset($_GET['cid'])) 
                echo "<input type=hidden name=cid value='".$_GET['cid']."'>";
        if (isset($_GET['language'])) 
                echo "<input type=hidden name=language value='".$_GET['language']."'>";
        if (isset($_GET['user_id'])) 
                echo "<input type=hidden name=user_id value='".$_GET['user_id']."'>";
        if (isset($_GET['problem_id'])) 
                echo "<input type=hidden name=problem_id value='".$_GET['problem_id']."'>";
        //echo "<input type=submit>";
*/
        
        
        
}
echo "<input type=submit value='$MSG_SEARCH'></form>";
?>
</div>
<table>
<tr  class='toprow'>
<td ><?php echo $MSG_RUNID?>
<td ><?php echo $MSG_USER?>
<td ><?php echo $MSG_PROBLEM?>
<td ><?php echo $MSG_RESULT?>
<td ><?php echo $MSG_MEMORY?>
<td ><?php echo $MSG_TIME?>
<td ><?php echo $MSG_LANG?>
<td ><?php echo $MSG_CODE_LENGTH?>
<td ><?php echo $MSG_SUBMIT_TIME?>
</tr>
<?php if($OJ_SIM){
        $old=$sql;
        $sql="select * from ($sql order by solution_id desc limit 20) solution left join `sim` on solution.solution_id=sim.s_id WHERE 1 ";
        if(isset($_GET['showsim'])&&intval($_GET['showsim'])>0){
                $showsim=intval($_GET['showsim']);
                $sql="select * from ($old ) solution 
                     left join `sim` on solution.solution_id=sim.s_id WHERE result=4 and sim>=$showsim";
                $sql="SELECT * FROM ($sql) `solution`
                        left join(select solution_id old_s_id,user_id old_user_id from solution) old
                        on old.old_s_id=sim_s_id WHERE  old_user_id!=user_id and sim_s_id!=solution_id ";
                $str2.="&showsim=$showsim";
        }
        //$sql=$sql.$order_str." LIMIT 20";
}

$sql=$sql.$order_str." LIMIT 20";
//echo $sql;
if($OJ_MEMCACHE){
	require("./include/memcache.php");
	$result = mysql_query_cache($sql) or die("Error! ".mysql_error());
	$rows_cnt=count($result);
}else{
		
	$result = mysql_query($sql) or die("Error! ".mysql_error());
	$rows_cnt=mysql_num_rows($result);
}
$top=$bottom=-1;
$cnt=0;
if ($start_first){
        $row_start=0;
        $row_add=1;
}else{
        $row_start=$rows_cnt-1;
        $row_add=-1;
}



for ($i=0;$i<$rows_cnt;$i++){
if($OJ_MEMCACHE)
	$row=$result[$i];
else
	$row=mysql_fetch_array($result);
        if ($top==-1) $top=$row['solution_id'];
        $bottom=$row['solution_id'];
        if ($cnt) echo "<tr align=center class='oddrow'>";
        else echo "<tr align=center class='evenrow'>";
        $flag=(!is_running(intval($row['contest_id']))) ||
                        isset($_SESSION['source_browser']) ||
                        isset($_SESSION['administrator']) || 

                        (isset($_SESSION['user_id'])&&!strcmp($row['user_id'],$_SESSION['user_id']));

        $cnt=1-$cnt;

        echo "<td>".$row['solution_id'];
        echo "<td><a href='userinfo.php?user=".$row['user_id']."'>".$row['user_id']."</a>";


       if ($row['contest_id']>0) {
                echo "<td><a href='problem.php?cid=".$row['contest_id']."&pid=".$row['num']."'>";
                if(isset($cid)){
                         echo $PID[$row['num']]."</a>";
                }else{
                        echo $row['problem_id']."</a>";
                }

        }else{
                echo "<td><a href='problem.php?id=".$row['problem_id']."'>".$row['problem_id']."</a>";
        }

       
        if (intval($row['result'])==11 && ((isset($_SESSION['user_id'])&&$row['user_id']==$_SESSION['user_id']) || isset($_SESSION['source_browser']))){
                echo "<td><a href='ceinfo.php?sid=".$row['solution_id']."' class=".$judge_color[$row['result']].">".$judge_result[$row['result']]."</a>";
        }else if (intval($row['result'])==10 && ((isset($_SESSION['user_id'])&&$row['user_id']==$_SESSION['user_id']) || isset($_SESSION['source_browser']))){
                echo "<td><a href='reinfo.php?sid=".$row['solution_id']."' class=".$judge_color[$row['result']].">".$judge_result[$row['result']]."</a>";

        }else{

                if($OJ_SIM&&$row['sim']>80&&$row['sim_s_id']!=$row['s_id']) {
                        echo "<td><span class=".$judge_color[$row['result']].">*".$judge_result[$row['result']]."</span>-<span class=red>";
                       
                        if( isset($_SESSION['source_browser'])){

                                        echo "<a href=showsource.php?id=".$row['sim_s_id']." target=original>".$row['sim_s_id']."(".$row['sim']."%)</a>";
                        }else{

                                        echo $row['sim_s_id'];

                        }
                        if(isset($_GET['showsim'])&&isset($row[13])){
                                        echo "$row[13]";
                                
                        }
                        echo     "</span>";
                }else{

                        echo "<td class=".$judge_color[$row['result']].">".$judge_result[$row['result']];
                }
                
        }
        if (isset($row['pass_rate'])&&$row['pass_rate']>0&&$row['pass_rate']<.98) echo ($row['pass_rate']*100)."%";
        if ($flag){


                if ($row['result']>=4){
                        echo "<td class=red>".$row['memory'];
                        echo "<td class=red>".$row['time'];

                }else{
                        echo "<td>------<td>------";
                }

                if (!(isset($_SESSION['user_id'])&&strtolower($row['user_id'])==strtolower($_SESSION['user_id']) || isset($_SESSION['source_browser']))){
                        echo "<td>".$language_name[$row['language']];
                }else{

                        echo "<td><a target=_blank href=showsource.php?id=".$row['solution_id'].">".$language_name[$row['language']]."</a>/";

                        if (isset($cid)) {

                                echo "<a target=_self href=\"submitpage.php?cid=".$cid."&pid=".$row['num']."&sid=".$row['solution_id']."\">Edit</a>";

                        }else{

                                echo "<a target=_self href=\"submitpage.php?id=".$row['problem_id']."&sid=".$row['solution_id']."\">Edit</a>";

                        }
                }

                echo "<td>".$row['code_length']." B";
                

        }else echo "<td>------<td>------<td>".$language_name[$row['language']]."<td>------";
        echo "<td>".$row['in_date'];
        echo "</tr>\n";
}
mysql_free_result($result);
?>
</table>
<?php echo "[<a href=status.php?".$str2.">Top</a>]&nbsp;&nbsp;";
if (isset($_GET['prevtop']))
        echo "[<a href=status.php?".$str2."&top=".$_GET['prevtop'].">Previous Page</a>]&nbsp;&nbsp;";
else
        echo "[<a href=status.php?".$str2."&top=".($top+20).">Previous Page</a>]&nbsp;&nbsp;";
echo "[<a href=status.php?".$str2."&top=".$bottom."&prevtop=$top>Next Page</a>]";
?>

<?php require_once("oj-footer.php");?>
<?php require_once("./include/cache_end.php");?>
