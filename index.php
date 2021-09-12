<?php
        $conn = pg_connect(getenv("DATABASE_URL"));
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
    <label><input type="radio" name="howmany" value="1" id="onetime">1単位</label>
    <label><input type="radio" name="howmany" value="2" id="twotime">2単位</label>
    <br><br>
    <button id="url_assignment_button" onclick="URLassignment()" style="display: none;">URL発行</button>
    <p id="result"></p>

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

           var package_query = "<?php
                                     $package_query = pg_query($conn, 'select Package_query from url_assignment;');
                                     while ($row = pg_fetch_row($package_query)) {
                                       echo $row[0];
                                     }
                                ?>";

           var single_query = '<?php
                                     $single_query = pg_query($conn, "select Single_query from url_assignment;");
                                     while ($row = pg_fetch_row($single_query)) {
                                       echo $row[0];
                                     }
                                ?>';

           package_query = package_query.replace("{{", "").replace("}}", "").split("},{");
           single_query = single_query.replace("{", "").replace("}", "").split(",");

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

            // Update DATABASE Query
            $.ajax({
                type: "POST", //　GETでも可
                url: "update.php", //　送り先
                data: { 'package_sql': package_sql, 'single_sql': single_sql }, //　渡したいデータをオブジェクトで渡す
                dataType : "json", //　データ形式を指定
                scriptCharset: 'utf-8' //　文字コードを指定
            })
            .then(
                function(param){　 //　paramに処理後のデータが入って戻ってくる
                    console.log(param); //　帰ってきたら実行する処理
                },
                function(XMLHttpRequest, textStatus, errorThrown){ //　エラーが起きた時はこちらが実行される
                    console.log(XMLHttpRequest); //　エラー内容表示
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

        function sleep(waitMsec) {
          var startMsec = new Date();

          // 指定ミリ秒間だけループさせる（CPUは常にビジー状態）
          while (new Date() - startMsec < waitMsec);
        }
    </script>
</body>
</html>