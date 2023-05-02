<?php
//別ファイルの処理を読み込む
require 'keikoku.php';

$errors = validation($_POST);  //エラーメッセージを変数$errorsに保持する

//マイクロ秒単位の現在時刻にもとづいた、接頭辞つきの一意な ID を取得します。(ex)コメントとかにつける13桁のID））
$id = uniqid();

//fopenはファイルを開いてくれの関数→fopen('開きたいファイル名' , 'ファイルオープンモード')；
$fp = fopen('data.csv', 'a+b'); //a+bは読み込み/書き込み（追記）でファイルがない場合は新規作成で、下に更新していくモード（＋で描くと読み書きモード）
if ($_SERVER['REQUEST_METHOD'] === 'POST') { //もし書き込みがあれば送信してくれ
    if(empty($errors)){
        fputcsv($fp, [$id,$_POST['title'], $_POST['message']]);//掲示板に書き込みがあれば、IDとタイトルと記事を送って書き込んでくれ
        rewind($fp);//rewind()関数でポインタを先頭に戻す
    }
}
while ($row = fgetcsv($fp)) {
    $rows[] = $row;
}
fclose($fp);//書き込んだファイルを閉じてくれ
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <link href="style.css" rel="stylesheet" >
    <title>ひと言掲示板</title>
</head>
<body>
    <header id="header">
        <a href="http://localhost:8888/onji/index.php" target="_new" rel="noopener"  class="pagetop">Laravel News</a><!--_newは1回目別タブ、2回目は1ページ目を読み込む-->
    </header>

    <section>
        <h2 class="pagetitle">さぁ、最新のニュースをシェアしましょう</h2>

        <?php if(!empty($errors)) : ?>
            <?php echo '<ul>'; ?>
            <?php
                foreach ($errors as $error) {
                    echo '<li>' . $error . '</li>';
                }
            ?>
            <?php echo '</ul>'; ?>
        <?php endif ?>

        <form action="" method="post" onSubmit="return formCheck()" class="honbun" name="honbun">
            <div>
                <label for="title">タイトル:</label>
                <input type="text"  class="messagetitle"  name="title" value="" cols="50" rows="5" onChange="check()">
            </div>
        
            <div class="kiji">
                <label for="message">記事:</label>
                <textarea rows="10" cols="50" name="message"class="message"></textarea>
            </div>

            <div class="input-submit">
                <input type="submit" name="btn-submit" class="button" value="送信">
            </div>
        </form>

        <script type="text/javascript">
            function formCheck() {
                eturn confirm("送信してよろしいですか？");
            }
        </script>

    </section>

    <section>
        <hr>
        <?php if (!empty($rows)): ?> <!--foreach文は配列の値を先頭から自動で取り出して、配列の要素の数だけ繰り返します。（要素をコピー）-->
            <ul class="ul">
            <?php foreach ($rows as $row): ?> <!--//foreach(配列 as 要素の値を格納する変数){
                                                    実行する処理;
                                                }-->
            <article>
                <li class="title"><?php echo  $row[1]?></li><!--(<?=$row[0]?>)を横に入れると13桁のIDも表示）される。１はタイトル-->

                <?php
                    $limit = 30; //文字数の上限
                    $text = $row[2]; //整形したい文字列。２は記事。csvの並び順
                ?>
                <?php if(mb_strlen($text) > $limit): ?> <!--もし記事が30文字より長かったら31文字目から・・・-->
                    <?php $part_text = mb_substr($text,0,$limit); ?><!--取得した文字列の一部を返します。-->
                        <li class="naiyou"><?=$part_text. ・・・;?></li>
                    <?php else: ?>
                        <li class="naiyou"><?=$text?></li><!--30文字より短ければそのまま記事を表示-->
                        <br>
                <?php endif; ?>
            <!--substr (引数1、引数2、引数3)
                引数１は記事（取得したい文字列）、引数２は数え開始位置（１にすると2文字目から表示）、引数３は取得したい文字数-->

            <!--CSVデータ読み込み.文字コードが「一致する場合」-->
            <?php
            // 読み込みモードでファイルポインタを取得
                if (($handle = fopen("data.csv","r")) !== FALSE) { //"data.csv"を"r"（読み取りモード）で開けて

                 // ファイルポインタから行を取得
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                echo <<<EOF
                <a href="syosai.php?id={$row[0]}">記事全文・コメントを見る</a>
                EOF; //「EOF」は End Of File、
                break; //ループをおわる
            }
                fclose($handle);
            }
        ?>
 </article>
        <!--ヒアドキュメント
            <
                $変数 = <<<終了の文字列 
                「文字列を記述」

                終了の文字列;
            ?>
            -->


                <!--fgetcsv関数
                        第1引数	ファイルポインタを指定。
                        第2引数	CSVファイルにある最大行長を指定。指定しないと最大行長の制限はないが遅くなる。
                        第3引数	区切り文字を指定。(デフォルト「,」)
                        第4引数	囲み文字を指定。(デフォルト「”」)-->
        <hr>
        <?php endforeach; ?>
            </ul>
    <?php else: ?> <!--もし書き込みがなかったら-->
        <p>投稿はまだありません</p>
    <?php endif; ?>
    </section>
</body>
</html>