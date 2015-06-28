# 使い方
このツールの使い方を説明します<br>

# 前提
インストールが完了していることを前提としております。<br>
まだインストールが完了していない方はREADME.mdを参照してください。<br>

# 初期設定
db_init.phpの先頭部分を変更します。<br>
9行目までの変数への代入部分を変更してください。<br>
今後、この設定部分を別ファイルに分ける予定です。<br>

# とにかく動かしてみる

```bash
$ cd /path/to/db_init
$ php db_init.php
どのDBスキーマを初期化しますか？ デフォルト：sampleDB-> fooDB
＝＝確認＝＝変更するデータベースは fooDBです。
本当に初期化しますか？ [yes/NO]:yes
DBに接続できました
スキーマを作成します
$
```
とすると、、、これ↓↓が<br>
```SQL
mysql> show databases;
+--------------------+
| Database           |
+--------------------+
| information_schema |
| mysql              |
| performance_schema |
+--------------------+
3 rows in set (0.00 sec)
```
こうなる
```SQL
mysql> show databases;
+--------------------+
| Database           |
+--------------------+
| information_schema |
| fooDB              |
| mysql              |
| performance_schema |
+--------------------+
4 rows in set (0.00 sec)
```
ついでにこうなる<br>

```SQL
mysql> use fooDB
Reading table information for completion of table and column names
You can turn off this feature to get a quicker startup with -A

Database changed
mysql> show tables;
+---------------------+
| Tables_in_fooDB     |
+---------------------+
| innodb_sample_table |
| myisam_sample_table |
+---------------------+
2 rows in set (0.00 sec)
```
これは、<br>
db_init/table/InnoDBに<br>
innodb_sample_table.sql<br>
があり、<br>
db_init/table/MyISAMに<br>
myisam_sample_table.sql<br>
があるからです。<br>
このSQLファイルにid, created, modified***以外***のカラム設定を書き込んでください。<br>
正しいSQLの一部になっていれば、そのカラム設定が反映されて初期化されるはずです。

# 動かない！？
動かない時は一度ユーザー名・パスワードを確認して見てください。<br>
それでもだめならIssueで呼びかけてください。