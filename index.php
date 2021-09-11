<!DOCTYPE html>
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
    <form action="radio.php" method="POST">
        <label><input type="radio" name="howmany" value="1" id="onetime">1単位</label>
        <label><input type="radio" name="howmany" value="2" id="twotime">2単位</label>
        <hr id="border_check_1" style="display: none;">
        <label id="whattyperatio1" style="display: none;"><input type="radio" name="whattype" value="A">A</label>
        <label id="whattyperatio2" style="display: none;"><input type="radio" name="whattype" value="B">B</label>
        <label id="whattyperatio3" style="display: none;"><input type="radio" name="whattype" value="C">C</label>
        <label id="whattyperatio4" style="display: none;"><input type="radio" name="whattype" value="D">D</label>
        <br><br>
        <button>URL発行</button>
    </form>

    <?php
        $conn = pg_connect(getenv("DATABASE_URL"));
        $result = pg_query($conn, "select * from url_assainment;");

        $target_html = file_get_contents('https://urlassainment.herokuapp.com/');
        $target_html = mb_convert_encoding($target_html, 'HTML-ENTITIES', 'UTF-8');

        $dom = new DOMDocument;
        @$dom->loadHTML($target_html);
        $xml_object = simplexml_import_dom($dom);

        (string)$xml_object->body->input[0]->attributes()->value;
    ?>

    <script type="text/javascript">
        function putRatio(){
            let elements = document.getElementsByName('howmany');
            if (elements.item(0).checked){
                document.getElementById("border_check_1").style.display ="block";
                document.getElementById("whattyperatio1").style.display ="block";
                document.getElementById("whattyperatio2").style.display ="block";
                document.getElementById("whattyperatio3").style.display ="block";
                document.getElementById("whattyperatio4").style.display ="block";
            }else if(elements.item(1).checked){
                document.getElementById("border_check_1").style.display ="none";
                document.getElementById("whattyperatio1").style.display ="none";
                document.getElementById("whattyperatio2").style.display ="none";
                document.getElementById("whattyperatio3").style.display ="none";
                document.getElementById("whattyperatio4").style.display ="none";
            }
        }
        let elements1 = document.getElementById('onetime');
        let elements2 = document.getElementById('twotime');
        elements1.addEventListener('change', putRatio);
        elements2.addEventListener('change', putRatio);
    </script>
</body>
</html>