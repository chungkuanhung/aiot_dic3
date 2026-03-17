from flask import Flask, request
import sqlite3
import os

app = Flask(__name__)
DB_FILE = 'aiotdb.db'

def init_db():
    """初始化資料庫並建立資料表（如果不存在的話）"""
    with sqlite3.connect(DB_FILE) as conn:
        cursor = conn.cursor()
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS sensors (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                temp FLOAT DEFAULT 0,
                humid FLOAT DEFAULT 0,
                time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        conn.commit()

# 在應用程式啟動時初始化資料庫
init_db()

@app.route('/addData', methods=['GET'])
def add_data():
    try:
        # 從 GET 請求取得參數，並轉換為浮點數
        temp = request.args.get('temp', type=float)
        humid = request.args.get('humid', type=float)

        # 檢查參數是否存在且有效
        if temp is not None and humid is not None:
            with sqlite3.connect(DB_FILE) as conn:
                cursor = conn.cursor()
                cursor.execute(
                    "INSERT INTO sensors (temp, humid) VALUES (?, ?)",
                    (temp, humid)
                )
                conn.commit()
            return "成功！資料已寫入資料庫。", 200
        else:
            return "錯誤：未提供正確的 temp 或 humid 參數。", 400
    except Exception as e:
        return f"寫入失敗或發生錯誤: {str(e)}", 500

if __name__ == '__main__':
    # 執行 Flask 應用程式，監聽 5000 port
    app.run(host='0.0.0.0', port=5000, debug=True)
