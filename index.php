<?php
        $conn = pg_connect(getenv("DATABASE_URL"));
 ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>URL Assignment System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="/store.legacy.min.js"></script>
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
    <button id="url_assignment_button" style="display: none;">URL発行</button>
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
    </script>
    <script type="text/javascript" src="main.js"></script>
</body>
</html>