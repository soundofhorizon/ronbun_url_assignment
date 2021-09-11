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
    <label><input type="radio" name="howmany" value="1" id="onetime">1単位</label>
    <label><input type="radio" name="howmany" value="2">2単位</label>
    <hr id="border_check_1" style="display: none;">
    <label><input type="radio" name="whattype" value="A" style="display: none;">A</label>
    <label><input type="radio" name="whattype" value="B" style="display: none;">B</label>
    <label><input type="radio" name="whattype" value="C" style="display: none;">C</label>
    <label><input type="radio" name="whattype" value="D" style="display: none;">D</label>
    <br><br>
    <button>URL発行</button>

    <?php
        $conn = pg_connect(getenv("DATABASE_URL"));
        $result = pg_query($conn, "select * from url_assainment;");
        var_dump(pg_fetch_all($result));
    ?>

    <script type="text/javascript">
        function putRatio(){
            let elements = document.getElementsByName('howmany');
            if (elements.item(0).checked){
                document.getElementsById("border_check_1").style.display ="block";
                document.getElementsByName('whattype').style.display ="block";
            }else{
                document.getElementsById("border_check_1").style.display ="none";
                document.getElementsByName('whattype').style.display ="none";
            }
        }
        let elements1 = document.getElementsById('onetime');
        elements1.addEventListener('change', putRatio);
    </script>
</body>
</html>