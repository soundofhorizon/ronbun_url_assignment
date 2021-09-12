<?php
    header('Content-type: application/json; charset=utf-8'); // ヘッダ（データ形式、文字コードなど指定）
    $package_sql = filter_input(INPUT_POST, 'package_sql'); // 送ったデータを受け取る（GETで送った場合は、INPUT_GET）
    $single_sql = filter_input(INPUT_POST, 'single_sql');

    $result_flag_package = pg_query($package_sql);

    if (!$result_flag_package) {
        die('Package INSERTクエリーが失敗しました。'.pg_last_error());
    }

    $result_flag_single = pg_query($single_sql);

    if (!$result_flag_single) {
        die('Single INSERTクエリーが失敗しました。'.pg_last_error());
    }

    $param = "Success";

    echo json_encode($param); //　echoするとデータを返せる（JSON形式に変換して返す）
?>

