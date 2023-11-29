# Mr.TESTER
ノートをマーキングして登録し、繰り返し閲覧することで、インプットとアウトプットの両方の面から記憶定着補助効果が大いに期待できるウェブアプリです。

# 概要｜Description
　担当業務でJQueryやbootstrapのバリデーション、MySQLのフォーマット機能等を使う機会があったので、そこで得た知識をもっと活かすべく、その業務の卒業制作的な意味合いも兼ねてこのアプリを作りました。<br>
　大事な情報。一度収集して記憶出来ても、なかなか記憶に定着せずに、いつの間にか抜け落ちてしまい、思い出せない。活かせない・・・。このアプリは、そんなもどかしさにお困りのあなたの、頼れる味方になってくれるかもしれませんよ！？<br>
　情報収集、情報整理、試験対策、学びなおし・・・お好きな用途でご利用ください。

# インストール方法｜Install
## クローンする場合｜Clone
ターミナルまたはコマンドプロンプトで、こちらを入力して下さい。パッケージをインストールできます。
```
$ git clone https://github.com/Kampanman/MrTESTER.git
```
パッケージを別名で保存する場合は、以下のように入力して下さい。
```
$ git clone https://github.com/Kampanman/MrTESTER.git [お好きなプロジェクト名]
```
## サーバーへのアップロード｜Upload
クローンが終了しましたら、お使いのサーバーにアップロードして下さい。
尚、製作者が利用しているサーバーはこちらです。
```
https://secure.sakura.ad.jp/rs/cp/
```
## DBコネクション設定｜Database Connection
/MrTESTER/server/properties.phpにアクセスし、$dsn,$dbname,$username,$passwordのそれぞれを、お使いのデータベースに合わせて編集してください。
## SQL読み込み｜SQL Reading
お使いのMySQLで、/MrTESTER/sqlに格納されているsqlファイルをインポートしてください。

# 最近の更新｜Recent updates
- 基本機能搭載完了：2023/11/30

# 環境と使用言語｜Requirement and Language
- フロントフレームワーク：JQuery 3.3.1
- サーバー言語：PHP 7.4.30
- サーバー：Apache/2.4.54（さくらレンタルサーバー）
- データベース：MySQL 5.7（さくらレンタルサーバー）

# その他｜Note
本ページのカラーテーマは、JR常磐線のラインカラーみたいにしています。

# 文責｜Author
- APT-I.T
