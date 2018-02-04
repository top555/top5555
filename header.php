<?
include 'settings.php';
$ref = 0;
if (isset($_GET['r'])) {
if (isset($_SESSION['ref'])) {
$ref = $_SESSION['ref'];
}else{
$ref = intval($_GET['r']);
$_SESSION['ref'] = $ref;
}
}
$site_name = mysqli_fetch_array(mysqli_query($db, "select value from settings where `set` = 'site_name'"));
if (isset($_SESSION['user']) && $_SESSION['user'] == "1") {echo'';}else{
$echo = '';
if (isset($_SERVER["HTTP_REFERER"])) {
if (substr_count($_SERVER["HTTP_REFERER"],"redsurf.ru") > 0 || substr_count($_SERVER["HTTP_REFERER"],"rubserf.ru") > 0 || substr_count($_SERVER["HTTP_REFERER"],"profitcentr.com") > 0 || substr_count($_SERVER["HTTP_REFERER"],"www.seo-honest.ru") > 0 || substr_count($_SERVER["HTTP_REFERER"],"infohit.cf") > 0 || substr_count($_SERVER["HTTP_REFERER"],"wmrok.com") > 0 || substr_count($_SERVER["HTTP_REFERER"],"bonusio.ru") > 0 || substr_count($_SERVER["HTTP_REFERER"],"seo-fast.ru") > 0 || substr_count($_SERVER["HTTP_REFERER"],"www.seosprint.net") > 0 || substr_count($_SERVER["HTTP_REFERER"],"socpublic.com") > 0) {echo '';}else{ echo $echo; }
}else{
echo $echo;
}
}
$vs = mysqli_fetch_row(mysqli_query($db, "select sum(`sum`) from sp"));
$s = round($vs[0], 2);
?>
<html>
<head>
<meta charset="utf-8">
<title><?=$site_name['value']; ?></title>
<link href="/css.css" rel="stylesheet">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<div id="loader"></div> 
<div class="top_menu">
<div class="top_menuu">
<span><?=$site_name['value']; ?></span>
<a href="/">Получить бонус</a>
<a href="/ref/">Рефералы</a>
<a href="/stat/">Статистика</a>
<a href="/rules/">Правила</a>
<a style="color: #FFBD00; font-weight: 600;" href="/competition/">Конкурсы</a>
<div title="Мы выплатили" class="balance_payed">
<?
echo '<div class="balance_balance">'.$s.'руб.</div>';
?>
<div class="balance_balance"></div>
</div>
</div>
</div>
<div class="linkslot-h">
<table>
<tr style="background: #55565C;">
<td>
<div class="linkslot-h-l">
<!---верх лево 468 x 60 пикселей>
</div>
</td>
<td>
<div class="linkslot-h-r">
<!---верх право 468 x 60 пикселей>
</div>
</td>
</tr>
</table>
<!---->
</div>
<div class="l_r">
<!---слева>
<br />
</div>
<div class="r_r">
<!---справа>
</div>
<div class="content">