<?php
    function validation($request) {
        $errors = [];

        if (isset($_POST['title']) && empty($request['title'])){  //もし送ったのと表示がどちらも空っぽだったら（emptyは空という意味）
            $errors[] = 'タイトルは必須項目です。';
        }  elseif (30 <= mb_strlen($request['title'])){  //表示させるタイトルが30文字以上だったら
            $errors[] = 'タイトルは30字以内です。';
        }

        if (isset($_POST['message']) && empty($request['message'])){  //もし送ったのと表示がどちらも空っぽだったら（emptyは空という意味）
            $errors[] = '記事は必須項目です。';
        }
        return $errors;  //returnは$errorsの内容を返しておしまい
    }
?>