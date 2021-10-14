<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>URL Assignment System</title>
        <?php
            header("Cache-Control:no-cache,no-store,must-revalidate,max-age=0");
            header("Pragma:no-cache");
            function main(){
                if(isset($_POST['howmany'])&&isset($_POST["url_assignment"])&&isset($_POST["your_id"])){

                    $conn = pg_connect(getenv("DATABASE_URL"));
                    // SQLで情報を取得
                    $package_query = pg_query($conn, 'select Package_query from url_assignment;');
                    while ($row = pg_fetch_row($package_query)) {
                        $package_query_result = $row[0];
                    }
                    $single_query = pg_query($conn, "select Single_query from url_assignment;");
                    while ($row = pg_fetch_row($single_query)) {
                        $single_query_result = $row[0];
                    }
                    $accepted_student_number = pg_query($conn, "select student_number from accepted_student_number;");
                    while ($row = pg_fetch_row($accepted_student_number)) {
                        $accepted_student_number_result = $row[0];
                    }

                    //ここで取得したQueryはString表記なのでArrayにする。
                    $package_query_result = explode("},{",substr($package_query_result, 2, strlen($package_query_result)-4));
                    //Packageについては、各要素がArrayであってほしいので、更に各要素をexplode.
                    for($i = 0; $i < count($package_query_result); $i++){
                        $package_query_result[$i] = explode("," , $package_query_result[$i]);
                    }

                    $single_query_result = explode("," , substr($single_query_result, 1, strlen($single_query_result)-2));

                    $accepted_student_number_result = explode("," , substr($accepted_student_number_result, 1, strlen($accepted_student_number_result)-2));

                    //既に一度でもURLの申請がされてたらreturn
                    if(in_array($_POST["your_id"], $accepted_student_number_result)){
                        echo '<script type="text/javascript">alert("貴方は実験に既に参加済みです。学内メールを確認してください。\nメールが届いていない場合はb9p31013@bunkyo.ac.jpまで連絡を下さい。");</script>';
                        return;
                    }
                    // 学籍番号を登録
                    array_push($accepted_student_number_result, $_POST["your_id"]);

                    // 実験数の選択によって分岐
                    $ratio_value = $_POST["howmany"];
                    switch($ratio_value){
                        case 1:
                            // single_query_resultのlengthが1 -> single_queryに何もない。package_query_resultの最後からunpackして追加する。
                            if(count($single_query_result) == 1){
                                // package_query_resultに、Queryが残っているかチェック
                                if(count($package_query_result) == 1){
                                    echo '<script type="text/javascript">alert("実験の総数が規定を満たした為、現在発行できるURLがありません！\n申し訳ございません。");</script>';
                                    return;
                                }else{
                                    // package_query_resultの最後の要素をunpack
                                    // 今回発行しないほうをsingleに追加
                                    array_push($single_query_result, end($package_query_result)[0]);
                                    $pick_url = end($package_query_result)[1];
                                    // unpackした要素を削除
                                    unset($package_query_result[count($package_query_result)-1]);
                                    $context = "1実験分の実験参加用のURLです。Google Chromeにて以下のURLからアクセスして下さい。\n※発行された分の実験は必ず行うようにしてください。実験時間は各URL毎30分が想定されています。\n※必ず、実験はパソコンで行ってください。スマホやタブレットを用いないでください。\n\n----------------------------\n\n 1: https://soundofhorizon.github.io/ronbun-homepage/";
                                    $context .= $pick_url;
                                    $context .= "-home.html?";
                                    //学籍番号へメール送信
                                    if(isset($_POST["url_assignment"])){
                                       require 'vendor/autoload.php';

                                       $email = new \SendGrid\Mail\Mail();
                                       $email->setFrom("b9p31013@bunkyo.ac.jp", "大柴雅基");
                                       $email->setSubject("実験アクセス用URL記述メール-1実験分");
                                       $to = $_POST["your_id"];
                                       $to .= "@bunkyo.ac.jp";
                                       $email->addTo($to, "参加者様");
                                       $email->addContent("text/plain", $context);
                                       $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
                                       try {
                                           $response = $sendgrid->send($email);
                                       } catch (Exception $e) {
                                           echo 'Caught exception: '. $e->getMessage() ."\n";
                                       }
                                    }
                                    break;
                                }
                            }else{
                                // '""'の要素は削除
                                $single_query_shifted = array_shift($single_query_result);
                                $pick_url = $single_query_result[0];
                                $single_query_shifted = array_shift($single_query_result);
                                array_unshift($single_query_result, "test");
                                $context = "1実験分の実験参加用のURLです。Google Chromeにて以下のURLからアクセスして下さい。\n※発行された分の実験は必ず行うようにしてください。実験時間は各URL毎30分が想定されています。\n※必ず、実験はパソコンで行ってください。スマホやタブレットを用いないでください。\n\n----------------------------\n\n 1: https://soundofhorizon.github.io/ronbun-homepage/";
                                $context .= $pick_url;
                                $context .= "-home.html?";
                                //学籍番号へメール送信
                                if(isset($_POST["url_assignment"])){
                                    require 'vendor/autoload.php';

                                    $email = new \SendGrid\Mail\Mail();
                                    $email->setFrom("b9p31013@bunkyo.ac.jp", "大柴雅基");
                                    $email->setSubject("実験アクセス用URL記述メール-1実験分");
                                    $to = $_POST["your_id"];
                                    $to .= "@bunkyo.ac.jp";
                                    $email->addTo($to, "参加者様");
                                    $email->addContent("text/plain", $context);
                                    $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
                                    try {
                                        $response = $sendgrid->send($email);
                                    } catch (Exception $e) {
                                        echo 'Caught exception: '. $e->getMessage() ."\n";
                                    }
                                }
                                break;
                            }
                        case 2:
                             // 最初の要素を削除
                            if(count($package_query_result) != 1){
                                unset($package_query_result[0]);
                                //要素番号がずれているのでここで整列
                                $package_query_result = array_values($package_query_result);
                                $pick_url = $package_query_result[count($package_query_result)-1];
                                unset($package_query_result[count($package_query_result)-1]);
                                array_unshift($package_query_result, array("first", "endpoint"));
                                $context = "2実験分の実験参加用のURLです。Google Chromeにて以下のURLからアクセスして下さい。\n※発行された分の実験は必ず行うようにしてください。実験時間は各URL毎30分が想定されています。\n※必ず、実験はパソコンで行ってください。スマホやタブレットを用いないでください。\n\n----------------------------\n\n\n -1-\n\n https://soundofhorizon.github.io/ronbun-homepage/";
                                $context .= $pick_url[0];
                                $context .= "-home.html?\n\n-2-\n\n https://soundofhorizon.github.io/ronbun-homepage/";
                                $context .= $pick_url[1];
                                $context .= "-home.html?";

                                //学籍番号へメール送信
                                if(isset($_POST["url_assignment"])){
                                    require 'vendor/autoload.php';

                                    $email = new \SendGrid\Mail\Mail();
                                    $email->setFrom("b9p31013@bunkyo.ac.jp", "大柴雅基");
                                    $email->setSubject("実験アクセス用URL記述メール-2実験分");
                                    $to = $_POST["your_id"];
                                    $to .= "@bunkyo.ac.jp";
                                    $email->addTo($to, "参加者様");
                                    $email->addContent("text/plain", $context);
                                    $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
                                    try {
                                        $response = $sendgrid->send($email);
                                    } catch (Exception $e) {
                                        echo 'Caught exception: '. $e->getMessage() ."\n";
                                    }
                                }
                            }else{
                                echo '<script type="text/javascript">alert("2実験分のセットが無くなりました。\n申し訳ありませんが、1実験分で再度申請してください。");</script>';
                                return;
                            }
                            break;

                    }
                    // UPDATE SQL文の生成
                    $single_update_sql = "";
                    for($i = 0; $i < count($single_query_result); $i++){
                        $single_update_sql .= "'" . $single_query_result[$i] . "',";
                    }
                    $single_update_sql = rtrim($single_update_sql, ",");
                    $single_sql = "UPDATE url_assignment SET Single_query=ARRAY[" . $single_update_sql . "];";

                    $package_update_sql = "";
                    for($i = 0; $i < count($package_query_result); $i++){
                        $package_update_sql .= "ARRAY['" . $package_query_result[$i][0] . "','" . $package_query_result[$i][1] . "'],";
                    }
                    $package_update_sql = rtrim($package_update_sql, ",");
                    $package_sql = "UPDATE url_assignment SET Package_query=ARRAY[" . $package_update_sql . "];";
                    var_dump($accepted_student_number_result);
                    $accepted_student_number_sql = "";
                    for($i = 0; $i < count($accepted_student_number_result); $i++){
                        $accepted_student_number_sql .= "'" . $accepted_student_number_result[$i] . "',";
                    }
                    $accepted_student_number_sql = rtrim($accepted_student_number_sql, ",");
                    $student_number_sql = "UPDATE accepted_student_number SET student_number=ARRAY[" . $accepted_student_number_sql . "];";

                    //各種SQL文を命令
                    $result_flag_single = pg_query($single_sql);
                    if (!$result_flag_single) {
                        die('Single INSERTクエリーが失敗しました。'.pg_last_error());
                    }

                    $result_flag_package = pg_query($package_sql);
                    if (!$result_flag_package) {
                        die('Package INSERTクエリーが失敗しました。'.pg_last_error());
                    }

                    $result_flag_student_number = pg_query($student_number_sql);
                    if (!$result_flag_student_number) {
                        die('Student Number INSERTクエリーが失敗しました。'.pg_last_error());
                    }

                    echo '<script type="text/javascript">alert("' . $_POST["your_id"] . '@bunkyo.ac.jpへメールを送信しました。確認し、実験を進めてください。");</script>';
                    return;
                }
            }
            main();
        ?>
