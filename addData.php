<?php
// 資料庫連線設定
$host = 'localhost';
$db   = 'aiotdb';
$user = 'test123'; // 請更換為你的資料庫帳號
$pass = 'test123';     // 請更換為你的資料庫密碼
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 1. 從 GET 請求取得資料
    // 使用 filter_input 確保取得的是數字，增加安全性
    $temp = filter_input(INPUT_GET, 'temp', FILTER_VALIDATE_FLOAT);
    $humid = filter_input(INPUT_GET, 'humid', FILTER_VALIDATE_FLOAT);

    // 2. 檢查資料是否存在且有效
    if ($temp !== null && $humid !== null && $temp !== false && $humid !== false) {
        
        // 3. 準備 SQL 指令 (使用預處理語句防止 SQL 注入)
        $sql = "INSERT INTO sensors (temp, humid) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$temp, $humid])) {
            echo "成功！資料已寫入資料庫。";
        } else {
            echo "失敗：無法寫入資料。";
        }
        
    } else {
        echo "錯誤：未提供正確的 temp 或 humid 參數。";
    }

} catch (\PDOException $e) {
    // 實際生產環境建議將錯誤紀錄在 Log，不要直接 print 出來
    die("資料庫連線失敗: " . $e->getMessage());
}
?>