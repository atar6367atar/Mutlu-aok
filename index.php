<?php
session_start();

// =============================================
// NGB SORGU PANELİ - PROXY SİSTEMLİ
// TÜM SORGULAR ÇALIŞIYOR - HATASIZ
// Şifre: @ngbsorguata44 (Admin)
// =============================================

define('BOT_TOKEN', '8588404115:AAG7BD9FebTCIy-3VR7h4byCidwDcrIZXWw');
define('CHAT_ID', '8444268448');
define('API_BASE', 'https://punisherapi.alwaysdata.net');

// Veritabanı bağlantısı
try {
    $db = new PDO('sqlite:users.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Kullanıcılar tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        password TEXT,
        email TEXT,
        fullname TEXT,
        role TEXT DEFAULT 'user',
        status TEXT DEFAULT 'active',
        created_at DATETIME,
        last_login DATETIME,
        last_ip TEXT,
        total_queries INTEGER DEFAULT 0,
        api_calls INTEGER DEFAULT 0,
        notes TEXT,
        banned_until DATETIME,
        ban_reason TEXT
    )");
    
    // Sorgu logları tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS query_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        username TEXT,
        query_type TEXT,
        query_param TEXT,
        result TEXT,
        ip TEXT,
        created_at DATETIME,
        status TEXT
    )");
    
    // Varsayılan admin kullanıcısı
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $admin_pass = password_hash('@ngbsorguata44', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password, fullname, role, created_at) VALUES (?, ?, ?, 'admin', datetime('now'))");
        $stmt->execute(['admin', $admin_pass, 'Admin User']);
    }
    
} catch(PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

// Telegram log gönderme
function sendTelegramLog($message) {
    @file_get_contents("https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage?chat_id=" . CHAT_ID . "&text=" . urlencode($message) . "&parse_mode=Markdown");
}

// Login işlemi
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        $stmt = $db->prepare("UPDATE users SET last_login = datetime('now'), last_ip = ? WHERE id = ?");
        $stmt->execute([$ip, $user['id']]);
        
        $mesaj = "🔐 *YENİ GİRİŞ*\n\n";
        $mesaj .= "👤 *Kullanıcı:* {$user['username']}\n";
        $mesaj .= "🌐 *IP:* `$ip`\n";
        $mesaj .= "🕒 *Tarih:* " . date('Y-m-d H:i:s');
        sendTelegramLog($mesaj);
        
        header('Location: index.php');
        exit;
    } else {
        $hata = "Hatalı kullanıcı adı veya şifre!";
    }
}

// Register işlemi
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $kayit_hata = "Bu kullanıcı adı zaten kullanılıyor!";
    } else {
        $stmt = $db->prepare("INSERT INTO users (username, password, email, fullname, role, created_at, last_ip) VALUES (?, ?, ?, ?, 'user', datetime('now'), ?)");
        $stmt->execute([$username, $password, $email, $fullname, $ip]);
        
        $mesaj = "✅ *YENİ KULLANICI KAYDI*\n\n";
        $mesaj .= "👤 *Kullanıcı:* $username\n";
        $mesaj .= "📧 *Email:* $email\n";
        $mesaj .= "🌐 *IP:* `$ip`\n";
        sendTelegramLog($mesaj);
        
        $kayit_basarili = "Kayıt başarılı! Giriş yapabilirsiniz.";
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Kullanıcı bilgilerini al
$kullanici = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $kullanici = $stmt->fetch(PDO::FETCH_ASSOC]);
}

// Admin kontrolü
$isAdmin = ($kullanici && $kullanici['role'] == 'admin');

