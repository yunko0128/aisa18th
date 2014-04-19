<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
<style>
	form{display:inline;}
</style>
</head>
<body>
<?php
//------------
//DBに入るお
//------------
	//定数定義　C4SAの環境変数を定義する。
define('DB_NAME', getenv('C4SA_MYSQL_DB')); 
define('DB_USER', getenv('C4SA_MYSQL_USER'));
define('DB_PASSWORD', getenv('C4SA_MYSQL_PASSWORD'));
define('DB_HOST', getenv('C4SA_MYSQL_HOST'));

//ユーザー名、パスワードの変数
$dsn = 'mysql:dbname=' . DB_NAME . ';' . 'host=' . DB_HOST . ';' . 'charset=utf8' . ';' ;

//DBしかライブラリ。ユーザー名、パスワードを入れてDB引っ張ってくる。
$db = new PDO($dsn,DB_USER,DB_PASSWORD);

//echo "My SQLに入れました";

//------------
//ここでDBに入れた
//------------

//------------
//DBにinsertする
//------------
function insertFunc($t){
	global $db;
	$statementObject= $db->prepare('insert into aisa18Table(txt) values(?)'); // (?)：とりあえずなんか入るよ
	$rtn=$statementObject->execute(array($t)); //array：複数列対応するため。
	var_dump($rtn); //insertできたかチェック。trueなら入ってる。
	var_dump($db->errorInfo()); //insertのエラーログを出す。
}

//------------
//パラメータ取得
//------------
$pal=$_GET["page"];
$num = intval($pal);
$min = $num*10;
$max =$min+9;
echo '<p>パラメータ'.$num.'</p>';
echo '<p>'.($min+1).'件</p>';
echo '<p>〜'.($max+1).'件</p>';


//------------
//DBを取得
//------------
function selectFunc($from){
	global $db;
    error_log("from $from");
	$statementObject = $db->prepare(' select * from aisa18Table limit 10 offset ?' );
    $statementObject->bindValue(1,$from,PDO::PARAM_INT);
	$statementObject->execute();
	return $statementObject; //selectFunc()の外で使いたいので、一旦returnしとく。
}

//------------
//DBから削除
//------------
function deleteFunc($i){
	global $db;
	$statementObject= $db->prepare('delete from aisa18Table where postID=?'); 
	$rtn=$statementObject->execute(array($i)); 
	var_dump($rtn); //insertできたかチェック。trueなら入ってる。
	var_dump($db->errorInfo()); //insertのエラーログを出す。
}


//------------
//出し分け
//------------
if(isset($_POST['action'])){
	$action = $_POST['action'];
	if($action == 'insert'){
		if(isset($_POST['txtForm'])){
			insertFunc($_POST['txtForm']);
			echo 'insert ok' . $_POST['txtForm'];
			echo '<a href="/">back</a>';
		}
		}else if($action == 'delete'){
			deleteFunc($_POST['id']);
		echo '<a href="/">back</a>';
		}
}else{


//------------
//レコード数をカウント
//------------  
function countFunc(){
	global $db;
	$countRecords = $db->prepare("select count(*) from aisa18Table");
	$countRecords ->execute();
	$row = $countRecords->fetch();
	//var_dump($row); 
	echo '<p>投稿数：'.$row[0].'</p>';
}
$result = countFunc();
	echo $result;
?>
<div>
<form method="post">
	<input type="text" name="txtForm" size=50>
	<input type="hidden" name="action" value="insert">
	<input type="submit" value="投稿">
</form>
	
<?php

	//------------
	//表示する
	//------------
	$contents = selectFunc($min);
	foreach($contents as $post){
		echo '<li>' . htmlspecialchars($post['txt']) ;
?>

<form method="post">
	<input type="hidden" name="id" value="<?php echo $post['id']; ?>" />
	<input type="hidden" name="action" value="delete">
	<input type="submit" value="削除">
</form>
		
		
<?php    
		echo '</li>' ;
	}
}
?>





</div>

</body>
</html>