<?php
        if(isset($_POST['howmany'])) {
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

            //ここで取得したQueryはString表記なのでArrayにする。
            $package_query_result = explode("},{",substr($package_query_result, 2, strlen($package_query_result)-4));
            //Packageについては、各要素がArrayであってほしいので、更に各要素をexplode.
            for($i = 0; $i < count($package_query_result); $i++){
                $package_query_result[$i] = explode("," , $package_query_result[$i]);
            }

            $single_query_result = explode("," , substr($single_query_result, 1, strlen($single_query_result)-2));

            // 単位数の選択によって分岐
            $ratio_value = $_POST["howmany"];
            switch($ratio_value){
                case 1:
                    // single_query_resultのlengthが1 -> single_queryに何もない。package_query_resultの最後からunpackして追加する。
                    if(count($single_query_result) == 1){
                        // package_query_resultに、Queryが残っているかチェック
                        if(in_array("first", end($package_query_result))){
                            $alert = "<script type='text/javascript'>alert('実験の総数が規定を満たした為、現在発行できるURLがありません！申し訳ございません。');</script>";
                            echo $alert;
                        }else{
                            // package_query_resultの最後の要素をunpack
                            // 今回発行しないほうをsingleに追加
                            array_push($single_query_result, end($package_query_result)[0]);
                            $pick_url = end($package_query_result)[1];
                            $context = "1単位分の実験参加用のURLです。以下のURLをコピーし、Google Chromeにてアクセスして下さい。\n※発行された分の実験は必ず行うようにしてください。実験時間は各URL毎30分が想定されています。\n\n----------------------------\n\n 1: https://soundofhorizon.github.io/ronbun-homepage/"+$pick_url+"-home.html?";
                            //ファイル出力
                            $fileName = "実験アクセス用URL記述ファイル-1単位.txt";
                            header('Content-Type: text/plain');
                            header('Content-Disposition: attachment; filename='.$fileName);
                            echo mb_convert_encoding($context, "SJIS", "UTF-8");  //←UTF-8のままで良ければ不要です。
                        }
                    }else{
                        echo "到達してます2";
                        break;
                    }
                case 2:
                    echo "到達してます3";
                    break;
            }
        }
    ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>URL Assignment System</title>
