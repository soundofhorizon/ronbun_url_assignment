const { Client } = require('pg');

const client = new Client({
  connectionString: process.env.DATABASE_URL,
  ssl: true,
});

client.connect();

$("#url_assignment_button").on("click", function(){
            if (document.getElementById('myText').value == "" || document.getElementById('myText').value == null)  {
                alert("学籍番号が入力されていません。入力した後再度URL発行をお試しください。");
                return
            }

            client.query('select Package_query from url_assignment;', (err, res) => {
              if (err) throw err;
              for (let row of res.rows) {
                console.log(JSON.stringify(row));
              }
              client.end();
            });

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

            var exec = require('child_process').exec;
            exec('python -c "import update; update.update_sql_execute()', function(err, stdout, stderr){
              if (err) { console.log(err); }
            });

            frag = false;
)};

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