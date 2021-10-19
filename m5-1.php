<?php
    //サーバーにアクセスする
    $dsn = "データベース名";
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //１テーブル（今まででいうテキストファイル）を用意する
 $sql = "CREATE TABLE IF NOT EXISTS m5" 
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY,"//このおかげで投稿番号いじる必要はない
. "name char(32),"
. "comment TEXT,"
. "datetime TEXT,"//日時を入れるカラムを作る
. "password TEXT" //パスワード保存欄を追加
.");";
$stmt = $pdo->query($sql);
 
 
    if(!empty($_POST["enum"])){//編集番号が入力されたとき
                 $enum = $_POST["enum"];
                 $epass = $_POST["password"];
                
               
                 //id が一致するときに呼び出す
                 $stmt =$pdo -> prepare('SELECT * FROM m5 WHERE id=:id AND password=:password');
                 $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                 $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                 $id = $enum;
                 $password = $epass;
                 $stmt -> execute();
                 $results = $stmt->fetch(); //fetchallじゃない！
                  
                         $fenum = $results['id'];
                         $ename = $results['name'];
                         $ecomment = $results['comment'];
}
?> 


 <html lang="ja">
 <head>
     <meta charset="UTF-8">
     <title>mission_5</title>
 </head>
 <body>
<html>
    
  [投稿フォーム]
  <form method="post">
  お名前：<input type="name" name="name" value="<?php if(isset($ename)){echo $ename;} ?>"><br>
  コメント：<input type="text" name="comment" value="<?php if(isset($ecomment)){echo $ecomment;}?>"><br>
  パスワード：<input type="password" name="password" placeholder="数字4ケタ" pattern="[0-9]{4}"  size="6">
<!--編集識別番号-->
　<input type="hidden" name="fenum" value="<?php if(isset($fenum)){echo $fenum;} ?>">
  <input type="submit" name ="submit1" value="投稿"><br>
  </form>
  <form method="post">
  [削除番号指定用フォーム]<br>
  削除番号：<input type="number" name="num" >
  パスワード：<input type="password" name="password" placeholder="数字4ケタ" pattern="[0-9]{4}"  size="6">
  <input type="submit" name ="submit2" value="削除"><br>
 </form>
 <form method="post">
 [編集番号申請フォーム]<br>
  編集番号：<input type="number" name="enum">
　パスワード：<input type="password" name="password" placeholder="数字4ケタ" pattern="[0-9]{4}"  size="6">
    <input type="submit" name ="submit3" value="編集">
</form>
<hr>
</body>
</html>
<?php

if(!empty($_POST["submit1"])){
     
    if(empty($_POST["name"])||empty($_POST["comment"])||empty($_POST["password"])){
    echo "未入力の項目があります。<br>";
    }
    
    //新規投稿（名前コメントパスワードがあって、編集識別番号がないとき）    
    elseif(!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["password"])&&empty($_POST["fenum"])){
    $sql = $pdo -> prepare("INSERT INTO m5 (name, comment, datetime, password) VALUES (:name, :comment, :datetime, :password)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':datetime', $datetime, PDO::PARAM_STR);
    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $datetime = date("Y年m月d日 H:i:s");
    $password = $_POST["password"];
    $sql -> execute();
    
    }
        //編集に関して(4要素が撃ち込まれているとき)　
    elseif(!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["password"])&&!empty($_POST["fenum"])){
            
    $id = $_POST["fenum"]; //変更対象となる投稿番号
    $password =  $_POST["password"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $datetime = date("Y年m月d日 H:i:s");
    $sql = 'UPDATE m5 SET name=:name,comment=:comment, datetime=:datetime WHERE id=:id AND password=:password';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':datetime', $datetime, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->execute(); 
    }
}       
if(!empty($_POST["submit2"])){//削除ボタンが押されたときの処理
     
    if(!empty($_POST["num"])){
    $sql = 'delete from m5 where id=:id AND password=:password';//where文が一致を確かめている
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $id = $_POST["num"];
    $password =  $_POST["password"];
    $stmt->execute();   
    }   
}            
    //５投稿を表示する（みんなに見える掲示板の画面）
     $sql = 'SELECT * FROM m5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['datetime'].'<br>';
    echo "<hr>";
    }
unset($pdo);       
?>