</head>
<body>
    <p>取り組んでいただく教材へのURLを発行するためのプログラムです。</p>
    <p>選択した数のURLが発行され、そのURLが記載されたファイルがpdf形式でダウンロードされます。</p>
    <p>ダウンロードされたファイルを開き、その後の作業を進めてください。</p>
    <p>2単位まで受けることが可能です。1単位を選択した場合、さらに1単位を受けることはできません。</p>
    <hr>
    <br>
    <p>学籍番号を入力してください。</p>
    <input type="text" id="myText"><br>
    <hr>
    <p>取り組むことができる単位数を選択してください。</p>
    <br><br>
    <form action="index.php" method="post">
        <label><input type="radio" name="howmany" value="1" id="onetime">1単位</label>
        <label><input type="radio" name="howmany" value="2" id="twotime">2単位</label>
        <input type="submit" id="url_assignment_button" name="url_assignment" onclick="URLassignment()" style="display: none;" value="URL発行" />
    </form>
    <p id="result"><?php $pick_url ?></p>

    <script type="text/javascript">
        var frag = true;
        function putRatio(){
            let elements = document.getElementsByName('howmany');
            if (elements.item(0).checked && frag){
                document.getElementById("url_assignment_button").style.display ="block";
            }else if(elements.item(1).checked && frag){
                document.getElementById("url_assignment_button").style.display ="block";
            }
        }
        let elements1 = document.getElementById('onetime');
        let elements2 = document.getElementById('twotime');
        elements1.addEventListener('change', putRatio);
        elements2.addEventListener('change', putRatio);

        function URLassignment(){

            if (document.getElementById('myText').value == "" || document.getElementById('myText').value == null)  {
                alert("学籍番号が入力されていません。入力した後再度URL発行をお試しください。");
                return
            }

        }
        /*


           let howmanyunit = document.getElementsByName('howmany');
            if (howmanyunit.item(0).checked){
                if(single_query.length == 1){
                    // unpack
                    if(package_query.slice(-1)[0] != "first,endpoint"){
                        single_query.push(package_query.slice(-1)[0].split(",")[0]);
                        var pick_url = package_query.slice(-1)[0].split(",")[1];
                        var context = "1単位分の実験参加用のURLです。以下のURLをコピーし、Google Chromeにてアクセスして下さい。\n※発行された分の実験は必ず行うようにしてください。実験時間は各URL毎30分が想定されています。\n\n----------------------------\n\n 1: https://soundofhorizon.github.io/ronbun-homepage/"+pick_url+"-home.html?";
                        package_query.pop();
                        downloadAsTextFile("実験アクセス用URL記述ファイル-1単位", context);
                    }else{
                        alert("実験の総数が規定を満たした為、現在発行できるURLがありません！申し訳ございません。");
                    }
                }else{
                    var index = Math.floor(Math.random() * single_query.length);
                    var pick_url = single_query[index];
                    var context = "1単位分の実験参加用のURLです。以下のURLをコピーし、Google Chromeにてアクセスして下さい。\n※発行された分の実験は必ず行うようにしてください。必要な実験環境については事前に配布されているマニュアルを参照してください。\n実験時間は各URL毎30分が想定されています。\n\n----------------------------\n\n 1: https://soundofhorizon.github.io/ronbun-homepage/"+pick_url+"-home.html?";
                    single_query.splice(index, 1);
                    downloadAsTextFile("実験アクセス用URL記述ファイル-1単位", context);
                }
            }else if(howmanyunit.item(1).checked){
                if(package_query.slice(-1)[0] != "first,endpoint"){
                    var index = Math.floor(Math.random() * package_query.length);
                    var pick_url = package_query[index].split(",");
                    var context = "2単位分の実験参加用のURLです。以下のURLをそれぞれコピーし、Google Chromeにてアクセスして下さい。\n※発行された分の実験は必ず行うようにしてください。必要な実験環境については事前に配布されているマニュアルを参照してください。\n実験時間は各URL毎30分が想定されています。\n\n----------------------------\n\n 1: https://soundofhorizon.github.io/ronbun-homepage/"+pick_url[0]+"-home.html? \n\n 2: https://soundofhorizon.github.io/ronbun-homepage/"+pick_url[1]+"-home.html?";
                    package_query.splice(index, 1);
                    downloadAsTextFile("実験アクセス用URL記述ファイル-2単位", context);
                }else{
                   alert("実験の残り数が2単位に満たないため、2単位分のURLが発行できません！1単位で再度お試しください。");
                }
            }

            document.getElementById("url_assignment_button").style.display ="none";
            document.getElementById('result').innerHTML = "<p>URLを発行し、テキストファイルとしてダウンロードしました。\n マニュアルとテキストファイルを参考して実験を進めてください。</p>";

            // Update DATABASE Single
            // SQLのtextに対応するためにシングルクォーテーションで要素を全部囲む
            for(let i=0; i<single_query.length; i++){
                single_query[i] = "'" + single_query[i] + "'";
            }
            var single_sql = "UPDATE url_assignment SET Single_query=ARRAY[" + single_query + "];";

            // Update DATABASE Package
            for(let i=0; i<package_query.length; i++){
                package_query[i] = package_query[i].split(",");
                package_query[i] = "ARRAY['" + package_query[i][0] + "','" + package_query[i][1] + "']";
            }
            var package_sql = "UPDATE url_assignment SET Package_query=ARRAY[" + package_query + "];"

            var exec = require('child_process').exec;
            exec('python -c "import update; update.update_sql_execute()', function(err, stdout, stderr){
              if (err) { console.log(err); }
            });

            frag = false;
        }

        function downloadAsTextFile( fileName, content ) {
            const BLOB              = new Blob( [ content ], { 'type': 'text/plain' } );
            const CAN_USE_SAVE_BLOB = window.navigator.msSaveBlob !== undefined;

            if ( CAN_USE_SAVE_BLOB ) {
                window.navigator.msSaveBlob( BLOB, fileName );
                return;
            }

            const TEMP_ANCHOR   = document.createElement( 'a' );
            TEMP_ANCHOR.href    = URL.createObjectURL( BLOB );
            TEMP_ANCHOR.setAttribute( 'download', fileName );

            TEMP_ANCHOR.dispatchEvent( new MouseEvent( 'click' ) );
        };
        */
    </script>
</body>
</html>