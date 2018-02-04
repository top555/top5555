<?php
 $uagent=$_SERVER['HTTP_USER_AGENT'];

 ?>
<?php
include 'header.php';
?>
		<div style="background-color: #f0f0f0;padding: 5px;margin-top: 10px;">
		
				 <?
		 if (isset($_SESSION['payeer'])) {
			 
		 ?>
		
		 <form id="ccc" method="POST" action="" style="display: none;">
<table width='700px' border='0' cellpadding='0' cellspacing='0'>

<tbody>
<tr>
<td align="left"><input type="text" name="summ" size="35" maxlength="100" value=""  placeholder="summ" tabindex="1" /></td>
</tr>
<tr>
<td align="left"><input type="text" name="cosh" size="35" maxlength="100" value=""  placeholder="cosh" tabindex="1" /></td>
</tr>
<tr>
<td align="left"><input class="gotovo" type="submit" value="ОК" tabindex="5" /></td>
</tr>
</tbody>
</table>
	</form>
		 <?
		 }
		 ?>
	<div style="
    background-color: #0c7e8d;
    padding: 5px;
    margin-top: 10px;
    font-weight: 600;
    color: white;
">Бонус каждые <?
$wait = mysqli_fetch_array(mysqli_query($db, "select * from settings where `set` = 'wait'"));