</head>
<body>
    <div style="margin-top: 100px;"></div>
    <center>
        <p>取り組んでいただく教材へのURLを発行するためのプログラムです。</p>
        <p>選択した数のURLが発行され、そのURLが記載されたメールが、記載された学籍番号のメールに送信されます。</p>
        <p>メールに添付されたURLを開き、その後の作業を進めてください。</p>
        <p>実験は2つまで受けることが可能です。</p>
        <p><font color="red">ただし、実験数を後から変更することはできません。</font></p>
        <p>※実験期間が終了すると、発行されたURLは無効になります。</p>
        <hr>
        <br>
        <form action="index.php" method="post">
            <p>学籍番号を入力してください。</p>
            <input type="text" id="myText" name="your_id" placeholder="学籍番号を入力してください。"><br>
            <hr>
            <p>確認の為、再度学籍番号を入力してください。</p>
            <input type="text" id="myText2" name="your_id_confirm" placeholder="再度、学籍番号を入力してください。"><br>
            <hr>
            <p>取り組むことができる実験数を選択してください。</p>
            <br><br>
            <label><input type="radio" name="howmany" value="1" id="onetime">1実験</label>
            <label><input type="radio" name="howmany" value="2" id="twotime">2実験</label>
            <input type="submit" id="url_assignment_button" name="url_assignment" onclick="URLassignment()" style="display: none;" value="URL発行" />
        </form>
        <p id="check_frag"></p>
    </center>

    <script type="text/javascript">
        var frag = false;
        let frag_check = document.getElementById('check_frag');
        function putRatio(){
            document.getElementById("check_frag").style.display = "block";
            let elements = document.getElementsByName('howmany');
            if (elements.item(0).checked){
                if(frag){
                    document.getElementById("url_assignment_button").style.display ="block";
                    document.getElementById("check_frag").style.display = "none";
                }else{
                    frag_check.innerText = "学籍番号が正しく入力されていません。確認の上再度送信してください。"
                }
            }else if(elements.item(1).checked){
                if(frag){
                    document.getElementById("url_assignment_button").style.display ="block";
                    document.getElementById("check_frag").style.display = "none";
                }else{
                    frag_check.innerText = "学籍番号が正しく入力されていません。確認の上再度送信してください。"
                }
            }
        }
        function buttonfrag_check(){
            document.getElementById("check_frag").style.display = "none";
            if(nameText_1.value==nameText_2.value){
                frag = true;
            }
        }
        let elements1 = document.getElementById('onetime');
        let elements2 = document.getElementById('twotime');
        elements1.addEventListener('change', putRatio);
        elements2.addEventListener('change', putRatio);
        let nameText_1 = document.getElementById('myText');
        let nameText_2 = document.getElementById('myText2');
        nameText_1.oninput = buttonfrag_check;
        nameText_2.oninput = buttonfrag_check;
    </script>
</body>
</html>
