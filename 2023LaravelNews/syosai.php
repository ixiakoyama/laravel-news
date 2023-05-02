<?php
//別ファイルの処理を読み込む
require 'keikokusyosai.php';
$errors = validation($_POST);  #エラーメッセージを変数$errorsに保持


if (empty($rows)) {
    $rows = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {//もし書き込みがあれば送信してくれ
    $delete = $_POST['delete'];
    $comment = $_POST['message'];

    if ($delete && strlen($delete) > 0) { //$deleteかつ$deleteが０以上の時
        $lines = file("message.txt", FILE_IGNORE_NEW_LINES);// ファイルの内容を配列に取り込みます。
        $fp = fopen('message.txt', 'w');  //書き出し専用で開く

    for($i = 0; $i < count($lines); $i++){
        $line = explode(",",$lines[$i]);
        $postnum = $line[0];

    if($postnum != $delete){
        fwrite($fp, $lines[$i]."\n");
    }
    }
    // もう一度読み込み専用で開く
    $fp = fopen('message.txt', 'r');

    } else if ($_GET["id"]) {

        $fp = fopen('message.txt', 'a+b'); //a+bは読み込み/書き込み（追記）でファイルがない場合は新規作成で、下に更新していくモード（＋で描くと読み書きモード）
        $id = uniqid(); //マイクロ秒単位の現在時刻にもとづいた、接頭辞つきの一意な ID を取得します。(ex)コメントとかにつける13桁のID））
        if (empty($errors)){
            fputcsv($fp, [
                ($id),
                ($_GET["id"]),
                ($_POST['message'])
            ]);
        }
    rewind($fp);
    } else {
        $fp = fopen('messages.txt', 'r');// もう一度読み込み専用で開く
    }
    while ($row = fgetcsv($fp)) {
        if (count($row) > 0) {
        $rows[] = $row;
        }
    }
    fclose($fp);
} else {
    $fp = fopen('message.txt', 'r');
    while ($row = fgetcsv($fp)) {
        if (count($row) > 0) {
            $rows[] = $row;
        }
    }
    fclose($fp);
}
?>



<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ニュース詳細</title>
<!--<link rel="stylesheet" href="reset.css">-->
<link rel="stylesheet" href="style.css">
</head>
<body>
<header id="header">
            <a href="http://localhost:8888/onji/index.php" target="_new" rel="noopener"  class="pagetop">Laravel News</a><!--_newは1回目別タブ、2回目は1ページ目を読み込む-->
        </header>


<?php if(!empty($errors)) : ?>
      <?php echo '<ul>'; ?>
        <?php
          foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
          }
        ?>
      <?php echo '</ul>'; ?>
<?php endif ?>

<div class="toukou">
<?php
$post_id = $_GET["id"];
//csvデータを読み込み、idが一致したらデータを書き出し終了する
//csvにはid、タイトル、記事の順番で入っている
if (($handle = fopen("data.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
  if($post_id == $data[0]) {

  echo <<<eof
    <h2>{$data[1]}<br /><br /></h2>
    <p>{$data[2]}<br /></p>
  eof;
  break;
  }
}
  fclose($handle);
}

?>
</div>

<hr>
<div class="komento">
<form method="post" class="honbun1"   action="">
	<div>
		<textarea id="comentomessage" name="message" cols="50" rows="5" class="comentomessage"placeholder="コメントを書く" ></textarea>
	</div>
    <input type="submit" name="button_submit" value="送信" class="comentobutton">
</form>


<?php if (!empty($rows)): ?>
  <ul class="comentoul">
  
    <?php foreach ($rows as $row): ?>
        <article class="comentoarticle">
      <?php $child_id = $row[1]; ?>
      <!-- ここで該当のデータを取得しているぽい -->
      <?php if ($post_id == $child_id): ?>
        <div class="post">
          <li><?=$row[2]?></li>
          <form action="syosai.php?id=<?= $post_id ?>" method="post">
            <input type="hidden" name="delete" value=<?= $row[0] ?>>
            <input type="submit" name="submit" class="btn-submit-comento" value="コメントを消す">
          </form>
        </div>
        </article>
      <?php endif ?>
      
    <?php endforeach; ?>
    </div>
  </ul>
     
<?php else: ?>
  <p>投稿はまだありません</p>
<?php endif; ?>
</div>

<a href="http://localhost:8888/onji/index.php">トップへ戻る</a>
</body>
</html>