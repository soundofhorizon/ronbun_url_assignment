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
    <label><input type="radio" name="howmany" value="2" id="twotime">2単位</label>
    <hr id="border_check_1" style="display: none;">
    <label id="whattyperatio1" style="display: none;"><input type="radio" name="whattype" value="A">A</label>
    <label id="whattyperatio2" style="display: none;"><input type="radio" name="whattype" value="B">B</label>
    <label id="whattyperatio3" style="display: none;"><input type="radio" name="whattype" value="C">C</label>
    <label id="whattyperatio4" style="display: none;"><input type="radio" name="whattype" value="D">D</label>
    <br><br>
    <button onclick="URLAssainment()">URL発行</button>
    <p id="result"></p>

    <?php
        $conn = pg_connect(getenv("DATABASE_URL"));
        $package_query = pg_query($conn, "select package_query from url_assainment;");
        $single_query = pg_query($conn, "select single_query from url_assainment;");
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

        function URLAssainment(){
            let package_query = <?php echo $package_query;?>

            let single_query = <?php echo $single_query;?>

            document.getElementById('result').innerHTML = package_query;
        }
    </script>
</body>
</html>