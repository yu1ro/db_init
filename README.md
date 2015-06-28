# db_init
データベースの初期化ツールです。<br>
[cakePHP2.xの規約](http://book.cakephp.org/2.0/ja/getting-started/cakephp-conventions.html#id3)に合うようなテーブルに自動でなります。<br>
もちろんcakePHPを利用しないプロジェクトにも利用できますが、<br>
id, created, modifiedカラムが自動でつくようになっております<br>
（今後選べるようにしたい）<br>

## 警告
このプログラムは最初に指定されたデータベースを削除後、新たにデータベースを作成するため<br>
元々入っていたデータは一旦**削除**されます<br>
（今後選べるようにしたい）<br>

# インストール
インストール方法は現在２つあります<br>
- zipをダウンロード
- GitHubからクローン

#### 前提
- PHP
- MySQL<br>
のインストールが完了していること
データベースに関してはMySQL以外でも使えると思いますが、未確認です。

### zipでダウンロード
1. ダウンロード<br>
 このリポジトリの[トップページ](https://github.com/yu1ro/db_init)右側にある<br>
 Download ZIPをクリックしてください。
2. 解凍
 ダウンロードしたディレクトリに移動後、<br>
 いつもどおり解凍してください。<br>
 ```
 unzip db_init-master.zip
 ```
3. 使い方の説明に進む<br>
 HowToUse.mdをご覧ください。
 使い方の説明ではディレクトリの名前をdb_init/として説明しておりますので、<br>
 名前を変更しておいてもいいでしょう。<br>
 ```
 mv db_init-master db_init
 ```

### GitHubからクローン
phpが使えるお好みのディレクトリで<br>
```bash
git clone git@github.com:yu1ro/db_init.git
```

### composerから
準備中のため利用できません<br>


# 使い方
howToUse.md or howToUse_ja.mdにてご説明します。<br>

# ご質問・ご要望など
ご質問・ご要望などございましたらお気軽にIssueでどうぞ:blush:<br>
英語でも構いませんが日本語の方が得意です<br>
もちろんプルリクエストも大歓迎です。

