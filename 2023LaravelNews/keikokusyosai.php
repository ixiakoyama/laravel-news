<?php
    function validation($request) {
    $errors = [];

    if (isset($_POST['message']) && empty($request['message'])){
        $errors[] = 'コメントは必須項目です。';
    }  elseif (50 <= mb_strlen($request['message'])){
        $errors[] = 'コメントは50字以内です。';
    }
    return $errors;
    }
?>