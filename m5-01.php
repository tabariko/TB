<html>
    <head>
        <meta name="viewport" 
              content=
                "width=320, height=480,initial-scale=1.0, 
                minimum-scale=1.0, maximum-scale=2.0,
                user-scalable=yes"
        >
        <meta charset="utf-8">
        <title>やさしい掲示板</title>

    </head>
    <body>
    <body bgcolor="#fbeab7" text="#4e454a">
    <h1> ◆◆ やさしい掲示版 ◆◆</h1>
    
<?php

	// DB接続設定
	$dsn = 'データベース名';
    $user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
    //「misson5」というテーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS misson5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	//日時とパスワードを項目として追加
	. "date TEXT,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);
    
        

//➀投稿フォームの各テキストボックスの中身が空でなく
//編集モードでないなら
if(!empty($_POST["name"])&&!empty($_POST["comment"])
&&!empty($_POST["pass"])&&empty($_POST["num_mark"])){
    
//データベースにデータを入力（ここから）//
    $sql = $pdo -> prepare
    (
    //テーブルmisson5内の項目name,comment,date,passに
    //パラメーター:name,:comment,:date,:passを代入
    "INSERT INTO misson5 (name, comment, date, pass) 
    VALUES (:name, :comment, :date, :pass)"
    );
    //それぞれの項目について、パラメーターに変数を当てはめる
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
    //変数に実際の値を代入する
	$name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y年m月d日 H:i:s");
    $pass = $_POST["pass"];
	$sql -> execute();
//データベースにデータを入力（ここまで）//

//②削除フォームの各テキストボックスの中身が空でないなら
}elseif(!empty($_POST["num_remove"])
        &&!empty($_POST["pass_remove"])){

    $id = $_POST["num_remove"];
    $pass_remove = $_POST["pass_remove"];
     
    $sql = 'SELECT * FROM misson5 WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
    $stmt->execute();                             // ←SQLを実行する。
    $results = $stmt->fetchAll(); 
	foreach ($results as $row){
		if($row['pass'] == $pass_remove){
		    $sql = 'delete from misson5 where id=:id';
         	$stmt = $pdo->prepare($sql);
        	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
        	$stmt->execute();
		}
	}
    
	
            
//③編集フォームの各テキストボックスの中身が空でなく
}elseif(!empty($_POST["num_edit"])
        &&!empty($_POST["pass_edit"])){

    $id = $_POST["num_edit"];  
    $pass_edit = $_POST["pass_edit"];
    
	$sql = 'SELECT * FROM misson5 WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
    $stmt->execute();                             // ←SQLを実行する。
    $results = $stmt->fetchAll(); 
	foreach ($results as $row){
        if($row['pass'] == $pass_edit){
            $num2 = $row['id'];
            $name2 = $row['name'];
            $comment2 = $row['comment'];
            $pass2 = $row['pass'];
        }
	}    
}

?>




<!_入力フォームを作成_>
<form action="" method="post">
    【🌼投稿フォーム🌼】
    <br>
    <input type="text" name="name" placeholder="名前"
            value = "<?php if(isset($_POST["edit"])){
                                echo $name2;
                            }?>">
    <br>
    <input type="text" name="comment" placeholder="コメント"
            value = "<?php if(isset($_POST["edit"])){
                                echo $comment2;
                            }?>">
    <input type="hidden" name="num_mark"
            value = "<?php if(isset($_POST["edit"])){
                                echo $num2;
                            }?>">
    <br>
    <input type="text" name="pass" placeholder="パスワード"
            value = "<?php if(isset($_POST["edit"])){
                                echo $pass2;
                            }?>">
    <button type="submit" name="submit" 
        style="background-color:#4e454a;">
        <span style="color:white">投稿</span></button>
    <br><br>
    【🌼削除フォーム🌼】
    <br>
    <input type="number" name="num_remove" placeholder="削除番号">
    <br>
    <input type="text" name="pass_remove" placeholder="パスワード">
    <button type="submit" name="remove" 
        style="background-color:#4e454a;">
        <span style="color:white">削除</span></button>
    <br><br>
    【🌼編集フォーム🌼】
    <br>
    <input type="number" name="num_edit" placeholder="編集番号">
    <br>
    <input type="text" name="pass_edit" placeholder="パスワード">
    <button type="submit" name="edit"
        style="background-color:#4e454a;">
        <span style="color:white">編集</span></button>
    <br>
</form>




<?php
//③-2 名前とコメントとパスワードの中身が空でなく
//編集モードであるなら
if(!empty($_POST["name"])&&!empty($_POST["comment"])
&&!empty($_POST["pass"])&&!empty($_POST["num_mark"])){
    
    $id = $_POST["num_mark"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y年m月d日 H:i:s");
    $pass = $_POST["pass"];
    
	$sql = 'UPDATE misson5 SET
	name=:name,comment=:comment,date=:date,pass=:pass
	WHERE id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	$stmt->bindParam(':date', $date, PDO::PARAM_STR);
	$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();    

}

if(isset($_POST["submit"])&&empty($_POST["name"])){
    echo "<br>名前を入力してね<br>";
}elseif(isset($_POST["submit"])&&empty($_POST["comment"])){
    echo "<br>コメントを入力してね<br>";
}elseif(isset($_POST["submit"])&&empty($_POST["pass"])){
    echo "<br>パスワードを入力してね<br>";
}

if(isset($_POST["remove"])&&empty($_POST["num_remove"])){
    echo "<br>削除番号を入力してね<br>";
}elseif(isset($_POST["remove"])&&empty($_POST["pass_remove"])){
    echo "<br>パスワードを入力してね<br>";
}elseif(isset($_POST["remove"])&&!empty($_POST["num_remove"])
        &&$_POST["num_remove"]!==$row['id']){
    echo "<br>その番号の投稿は存在しないよ<br>";
}elseif(isset($_POST["remove"])&&!empty($_POST["pass_remove"])
        &&$_POST["pass_remove"]!==$row['pass']){
    echo "<br>パスワードが一致しないよ<br>";
}

if(isset($_POST["edit"])&&empty($_POST["num_edit"])){
    echo "<br>編集番号を入力してね<br>";
}elseif(isset($_POST["edit"])
        &&empty($_POST["pass_edit"])){
    echo "<br>パスワードを入力してね<br>";
}elseif(isset($_POST["edit"])&&!empty($_POST["num_edit"])
        &&$_POST["num_edit"]!==$row['id']){
    echo "<br>その番号の投稿は存在しないよ<br>";
}elseif(isset($_POST["edit"])&&!empty($_POST["pass_edit"])
        &&$_POST["pass_edit"]!==$row['pass']){
    echo "<br>パスワードが一致しないよ<br>";
}


echo "<br>＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊<br>";
echo "【🌻投稿一覧🌻】<br><br>";

$sql = 'SELECT * FROM misson5';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].'．';
		echo $row['name'];
		echo '「'.$row['comment'].'」';
		echo $row['date'].'<br>';
	}

?>
        <br><br>
        <hr>
        <br>こちらは<br>
        <ul>
            <li>「易しい」操作</li>
            <li>「優しい」投稿</li>
        </ul>
        がコンセプトの掲示板です☻<br><br>

    </body>
</html>