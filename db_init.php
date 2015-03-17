<?php
//データベース情報のデフォルト
$host = "localhost";
$dbms = "mysql";
$user = "username_here";
$pass = "password_here";
$schema_name = "sampleDB";
$engine = "MyISAM";
$charset = "utf8";

//ここでデフォルト情報の変更を行えるようにする


echo "どのDBスキーマを初期化しますか？ デフォルト：".$schema_name. "-> ";
$stdin = trim(fgets(STDIN));
if($stdin !== ''){
	$schema_name = $stdin;
	print_line("＝＝確認＝＝変更するデータベースは ".$schema_name."です。");
}

//データベースの初期化は危険なので、本当に初期かするのか聞くようにする
echo "本当に初期化しますか？ [yes/NO]:";
$stdin = trim(fgets(STDIN));
if($stdin !== "yes"){
	print_line("初期化せずに終了します。");
	exit;
}


//今後その他の情報の変更を行えるようにする



try{
	$ifExists = null;//DBが存在しなければnullのまま
	$db = new PDO($dbms.':host='. $host. ';dbname=information_schema', $user, $pass);
	foreach($db->query("SELECT * FROM `SCHEMATA` WHERE SCHEMA_NAME = '{$schema_name}'") as $row) {
		$ifExists = $row;
	}
	//DBがあるか調べる
	if($ifExists){
		//スキーマがある場合,スキーマを削除する処理を
		$db->exec("drop schema ".$schema_name);
	}

}catch (PDOException $e){
	//DBに接続できなかった場合
	print_line("DBに接続できませんでした");
	exit(1);
}

//DBを作成する
try {
	$pdo = new PDO($dbms.':host = $host', $user,$pass);
	print_line("DBに接続できました");
} catch (PDOException $e) {
	print_line("DBに接続できませんでした");
	exit(1);
}

//スキーマを再作成する
print_line("スキーマを作成します");
$pdo->exec("create database if not exists ".$schema_name." default character set utf8")
or die("スキーマを作成できませんでした".PHP_EOL);

//$schema_nameに入る
$pdo->exec("use ".$schema_name);


//tableを作成する処理
//db_init/table/'ENGINE NAME'/*.txtをすべて呼び出す
$dir = __DIR__."/table/";
$dir = glob(rtrim($dir, '/') . '/*');

//var_dump($dir);
//exit;

foreach ($dir as $value) {
    //エンジン名を入れる
    $engine = array_pop(explode("/", $value));

    //このままだとopendirが使えないので、ディレクトリ名にする
    $value = $value . '/';

    if ($dh = opendir($value)) {
        while (($file = readdir($dh)) !== false) {
            // フルパスファイル名を入れる
            $full_file_name = $value . $file;
            // echo $file,PHP_EOL;
            // 末尾がtxtの場合そのファイルを読み込む
            if (preg_match("/sql$/", $full_file_name)) {
                // ファイル名を.前後で分割し、テーブル名に利用する
                $fname_pieces = explode(".", $file);

                // テーブルごとに変わる部分を読み込む
                $create_core = file_get_contents($full_file_name);

                // どのセットを使うのか今後はJSONになる？
                $sqlset = file_get_contents("cakeset.txt");
                $sqlpieces = explode("{}", $sqlset);



                // var_dump($sqlpieces);
                // 実際に実行するSQL文を発行する
                $csql = $sqlpieces[0] . $fname_pieces[0] .
                $sqlpieces[1] . $create_core .
                $sqlpieces[2] . $engine .
                $sqlpieces[3] . $charset .
                $sqlpieces[4];

                $pdo->exec($csql);
            }
        }
    }
}
//作成したテーブルに初期データを入れる処理
//db_init/data/*.csvの内容をすべて呼び出す

$dir = __DIR__."/data/";

if($dh = opendir($dir)){
	while (($file = readdir($dh)) !== false) {
		//フルパスファイル名を入れる
		$full_file_name = $dir.$file;
		//echo $file,PHP_EOL;

		//ファイル名を.前後で分割し、テーブル名に利用する
		$fname_pieces = explode(".",$file);

		//カラム名が入っているかどうかのフラグ
		$flg_column = true;

		//末尾がcsvの場合そのファイルを読み込む
		if (preg_match("/csv$/",$full_file_name)) {
			if (($handle = fopen($full_file_name, "r")) !== false) {

				//insert文を取り出す
				$sqlset = file_get_contents("insertset.txt");

				//テーブル名などを挟むためにsql文を{}で分割
				$sqlpieces = explode("{}", $sqlset);

				//sql文を発行
				//まずはカラム名の前まで
				$sub_ins_sql = $sqlpieces[0].$fname_pieces[0].$sqlpieces[1];

				//この変数に入ったsql文を実行する。スコープの関係でここで宣言
				$ins_sql;

				//csvファイルを１行ずつ読み込む
				while (($line = fgetcsv($handle)) !== false) {

					//空の行はスキップ
					if($line === array(null)){
						continue;
					}

					//カラム名を入れる。
					//var_dump($line);
					if($flg_column){
						//カラム名の場合
						foreach ($line as $id => $rec){
							//print_line($id);
							//print_line($rec);
							$sub_ins_sql = $sub_ins_sql.$rec.",";
						}
						//最後のカンマが余計なので削除
						$sub_ins_sql = substr($sub_ins_sql, 0,-1);

						//VALUEの次までsql発行
						$sub_ins_sql = $sub_ins_sql.$sqlpieces[2];
						//print_line($sub_ins_sql);

						//次からはカラム名ではないのでこのブロックに入れないようにする
						$flg_column = false;
					}else {
						//値の場合
						//まずはカラム名の部分までを代入
						$ins_sql = $sub_ins_sql;

						//値を代入
						foreach ($line as $id => $rec){
							$ins_sql = $ins_sql.$rec .",";
						}
						//最後のカンマが余計なので削除
						$ins_sql = substr($ins_sql, 0,-1);

						//sqlの最後の部分を加える
						$ins_sql = $ins_sql.$sqlpieces[3];

						//実行
						$pdo->exec($ins_sql);
					}

					/*
					 * INSERT INTO {} ({})
					 * VALUES({});
					 */

					/*
					$stmt = $pdo -> prepare("INSERT INTO ".$fname_pieces[0] ."(name, value) VALUES (:name, :value)");

					$stmt->bindParam(':name', $name, PDO::PARAM_STR);
					$stmt->bindValue(':value', $value, PDO::PARAM_INT);
					$stmt->execute();
					*/

					}
					if(!feof($handle)){
						throw new RuntimeException("CSV parsing error in db_init.php");
					}
				}
				fclose($handle);
			}



		}
}


function print_line($message){
	echo $message.PHP_EOL;
}

?>