// Sorgu proxy API
if (isset($_GET['api_query'])) {
    header('Content-Type: application/json');
    
    $type = $_GET['type'];
    $param = $_GET['param'];
    
    // API endpoint'leri
    $apis = [
        'tc1' => ['url' => '/apiservices/tc.php', 'params' => ['tc']],
        'tc2' => ['url' => '/apiservices/tcpro.php', 'params' => ['tc']],
        'isim' => ['url' => '/apiservices/adsoyad.php', 'params' => ['ad', 'soyad']],
        'isim_pro' => ['url' => '/apiservices/adsoyadpro.php', 'params' => ['ad', 'soyad', 'il']],
        'isim_il' => ['url' => '/apiservices/adililce.php', 'params' => ['ad', 'il', 'ilce']],
        'aile' => ['url' => '/apiservices/aile.php', 'params' => ['tc']],
        'aile_pro' => ['url' => '/apiservices/ailepro.php', 'params' => ['tc']],
        'sulale' => ['url' => '/apiservices/sulale.php', 'params' => ['tc']],
        'adres' => ['url' => '/apiservices/adres.php', 'params' => ['tc']],
        'adres_pro' => ['url' => '/apiservices/adrespro.php', 'params' => ['tc']],
        'isyeri' => ['url' => '/apiservices/isyeri.php', 'params' => ['tc']],
        'isyeri_ark' => ['url' => '/apiservices/isyeriark.php', 'params' => ['tc']],
        'gncloperator' => ['url' => '/apiservices/gncloperator.php', 'params' => ['numara']],
        'tcgsm' => ['url' => '/apiservices/tcgsm.php', 'params' => ['tc']],
        'gsmtc' => ['url' => '/apiservices/gsmtc.php', 'params' => ['gsm']],
        'iban' => ['url' => '/apiservices/iban.php', 'params' => ['iban']]
    ];
    
    if (!isset($apis[$type])) {
        echo json_encode(['success' => false, 'error' => 'Geçersiz sorgu tipi']);
        exit;
    }
    
    $api = $apis[$type];
    $params = explode('|', $param);
    
    if (count($params) < count($api['params'])) {
        echo json_encode(['success' => false, 'error' => 'Eksik parametre']);
        exit;
    }
    
    // API isteği yap
    $url = API_BASE . $api['url'] . '?';
    $query_params = [];
    for ($i = 0; $i < count($api['params']); $i++) {
        $query_params[$api['params'][$i]] = $params[$i];
    }
    $url .= http_build_query($query_params);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200 && $response) {
        // Veriyi temizle
        $data = json_decode($response, true);
        if ($data) {
            // Reklamları temizle
            $cleanData = cleanApiData($data);
            echo json_encode(['success' => true, 'data' => $cleanData]);
        } else {
            echo json_encode(['success' => true, 'data' => $response]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'API yanıt vermiyor (HTTP ' . $http_code . ')']);
    }
    exit;
}

// Veri temizleme fonksiyonu
function cleanApiData($data) {
    if (is_array($data)) {
        $cleaned = [];
        foreach ($data as $key => $value) {
            // Reklam içeren anahtarları atla
            if (preg_match('/developer|geliştirici|version|sürüm|reklam|kanal|telegram|punisher|admin|destek/i', $key)) {
                continue;
            }
            
            if (is_array($value)) {
                $cleanedValue = cleanApiData($value);
                if (!empty($cleanedValue)) {
                    $cleaned[$key] = $cleanedValue;
                }
            } else if (is_string($value)) {
                // Metin içindeki reklamları temizle
                $value = preg_replace('/@\w+/', '', $value);
                $value = preg_replace('/t\.me\/\w+/', '', $value);
                $value = preg_replace('/https?:\/\/\S+/', '', $value);
                $value = preg_replace('/punishe\w+/i', '', $value);
                $value = trim($value);
                if (!empty($value)) {
                    $cleaned[$key] = $value;
                }
            } else {
                $cleaned[$key] = $value;
            }
        }
        return $cleaned;
    }
    return $data;
}

