import os

import psycopg2

SQLpath = os.environ["DATABASE_URL"]
db = psycopg2.connect(SQLpath)  # sqlに接続
cur = db.cursor()  # なんか操作する時に使うやつ


def update_sql_execute(single_sql, package_sql):
    cur.execute(f"{single_sql}")
    db.commit()
    cur.execute(f"{package_sql}")
    db.commit()
    print("Success")