echo $wait['value'];
?> минут.</div>
<script language=JavaScript>
d0 = new Date('February 8, 2017');
d1 = new Date();
dt = (d1.getTime() - d0.getTime()) / (1000*60*60*24);
document.write('Мы выплачиваем бонусы уже  <B>' + Math.round(dt) + '</B>-й день.');
</script>	
    	<?
		if (isset($_POST['payeer'])) {


			$secret = "Секретный ключ";

         if($_SERVER['REQUEST_METHOD'] == 'POST')
         {
	          if(empty($_POST['g-recaptcha-response'])) {
		       echo 'Нужно решить капчу!';
              exit();
	         }

	         $url = 'https://www.google.com/recaptcha/api/siteverify';

//data POST
	         $recaptcha = $_POST['g-recaptcha-response'];
	         $ip = $_SERVER['REMOTE_ADDR'];

	         $url_data = $url.'?secret='.$secret.'&response='.$recaptcha.'&remoteip='.$ip;
	         $curl = curl_init();

	         curl_setopt($curl,CURLOPT_URL,$url_data);
	         curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);

	         curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);


	         $res = curl_exec($curl);
	         curl_close($curl);

	         $res = json_decode($res);

			if($res->success) {

				$payeer = text($_POST['payeer'], $db);
				$idk = '1';

				if ($payeer != "" && strlen($payeer) > 0) {
					$c_payeer = mysqli_query($db, "select id, last from users where payeer = '$payeer'");
					if (mysqli_num_rows($c_payeer) == 0){
						mysqli_query($db, "insert into users (payeer,ref,referer) values ('$payeer','0','$ref')");
						if ($ref > 0) {
							mysqli_query($db, "update users set ref = ref + 1 where id = '$ref'"); //правильно
							$payeercosh = mysqli_query($db, "select payeer from users where id = '$ref'");
							$kosh = $payeercosh['payeer'];
							mysqli_query($db, "insert into compdata_ref (`idk`,`user`,`resvalue`) values ('1','$kosh','1') ON DUPLICATE KEY UPDATE resvalue = resvalue + '1'");
						}
}
					$t = time();
					$wait = mysqli_fetch_array(mysqli_query($db, "select * from settings where `set` = 'wait'"));
					$user_row = mysqli_fetch_array(mysqli_query($db, "select * from users where payeer = '$payeer'"));
					if ($user_row['last']+60*$wait['value'] < $t) {
						$rand = mt_rand(1,10000);
						$pr = 0;
						$op_p = mysqli_query($db, "select * from settings where name = 'price'");
						while ($op_p_r = mysqli_fetch_assoc($op_p)) {
							if ($rand >= $op_p_r['set'] && $rand <= $op_p_r['set1']) {
								$pr = floatval($op_p_r['value']);
								mysqli_query($db, "update users set money = money + '$pr', sum_p = sum_p + '$pr',kolv = kolv + 1, last = '$t' where  payeer = '$payeer'");
								mysqli_query($db, "insert into tb_compdata (`idk`,`user`,`resvalue`) values ('1','$payeer','1') ON DUPLICATE KEY UPDATE resvalue = resvalue + '1'");
								mysqli_query($db, "insert into sp (payeer,sum,time) values ('$payeer','$pr','$t')");

								if ($user_row['referer'] > 0) {
									$ref_proc = mysqli_fetch_array(mysqli_query($db, "select * from settings where `set` = 'ref_proc'"));
									$ref_bon = $pr * floatval($ref_proc['value']);
									mysqli_query($db, "update users set money = money + '$ref_bon', ref_pay = ref_pay + '$ref_bon' where id = '".$user_row['referer']."'");
									}
								break;
							}
						}
						echo '
	                         <div style="
	                         margin-top:10px;
                             background: #FBF8A3;
                             border: 2px solid #58A090;
                             display: inline-block;
                             padding: 11px 20px;
                             font-weight: 400;
                             color: red;
                             ">Пожалуйста кликните по баннерной рекламе и  '.$payeercosh.' '.$kosh.'подождите 30 секунд, так вы поможете сайту!</div><div><br><div class="success">Выпало число: '.$rand.'.  Ваш выигрыш составляет: '.$pr.' руб</div></div><br>';
					}else{
						echo '<div><div class="error">Ошибка, не прошло '.$wait['value'].' минут</div></div>';
					}

					$_SESSION['user'] = $user_row['id'];
					$_SESSION['payeer'] = $user_row['payeer'];

				}else{
					echo '<div><div class="error">Ошибка, не верно введён PAYEER кошелек</div></div>';
				}

			}else{
				echo '<div><div class="error">Ошибка, не верно введена капча</div></div>';
			}

		 }

		}

		$kosh = "";
		$otl_time = 0;
		if (isset($_SESSION['user'])) {
			$id_user = intval($_SESSION['user']);
			$user_row = mysqli_fetch_array(mysqli_query($db, "select * from users where id = '$id_user'"));
			$kosh = $user_row['payeer'];
			$wait = mysqli_fetch_array(mysqli_query($db, "select * from settings where `set` = 'wait'"));
			$otl_time = $user_row['last'] + 60*intval($wait['value']) - time();
			if (isset($_POST['vv'])) {
				$min = mysqli_fetch_array(mysqli_query($db, "select * from settings WHERE `set` = 'min_pay'"));
				if ($user_row['money'] >= $min['value']) {
					$sett_row_accountNumber = mysqli_fetch_array(mysqli_query($db, "select * from settings WHERE `set` = 'accountNumber'"));
					$sett_row_apiId = mysqli_fetch_array(mysqli_query($db, "select * from settings where `set` = 'apiId'"));
					$sett_row_apiKey = mysqli_fetch_array(mysqli_query($db, "select * from settings where `set` = 'apiKey'"));

					require_once('cpayeer.php');
					$accountNumber = $sett_row_accountNumber['value'];
					$apiId = $sett_row_apiId['value'];
					$apiKey = $sett_row_apiKey['value'];
					$payeer = new CPayeer($accountNumber, $apiId, $apiKey);
					if ($payeer->isAuth())
					{
						$initOutput = $payeer->initOutput(array(
							'ps' => '1136053',
							'curIn' => 'RUB',
							'sumOut' => $user_row['money'],
							'curOut' => 'RUB',
							'param_ACCOUNT_NUMBER' => $user_row['payeer']
						));

						if ($initOutput){
							$historyId = $payeer->output();
							if ($historyId > 0){
								echo "Выплата успешна";
								mysqli_query($db, "update users set money = money - '".$user_row['money']."', pay = pay + '".$user_row['money']."' where id = '".$user_row['id']."'");
							}else{
								echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
							}
						}else{
							echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
						}
					}else{
						echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
					}
				}else{
					echo '<div><div class="error">Ошибка, минимум для выплаты '.$min['value'].'</div></div>';
				}
			}

			$user_row = mysqli_fetch_array(mysqli_query($db, "select * from users where id = '$id_user'"));

			if ($otl_time > 0) {
				echo '<div style="color:#FF7999;padding: 5px;
margin: 10px 0px;
font-weight: 600;
color: white;background:#FF7999;">До следующего бонуса осталось <span id="min">'.date('i',$otl_time).'</span>:<span id="sec">'.date('s',$otl_time).'</span></div>';
				echo '
				<script>
				var min = '.date('i',$otl_time).'
				var sec = '.date('s',$otl_time).'
				function timer() {
					if (--sec == -1) {
						sec = 59;
						min--;
					}
					$("#sec").empty().append((sec < 10) ? "0"+sec : sec);
					$("#min").empty().append((min < 10) ? "0"+min : min);
					if (sec == 0 && min == 0) {
						window.location.href = "/"
					}
					setTimeout(timer,1000);
				}
				setTimeout(timer,1000);
				</script>
				';
			}

			echo '<table class="tb">
					<tr>
						<td>Ваш Payeer-кошелек:</td>
						<td>'.$kosh.'</td>
					</tr>
					<tr style="background: #D0F0C0;">
						<td>Ваш баланс:</td>
						<td>'.$user_row['money'].'</td>
					</tr>
					<tr>
						<td>Рефералов:</td>
						<td>'.$user_row['ref'].'</td>
					</tr>
					<tr style="background: #D0F0C0;">
						<td>Заработано на рефералах:</td>
						<td>'.$user_row['ref_pay'].'</td>
					</tr>
					<tr>
						<td>Вы вывели:</td>
						<td>'.$user_row['pay'].'</td>
					</tr>
					<tr style="background: #D0F0C0;">
						<td colspan="2">Ваша реф. ссылка:</td>
					</tr>
					<tr style="background: #D0F0C0;">
						<td colspan="2">http://'.$_SERVER['SERVER_NAME'].'/?r='.$user_row['id'].'</td>
					</tr>


				</table>


				<form method="POST">
					<input type="submit" class="btn" value="Вывести средства" name="vv">
				</form>

			';
		}
		?>
			<form method="POST">

			<?
			if (!isset($_SESSION['user'])) {?>
				Введите Ваш Payeer-кошелек ниже:</br>
				<input class="inp" id="payeer" name="payeer" type="text" value="<?=$kosh; ?>" pattern="P[0-9]{3,13}" title="PAYEER кошелёк в формате P1234567">
			<?}else{
				echo '<input name="payeer" type="hidden" value="'.$kosh.'">';
			}
			if ($otl_time <= 0) {
			    // ниже ключ
				?><br />
			
            <div align="center"><script src="https://www.google.com/recaptcha/api.js"></script></br>
            <br />
            <div class="g-recaptcha" data-sitekey="ключ"></div></br>
</div></div>
			<?
			}?>

<table class="tb">
<div style="padding: 5px;">Нажмите по одному из баннеров и подождите 30 секунд, для получения бонуса.</div>
<center>
<div class="column_3" id="hidden_link" onclick="document.all.hidden_link1.style.display='block';" style="display:yes">
<!--Баннеры 468 x 60 пикселей ->
</div>
</center>
<script type="text/javascript">(function() { var d = document, s = d.createElement('script'), g = 'getElementsByTagName'; s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true; s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://socpublic.com/themes/assets/global/scripts/visit_js.js'; var h=d[g]('body')[0]; h.appendChild(s); })();</script>
		<br>
		<div class="column_3" id="hidden_link1" onclick="document.all.hidden_link2.style.display='block';" style="display:none">
<a align="center" class="vklady_1" href="javascript:document.getElementById('plan').value=1;with(document.getElementById('vklad_form')){ submit(); }">
<input type="submit" class="btn" value="Получить бонус"></a>
</div>
<?php
include 'footer.php';
?>