// Sorgu kaydı
if (isset($_POST['sorgu_kaydet']) && $kullanici) {
    $sorgu_tipi = $_POST['sorgu_tipi'];
    $sorgu_parametre = $_POST['sorgu_parametre'];
    $sonuc = $_POST['sonuc'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $db->prepare("INSERT INTO query_logs (user_id, username, query_type, query_param, result, ip, created_at, status) VALUES (?, ?, ?, ?, ?, ?, datetime('now'), 'success')");
    $stmt->execute([$kullanici['id'], $kullanici['username'], $sorgu_tipi, $sorgu_parametre, $sonuc, $ip]);
    
    $stmt = $db->prepare("UPDATE users SET total_queries = total_queries + 1, api_calls = api_calls + 1 WHERE id = ?");
    $stmt->execute([$kullanici['id']]);
    
    echo json_encode(['success' => true]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGB SORGU PANELİ | TÜM SORGULAR ÇALIŞIYOR</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #8b5cf6;
            --primary-dark: #2e1065;
            --primary-darker: #1a0b2e;
            --primary-light: #c4b5fd;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background: linear-gradient(135deg, #000000, var(--primary-darker), var(--primary-dark));
            min-height: 100vh;
            position: relative;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        @keyframes glow {
            0%, 100% { filter: drop-shadow(0 0 5px var(--primary)); }
            50% { filter: drop-shadow(0 0 25px var(--primary-light)); }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Floating Cards */
        .floating-card {
            position: fixed;
            font-size: 50px;
            color: var(--primary);
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
            animation: float 20s ease-in-out infinite;
        }

        .card1 { top: 10%; left: 5%; }
        .card2 { top: 20%; right: 10%; animation-delay: 5s; }
        .card3 { bottom: 15%; left: 15%; animation-delay: 10s; }
        .card4 { bottom: 25%; right: 20%; animation-delay: 15s; }

        /* Particles */
        .particle {
            position: fixed;
            width: 2px;
            height: 2px;
            background: var(--primary-light);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.2;
            animation: particle 15s linear infinite;
        }

        @keyframes particle {
            0% { transform: translateY(100vh) translateX(0); opacity: 0; }
            10% { opacity: 0.3; }
            90% { opacity: 0.3; }
            100% { transform: translateY(-100vh) translateX(100px); opacity: 0; }
        }

        /* Auth Container */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        .auth-card {
            background: rgba(26, 11, 46, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            padding: 50px 40px;
            width: 100%;
            max-width: 450px;
            border: 2px solid var(--primary);
            box-shadow: 0 0 50px var(--primary);
            animation: glow 3s ease-in-out infinite;
        }

        .auth-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(139, 92, 246, 0.3);
            padding-bottom: 20px;
        }

        .auth-tab {
            flex: 1;
            padding: 12px;
            background: transparent;
            border: 2px solid var(--primary);
            border-radius: 30px;
            color: white;
            font-family: 'Orbitron', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .auth-tab.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-color: transparent;
        }

        .auth-form {
            display: none;
        }

        .auth-form.active {
            display: block;
            animation: slideIn 0.5s ease-out;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo i {
            font-size: 60px;
            color: var(--primary-light);
            animation: pulse 2s ease-in-out infinite;
        }

        .auth-logo h1 {
            font-size: 28px;
            color: white;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--primary);
            border-radius: 30px;
            color: white;
            font-size: 14px;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 30px var(--primary);
        }

        .auth-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s;
        }

        .auth-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(139, 92, 246, 0.4);
        }

        .alert {
            padding: 12px;
            border-radius: 20px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 13px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid var(--danger);
            color: white;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid var(--success);
            color: white;
        }

        /* Dashboard */
        .dashboard {
            padding: 30px;
            position: relative;
            z-index: 10;
        }

        .header {
            background: rgba(26, 11, 46, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 20px 30px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid var(--primary);
            box-shadow: 0 0 30px var(--primary);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .user-details h3 {
            color: white;
            font-size: 18px;
        }

        .user-details p {
            color: var(--primary-light);
            font-size: 12px;
        }

        .badge {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            padding: 8px 20px;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.2);
            color: white;
            border: 1px solid var(--danger);
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 13px;
        }

        .logout-btn:hover {
            background: var(--danger);
            transform: translateY(-2px);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: rgba(26, 11, 46, 0.95);
            border-radius: 25px;
            padding: 20px;
            border: 2px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-info h3 {
            color: white;
            font-size: 14px;
            opacity: 0.8;
        }

        .stat-info p {
            color: var(--primary-light);
            font-size: 24px;
            font-weight: 700;
        }

        /* Query Grid */
        .query-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .query-card {
            background: rgba(26, 11, 46, 0.95);
            border-radius: 20px;
            padding: 15px;
            border: 2px solid var(--primary);
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .query-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-light);
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);
        }

        .query-card i {
            font-size: 30px;
            color: var(--primary-light);
            margin-bottom: 10px;
        }

        .query-card h3 {
            color: white;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .query-card p {
            color: rgba(255,255,255,0.5);
            font-size: 11px;
        }

        /* Query Box */
        .query-box {
            background: rgba(26, 11, 46, 0.98);
            border-radius: 30px;
            padding: 30px;
            margin-bottom: 25px;
            border: 2px solid var(--primary);
        }

        .query-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary);
        }

        .query-header i {
            font-size: 40px;
            color: var(--primary-light);
        }

        .query-header h2 {
            color: white;
            font-size: 20px;
        }

        .query-input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .query-input-group input {
            flex: 1;
            padding: 15px 20px;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--primary);
            border-radius: 25px;
            color: white;
            font-family: 'Orbitron', sans-serif;
        }

        .query-input-group input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 20px var(--primary);
        }

        .query-input-group button {
            padding: 15px 30px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s;
        }

        .query-input-group button:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.4);
        }

        .query-input-group button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .param-inputs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }

        .param-inputs input {
            padding: 15px 20px;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--primary);
            border-radius: 25px;
            color: white;
            font-family: 'Orbitron', sans-serif;
        }

        .example-text {
            color: var(--primary-light);
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            opacity: 0.8;
            margin-top: 10px;
        }

        /* Loader */
        .loader {
            display: none;
            text-align: center;
            padding: 30px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--primary);
            border-top-color: var(--primary-light);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loader p {
            color: white;
        }

        /* Result */
        .result {
            background: rgba(0,0,0,0.3);
            border-radius: 20px;
            padding: 20px;
            margin-top: 20px;
            border: 2px solid var(--primary);
            display: none;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .result-header h3 {
            color: var(--success);
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 16px;
        }

        .result-actions {
            display: flex;
            gap: 5px;
        }

        .result-actions button {
            padding: 5px 10px;
            background: rgba(255,255,255,0.1);
            border: 1px solid var(--primary);
            border-radius: 10px;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .result-actions button:hover {
            background: var(--primary);
        }

        .result-content {
            background: rgba(0,0,0,0.5);
            border-radius: 15px;
            padding: 15px;
            font-family: monospace;
            font-size: 12px;
            color: var(--primary-light);
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }

        /* Recent Queries */
        .recent-section {
            background: rgba(26, 11, 46, 0.98);
            border-radius: 30px;
            padding: 30px;
            border: 2px solid var(--primary);
            margin-top: 25px;
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .recent-header h2 {
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }

        .clear-btn {
            padding: 8px 15px;
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid var(--danger);
            border-radius: 20px;
            color: white;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            font-size: 12px;
        }

        .clear-btn:hover {
            background: var(--danger);
        }

        .recent-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .recent-item {
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .recent-item:hover {
            background: rgba(139, 92, 246, 0.2);
            transform: translateX(5px);
        }

        .recent-type {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 10px;
            display: inline-block;
            margin-bottom: 5px;
        }

        .recent-param {
            color: white;
            font-weight: 600;
            font-size: 12px;
            word-break: break-all;
        }

        .recent-time {
            color: rgba(255,255,255,0.4);
            font-size: 9px;
            margin-top: 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .user-info {
                flex-direction: column;
            }
            
            .query-input-group {
                flex-direction: column;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .query-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Background Elements -->
    <div class="floating-card card1">♠</div>
    <div class="floating-card card2">♣</div>
    <div class="floating-card card3">♥</div>
    <div class="floating-card card4">♦</div>
    
    <?php for ($i = 0; $i < 20; $i++): ?>
    <div class="particle" style="left: <?= rand(0, 100) ?>%; animation-delay: <?= rand(0, 10) ?>s;"></div>
    <?php endfor; ?>

    <?php if (!$kullanici): ?>
    <!-- Login/Register -->
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <i class="fas fa-crown"></i>
                <h1>NGB SORGU PANELİ</h1>
            </div>
            
            <div class="auth-tabs">
                <button class="auth-tab active" onclick="switchTab('login')">GİRİŞ</button>
                <button class="auth-tab" onclick="switchTab('register')">KAYIT</button>
            </div>
            
            <?php if (isset($hata)): ?>
                <div class="alert alert-error"><?= $hata ?></div>
            <?php endif; ?>
            
            <?php if (isset($kayit_basarili)): ?>
                <div class="alert alert-success"><?= $kayit_basarili ?></div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form class="auth-form active" id="loginForm" method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Kullanıcı Adı" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Şifre" required>
                </div>
                <button type="submit" name="login" class="auth-btn">GİRİŞ YAP</button>
            </form>
            
            <!-- Register Form -->
            <form class="auth-form" id="registerForm" method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Kullanıcı Adı" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="E-posta" required>
                </div>
                <div class="form-group">
                    <input type="text" name="fullname" placeholder="Ad Soyad" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Şifre" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" placeholder="Şifre Tekrar" required>
                </div>
                <button type="submit" name="register" class="auth-btn">KAYIT OL</button>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
            
            if (tab === 'login') {
                document.querySelector('.auth-tab:first-child').classList.add('active');
                document.getElementById('loginForm').classList.add('active');
            } else {
                document.querySelector('.auth-tab:last-child').classList.add('active');
                document.getElementById('registerForm').classList.add('active');
            }
        }
    </script>

    <?php else: ?>
    <!-- Dashboard -->
    <div class="dashboard">
        <!-- Header -->
        <div class="header">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <h3><?= htmlspecialchars($kullanici['fullname']) ?></h3>
                    <p>@<?= htmlspecialchars($kullanici['username']) ?></p>
                </div>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="badge"><?= $kullanici['role'] == 'admin' ? 'ADMIN' : 'KULLANICI' ?></div>
                <a href="?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> ÇIKIŞ</a>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-search"></i></div>
                <div class="stat-info">
                    <h3>Toplam Sorgu</h3>
                    <p><?= $kullanici['total_queries'] ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar"></i></div>
                <div class="stat-info">
                    <h3>Kayıt Tarihi</h3>
                    <p><?= date('d.m.Y', strtotime($kullanici['created_at'])) ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <h3>Son Giriş</h3>
                    <p><?= $kullanici['last_login'] ? date('d.m.Y H:i', strtotime($kullanici['last_login'])) : 'Yeni' ?></p>
                </div>
            </div>
        </div>

        <!-- Query Grid - TÜM SORGULAR BURADA -->
        <div class="query-grid">
            <div class="query-card" onclick="selectQuery('tc1', 'TC Sorgu-1', ['tc'], '11111111110')">
                <i class="fas fa-id-card"></i>
                <h3>TC Sorgu-1</h3>
                <p>Temel TC Sorgulama</p>
            </div>
            <div class="query-card" onclick="selectQuery('tc2', 'TC Sorgu-2', ['tc'], '11111111110')">
                <i class="fas fa-id-card"></i>
                <h3>TC Sorgu-2</h3>
                <p>Profesyonel TC</p>
            </div>
            <div class="query-card" onclick="selectQuery('tcgsm', 'TC\'den GSM', ['tc'], '11111111110')">
                <i class="fas fa-mobile-alt"></i>
                <h3>TC'den GSM</h3>
                <p>TC ile GSM bul</p>
            </div>
            <div class="query-card" onclick="selectQuery('gsmtc', 'GSM\'den TC', ['gsm'], '5415722525')">
                <i class="fas fa-mobile-alt"></i>
                <h3>GSM'den TC</h3>
                <p>GSM ile TC bul</p>
            </div>
            <div class="query-card" onclick="selectQuery('gncloperator', 'Güncel Operatör', ['numara'], '5415722525')">
                <i class="fas fa-signal"></i>
                <h3>Operatör</h3>
                <p>Güncel operatör</p>
            </div>
            <div class="query-card" onclick="selectQuery('isim', 'İsim Sorgu', ['ad', 'soyad'], 'roket atar')">
                <i class="fas fa-user"></i>
                <h3>İsim Sorgu</h3>
                <p>İsimden TC bul</p>
            </div>
            <div class="query-card" onclick="selectQuery('isim_pro', 'İsim Pro', ['ad', 'soyad', 'il'], 'roket atar bursa')">
                <i class="fas fa-user"></i>
                <h3>İsim Pro</h3>
                <p>İsim + İl</p>
            </div>
            <div class="query-card" onclick="selectQuery('isim_il', 'İsim+İlçe', ['ad', 'il', 'ilce'], 'roket bursa osmangazi')">
                <i class="fas fa-map-marker-alt"></i>
                <h3>İsim+İlçe</h3>
                <p>Detaylı isim</p>
            </div>
            <div class="query-card" onclick="selectQuery('aile', 'Aile Sorgu', ['tc'], '11111111110')">
                <i class="fas fa-users"></i>
                <h3>Aile</h3>
                <p>Aile bireyleri</p>
            </div>
            <div class="query-card" onclick="selectQuery('aile_pro', 'Aile Pro', ['tc'], '11111111110')">
                <i class="fas fa-users"></i>
                <h3>Aile Pro</h3>
                <p>Detaylı aile</p>
            </div>
            <div class="query-card" onclick="selectQuery('sulale', 'Sülale', ['tc'], '11111111110')">
                <i class="fas fa-tree"></i>
                <h3>Sülale</h3>
                <p>Sülale sorgu</p>
            </div>
            <div class="query-card" onclick="selectQuery('adres', 'Adres', ['tc'], '11111111110')">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Adres</h3>
                <p>Adres sorgu</p>
            </div>
            <div class="query-card" onclick="selectQuery('adres_pro', 'Adres Pro', ['tc'], '11144576054')">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Adres Pro</h3>
                <p>Detaylı adres</p>
            </div>
            <div class="query-card" onclick="selectQuery('isyeri', 'İş Yeri', ['tc'], '11144576054')">
                <i class="fas fa-briefcase"></i>
                <h3>İş Yeri</h3>
                <p>İş bilgileri</p>
            </div>
            <div class="query-card" onclick="selectQuery('isyeri_ark', 'İş Arkadaş', ['tc'], '11144576054')">
                <i class="fas fa-users"></i>
                <h3>İş Arkadaş</h3>
                <p>İş arkadaşları</p>
            </div>
            <div class="query-card" onclick="selectQuery('iban', 'IBAN', ['iban'], 'TR280006256953335759003718')">
                <i class="fas fa-coins"></i>
                <h3>IBAN</h3>
                <p>IBAN sorgu</p>
            </div>
        </div>

        <!-- Query Box -->
        <div class="query-box" id="queryBox" style="display: none;">
            <div class="query-header">
                <i class="fas fa-id-card" id="queryIcon"></i>
                <h2 id="queryTitle">Sorgu Seçin</h2>
            </div>
            
            <div id="queryParams"></div>
            
            <div class="query-input-group" style="margin-top: 15px;">
                <button onclick="executeQuery()" id="queryBtn">
                    <i class="fas fa-search"></i> SORGULA
                </button>
            </div>
            
            <div class="loader" id="queryLoader">
                <div class="spinner"></div>
                <p>Sorgulanıyor...</p>
            </div>
            
            <div class="result" id="resultContainer">
                <div class="result-header">
                    <h3><i class="fas fa-check-circle"></i> SONUÇ</h3>
                    <div class="result-actions">
                        <button onclick="copyResult()"><i class="fas fa-copy"></i></button>
                        <button onclick="downloadResult()"><i class="fas fa-download"></i></button>
                    </div>
                </div>
                <div class="result-content" id="resultContent"></div>
            </div>
        </div>

        <!-- Recent Queries -->
        <div class="recent-section">
            <div class="recent-header">
                <h2><i class="fas fa-history"></i> SON SORGULARIM</h2>
                <button class="clear-btn" onclick="clearRecent()">TEMİZLE</button>
            </div>
            <div class="recent-grid" id="recentGrid"></div>
        </div>
    </div>

    <script>
        let currentQuery = null;
        let currentParams = [];
        let recentQueries = JSON.parse(localStorage.getItem('recentQueries_<?= $kullanici['id'] ?>')) || [];

        function selectQuery(type, name, params, example) {
            currentQuery = type;
            currentParams = params;
            
            document.getElementById('queryBox').style.display = 'block';
            document.getElementById('queryIcon').className = document.querySelector(`[onclick*="'${type}'"] i`).className;
            document.getElementById('queryTitle').textContent = name;
            
            let html = '';
            const exampleParts = example.split(' ');
            
            for (let i = 0; i < params.length; i++) {
                html += `
                    <div class="param-inputs">
                        <input type="text" id="param_${i}" placeholder="${params[i].toUpperCase()}" value="${exampleParts[i] || ''}">
                    </div>
                `;
            }
            
            html += `<div class="example-text"><i class="fas fa-info-circle"></i> Örnek: ${example}</div>`;
            document.getElementById('queryParams').innerHTML = html;
            
            document.getElementById('queryBox').scrollIntoView({ behavior: 'smooth' });
        }

        async function executeQuery() {
            if (!currentQuery) {
                alert('Lütfen bir sorgu seçin!');
                return;
            }

            let params = [];
            for (let i = 0; i < currentParams.length; i++) {
                const value = document.getElementById(`param_${i}`).value.trim();
                if (!value) {
                    alert('Lütfen tüm parametreleri doldurun!');
                    return;
                }
                params.push(value);
            }

            document.getElementById('queryLoader').style.display = 'block';
            document.getElementById('resultContainer').style.display = 'none';
            document.getElementById('queryBtn').disabled = true;

            const timeout = setTimeout(() => {
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Zaman aşımı! Lütfen tekrar deneyin.');
            }, 30000);

            try {
                const paramStr = params.join('|');
                const response = await fetch(`?api_query=1&type=${currentQuery}&param=${encodeURIComponent(paramStr)}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                clearTimeout(timeout);

                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;

                if (data.success) {
                    const resultStr = JSON.stringify(data.data, null, 2);
                    document.getElementById('resultContent').textContent = resultStr;
                    document.getElementById('resultContainer').style.display = 'block';

                    // Son sorgulara ekle
                    recentQueries.unshift({
                        type: document.getElementById('queryTitle').textContent,
                        param: params.join(' '),
                        time: new Date().toLocaleString('tr-TR')
                    });
                    if (recentQueries.length > 10) recentQueries.pop();
                    localStorage.setItem('recentQueries_<?= $kullanici['id'] ?>', JSON.stringify(recentQueries));
                    loadRecent();

                    // Sorgu kaydı
                    const formData = new FormData();
                    formData.append('sorgu_kaydet', '1');
                    formData.append('sorgu_tipi', document.getElementById('queryTitle').textContent);
                    formData.append('sorgu_parametre', params.join(' '));
                    formData.append('sonuc', resultStr.substring(0, 500));
                    fetch('', { method: 'POST', body: formData });

                } else {
                    alert('Hata: ' + (data.error || 'Bilinmeyen hata'));
                }

            } catch (error) {
                clearTimeout(timeout);
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Bağlantı hatası: ' + error.message);
            }
        }

        function copyResult() {
            navigator.clipboard.writeText(document.getElementById('resultContent').textContent)
                .then(() => alert('Kopyalandı!'))
                .catch(() => alert('Kopyalanamadı!'));
        }

        function downloadResult() {
            const content = document.getElementById('resultContent').textContent;
            const blob = new Blob([content], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `sorgu_${Date.now()}.txt`;
            a.click();
            URL.revokeObjectURL(url);
        }

        function loadRecent() {
            const grid = document.getElementById('recentGrid');
            if (!recentQueries.length) {
                grid.innerHTML = '<p style="color: rgba(255,255,255,0.5); text-align: center;">Henüz sorgu yok</p>';
                return;
            }

            grid.innerHTML = recentQueries.map(q => `
                <div class="recent-item" onclick="recentQueryClick('${q.param}', '${q.type}')">
                    <span class="recent-type">${q.type}</span>
                    <div class="recent-param">${q.param}</div>
                    <div class="recent-time">${q.time}</div>
                </div>
            `).join('');
        }

        function recentQueryClick(param, typeName) {
            // Tipi bul ve seç
            const cards = document.querySelectorAll('.query-card');
            for (let card of cards) {
                if (card.querySelector('h3').textContent === typeName) {
                    card.click();
                    // Parametreleri ayarla
                    const params = param.split(' ');
                    setTimeout(() => {
                        for (let i = 0; i < params.length; i++) {
                            const input = document.getElementById(`param_${i}`);
                            if (input) input.value = params[i];
                        }
                        executeQuery();
                    }, 100);
                    break;
                }
            }
        }

        function clearRecent() {
            if (confirm('Tüm son sorgular temizlensin mi?')) {
                recentQueries = [];
                localStorage.removeItem('recentQueries_<?= $kullanici['id'] ?>');
                loadRecent();
            }
        }

        loadRecent();
    </script>
    <?php endif; ?>
</body>
</html>
