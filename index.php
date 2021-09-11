<?php
        $conn = pg_connect(getenv("DATABASE_URL"));
        $package_query = pg_query($conn, "select package_query from url_assainment;");
        $single_query = pg_query($conn, "select single_query from url_assainment;");
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
    <p>同時に最大2単位まで受けることが可能です。</p>
    <hr>
    <br>
    <p>取り組むことができる単位数を選択してください。</p>
    <label><input type="radio" name="howmany" value="1" id="onetime">1単位</label>
    <label><input type="radio" name="howmany" value="2" id="twotime">2単位</label>
    <hr id="border_check_1" style="display: none;">
    <p id="warn_check_1" style="display: none;">※実験の参加が2回目の場合は、1回目の動画がどの様な内容だったかを入力してください。
    <label id="whattyperatio1" style="display: none;"><input type="radio" name="whattype" value="1">来客の応対</label>
    <label id="whattyperatio2" style="display: none;"><input type="radio" name="whattype" value="2">挨拶</label>
    <label id="whattyperatio3" style="display: none;"><input type="radio" name="whattype" value="3">電話のかけ方</label>
    <label id="whattyperatio4" style="display: none;"><input type="radio" name="whattype" value="4">敬語</label>
    <br><br>
    <button onclick="URLAssainment()" style="display: block;">URL発行</button>
    <p id="result"></p>

    <script type="text/javascript">
        function putRatio(){
            let elements = document.getElementsByName('howmany');
            if (elements.item(0).checked){
                document.getElementById("border_check_1").style.display ="block";
                document.getElementById("whattyperatio1").style.display ="block";
                document.getElementById("whattyperatio2").style.display ="block";
                document.getElementById("whattyperatio3").style.display ="block";
                document.getElementById("whattyperatio4").style.display ="block";
                document.getElementById("warn_check_1").style.display ="block";
            }else if(elements.item(1).checked){
                document.getElementById("border_check_1").style.display ="none";
                document.getElementById("whattyperatio1").style.display ="none";
                document.getElementById("whattyperatio2").style.display ="none";
                document.getElementById("whattyperatio3").style.display ="none";
                document.getElementById("whattyperatio4").style.display ="none";
                document.getElementById("warn_check_1").style.display ="none";
            }
        }
        let elements1 = document.getElementById('onetime');
        let elements2 = document.getElementById('twotime');
        elements1.addEventListener('change', putRatio);
        elements2.addEventListener('change', putRatio);

        function URLAssainment(){
           var package_query = "<?php while ($row = pg_fetch_row($package_query)) {
                                       echo $row[0];
                                     }
                                ?>"

           var single_query = '<?php while ($row = pg_fetch_row($single_query)) {
                                       echo $row[0];
                                     }
                                ?>'

           package_query = package_query.replace("{{", "").replace("}}", "").split("},{");
           single_query = single_query.replace("{", "").replace("}", "").split(",");

           let howmanyunit = document.getElementsByName('howmany');
            if (elements.item(0).checked){
                document.getElementById("border_check_1").style.display ="block";
                document.getElementById("whattyperatio1").style.display ="block";
                document.getElementById("whattyperatio2").style.display ="block";
                document.getElementById("whattyperatio3").style.display ="block";
                document.getElementById("whattyperatio4").style.display ="block";
                document.getElementById("warn_check_1").style.display ="block";
            }else if(elements.item(1).checked){
                var index = Math.floor(Math.random() * package_query.length);
                var pick_url = package_query[index];
                var context = "https://soundofhorizon.github.io/ronbun-homepage/"+pick_url[0]+"-home.html? \n https://soundofhorizon.github.io/ronbun-homepage/"+pick_url[1]+"-home.html?"
                downloadAsTextFile("実験アクセス用URL記述ファイル", context);
            }
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
    </script>
</body>
</html>