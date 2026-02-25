<?php
session_start();

// =============================================
// NGB SORGU PANELİ - OPTİMİZE EDİLMİŞ PRO
// HIZLI - AKICI - HATASIZ
// Şifre: @ngbsorguata44 (Admin)
// =============================================

define('BOT_TOKEN', '8588404115:AAG7BD9FebTCIy-3VR7h4byCidwDcrIZXWw');
define('CHAT_ID', '8444268448');
define('API_BASE', 'https://punisherapi.alwaysdata.net');

// Veritabanı bağlantısı
try {
    $db = new PDO('sqlite:users.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
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
        api_calls INTEGER DEFAULT 0
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS query_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        username TEXT,
        query_type TEXT,
        query_param TEXT,
        result TEXT,
        ip TEXT,
        created_at DATETIME
    )");
    
    // Varsayılan admin
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

// Login
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
        
        header('Location: index.php');
        exit;
    } else {
        $hata = "Hatalı kullanıcı adı veya şifre!";
    }
}

// Register
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
        $kayit_basarili = "Kayıt başarılı! Giriş yapabilirsiniz.";
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Kullanıcı bilgileri
$kullanici = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Admin kontrolü
$isAdmin = ($kullanici && $kullanici['role'] == 'admin');

// API Proxy - DÜZELTİLDİ
if (isset($_GET['api_query'])) {
    header('Content-Type: application/json');
    
    $type = $_GET['type'];
    $param = $_GET['param'];
    
    // API endpoint'leri - DOĞRU URL'LER
    $apis = [
        'tc1' => 'tc.php',
        'tc2' => 'tcpro.php',
        'isim' => 'adsoyad.php',
        'isim_pro' => 'adsoyadpro.php',
        'isim_il' => 'adililce.php',
        'aile' => 'aile.php',
        'aile_pro' => 'ailepro.php',
        'sulale' => 'sulale.php',
        'adres' => 'adres.php',
        'adres_pro' => 'adrespro.php',
        'isyeri' => 'isyeri.php',
        'isyeri_ark' => 'isyeriark.php',
        'gncloperator' => 'gncloperator.php',
        'tcgsm' => 'tcgsm.php',
        'gsmtc' => 'gsmtc.php',
        'iban' => 'iban.php'
    ];
    
    if (!isset($apis[$type])) {
        echo json_encode(['success' => false, 'error' => 'Geçersiz sorgu tipi']);
        exit;
    }
    
    // Parametreleri ayır
    $params = explode('|', $param);
    
    // URL oluştur - DÜZELTİLDİ
    $url = API_BASE . '/apiservices/' . $apis[$type] . '?';
    
    // Parametre isimlerini belirle
    $paramNames = [];
    switch($type) {
        case 'tc1': case 'tc2': case 'aile': case 'aile_pro': case 'sulale': 
        case 'adres': case 'adres_pro': case 'isyeri': case 'isyeri_ark': 
        case 'tcgsm':
            $paramNames = ['tc'];
            break;
        case 'isim':
            $paramNames = ['ad', 'soyad'];
            break;
        case 'isim_pro':
            $paramNames = ['ad', 'soyad', 'il'];
            break;
        case 'isim_il':
            $paramNames = ['ad', 'il', 'ilce'];
            break;
        case 'gncloperator':
            $paramNames = ['numara'];
            break;
        case 'gsmtc':
            $paramNames = ['gsm'];
            break;
        case 'iban':
            $paramNames = ['iban'];
            break;
    }
    
    // URL parametrelerini ekle
    for ($i = 0; $i < count($paramNames); $i++) {
        if ($i > 0) $url .= '&';
        $url .= $paramNames[$i] . '=' . urlencode($params[$i]);
    }
    
    // API isteği - cURL ile
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200 && $response) {
        // Veriyi temizle
        $data = json_decode($response, true);
        if ($data) {
            // Reklamları temizle
            if (isset($data['developer'])) unset($data['developer']);
            if (isset($data['version'])) unset($data['version']);
            if (isset($data['geliştirici'])) unset($data['geliştirici']);
            
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => true, 'data' => $response]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'API yanıt vermiyor', 'url' => $url]);
    }
    exit;
}

// Sorgu kaydı
if (isset($_POST['sorgu_kaydet']) && $kullanici) {
    $sorgu_tipi = $_POST['sorgu_tipi'];
    $sorgu_parametre = $_POST['sorgu_parametre'];
    $sonuc = $_POST['sonuc'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $db->prepare("INSERT INTO query_logs (user_id, username, query_type, query_param, result, ip, created_at) VALUES (?, ?, ?, ?, ?, ?, datetime('now'))");
    $stmt->execute([$kullanici['id'], $kullanici['username'], $sorgu_tipi, $sorgu_parametre, $sonuc, $ip]);
    
    $stmt = $db->prepare("UPDATE users SET total_queries = total_queries + 1 WHERE id = ?");
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
    <title>NGB SORGU PANELİ | PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background: linear-gradient(135deg, #000000, var(--primary-darker));
            min-height: 100vh;
        }

        /* Optimize Edilmiş Arka Plan */
        .bg-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(139,92,246,0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(196,181,253,0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        /* Hafif Partiküller */
        .particle {
            position: fixed;
            width: 2px;
            height: 2px;
            background: var(--primary-light);
            border-radius: 50%;
            opacity: 0.2;
            animation: particleFloat 20s linear infinite;
            pointer-events: none;
            z-index: -1;
        }

        @keyframes particleFloat {
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
        }

        .auth-card {
            background: rgba(26, 11, 46, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border: 2px solid var(--primary);
            box-shadow: 0 0 40px rgba(139,92,246,0.3);
            animation: glow 3s ease-in-out infinite;
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 30px rgba(139,92,246,0.3); }
            50% { box-shadow: 0 0 50px rgba(139,92,246,0.5); }
        }

        .auth-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
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

        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo i {
            font-size: 70px;
            color: var(--primary-light);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .auth-logo h1 {
            font-size: 32px;
            color: white;
            margin-top: 15px;
            text-shadow: 0 0 20px var(--primary);
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group input {
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

        .input-group input:focus {
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
            box-shadow: 0 10px 30px rgba(139,92,246,0.5);
        }

        .alert {
            padding: 12px;
            border-radius: 20px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 13px;
        }

        .alert-error {
            background: rgba(239,68,68,0.2);
            border: 1px solid var(--danger);
            color: white;
        }

        .alert-success {
            background: rgba(16,185,129,0.2);
            border: 1px solid var(--success);
            color: white;
        }

        /* Dashboard */
        .dashboard {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: rgba(26, 11, 46, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 20px 30px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid var(--primary);
            box-shadow: 0 0 30px rgba(139,92,246,0.3);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .avatar {
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

        .user-text h3 {
            color: white;
            font-size: 18px;
        }

        .user-text p {
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
            background: rgba(239,68,68,0.2);
            color: white;
            border: 1px solid var(--danger);
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 13px;
        }

        .logout-btn:hover {
            background: var(--danger);
        }

        /* Stats */
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
            color: rgba(255,255,255,0.7);
            font-size: 13px;
        }

        .stat-info p {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }

        /* Query Grid */
        .query-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .query-card {
            background: rgba(26, 11, 46, 0.95);
            border-radius: 20px;
            padding: 20px 15px;
            border: 2px solid var(--primary);
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }

        .query-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-light);
            box-shadow: 0 10px 30px rgba(139,92,246,0.3);
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
            background: rgba(26, 11, 46, 0.95);
            backdrop-filter: blur(10px);
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
            font-size: 22px;
        }

        .param-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .param-field input {
            width: 100%;
            padding: 15px 20px;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--primary);
            border-radius: 25px;
            color: white;
            font-family: 'Orbitron', sans-serif;
            font-size: 14px;
        }

        .param-field input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 20px var(--primary);
        }

        .query-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            font-size: 16px;
            transition: all 0.3s;
        }

        .query-btn:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(139,92,246,0.5);
        }

        .query-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .example-text {
            color: var(--primary-light);
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
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
            margin: 0 auto 15px;
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
            gap: 8px;
            font-size: 16px;
        }

        .result-actions {
            display: flex;
            gap: 8px;
        }

        .result-actions button {
            padding: 5px 12px;
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

        /* Recent */
        .recent-section {
            background: rgba(26, 11, 46, 0.95);
            border-radius: 30px;
            padding: 30px;
            border: 2px solid var(--primary);
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
            font-size: 20px;
        }

        .clear-btn {
            padding: 8px 20px;
            background: rgba(239,68,68,0.2);
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
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
        }

        .recent-item {
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .recent-item:hover {
            background: rgba(139,92,246,0.2);
            transform: translateX(5px);
        }

        .recent-type {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 10px;
            display: inline-block;
            margin-bottom: 8px;
        }

        .recent-param {
            color: white;
            font-weight: 600;
            font-size: 13px;
            word-break: break-all;
        }

        .recent-time {
            color: rgba(255,255,255,0.4);
            font-size: 10px;
            margin-top: 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard {
                padding: 10px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .user-info {
                flex-direction: column;
            }
            
            .query-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .auth-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-gradient"></div>
    
    <?php for ($i = 0; $i < 30; $i++): ?>
    <div class="particle" style="left: <?= rand(0, 100) ?>%; animation-delay: <?= rand(0, 15) ?>s;"></div>
    <?php endfor; ?>

    <?php if (!$kullanici): ?>
    <!-- Login/Register -->
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <i class="fas fa-crown"></i>
                <h1>NGB SORGU</h1>
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
            
            <form class="auth-form active" id="loginForm" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Kullanıcı Adı" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Şifre" required>
                </div>
                <button type="submit" name="login" class="auth-btn">GİRİŞ YAP</button>
            </form>
            
            <form class="auth-form" id="registerForm" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Kullanıcı Adı" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="E-posta" required>
                </div>
                <div class="input-group">
                    <input type="text" name="fullname" placeholder="Ad Soyad" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Şifre" required>
                </div>
                <div class="input-group">
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
        <div class="header">
            <div class="user-info">
                <div class="avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-text">
                    <h3><?= htmlspecialchars($kullanici['fullname']) ?></h3>
                    <p>@<?= htmlspecialchars($kullanici['username']) ?></p>
                </div>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="badge"><?= $kullanici['role'] == 'admin' ? 'ADMIN' : 'USER' ?></div>
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

        <!-- Query Grid -->
        <div class="query-grid">
            <div class="query-card" onclick='selectQuery("tc1", "TC Sorgu-1", ["tc"], "11111111110")'>
                <i class="fas fa-id-card"></i>
                <h3>TC Sorgu-1</h3>
                <p>Temel TC</p>
            </div>
            <div class="query-card" onclick='selectQuery("tc2", "TC Sorgu-2", ["tc"], "11111111110")'>
                <i class="fas fa-id-card"></i>
                <h3>TC Sorgu-2</h3>
                <p>Profesyonel</p>
            </div>
            <div class="query-card" onclick='selectQuery("tcgsm", "TC'den GSM", ["tc"], "11111111110")'>
                <i class="fas fa-mobile-alt"></i>
                <h3>TC'den GSM</h3>
                <p>GSM bul</p>
            </div>
            <div class="query-card" onclick='selectQuery("gsmtc", "GSM'den TC", ["gsm"], "5415722525")'>
                <i class="fas fa-mobile-alt"></i>
                <h3>GSM'den TC</h3>
                <p>TC bul</p>
            </div>
            <div class="query-card" onclick='selectQuery("gncloperator", "Operatör", ["numara"], "5415722525")'>
                <i class="fas fa-signal"></i>
                <h3>Operatör</h3>
                <p>Güncel</p>
            </div>
            <div class="query-card" onclick='selectQuery("isim", "İsim Sorgu", ["ad", "soyad"], "roket atar")'>
                <i class="fas fa-user"></i>
                <h3>İsim Sorgu</h3>
                <p>İsimden TC</p>
            </div>
            <div class="query-card" onclick='selectQuery("isim_pro", "İsim Pro", ["ad", "soyad", "il"], "roket atar bursa")'>
                <i class="fas fa-user"></i>
                <h3>İsim Pro</h3>
                <p>İsim + İl</p>
            </div>
            <div class="query-card" onclick='selectQuery("isim_il", "İsim+İlçe", ["ad", "il", "ilce"], "roket bursa osmangazi")'>
                <i class="fas fa-map-marker-alt"></i>
                <h3>İsim+İlçe</h3>
                <p>Detaylı</p>
            </div>
            <div class="query-card" onclick='selectQuery("aile", "Aile", ["tc"], "11111111110")'>
                <i class="fas fa-users"></i>
                <h3>Aile</h3>
                <p>Aile bireyleri</p>
            </div>
            <div class="query-card" onclick='selectQuery("aile_pro", "Aile Pro", ["tc"], "11111111110")'>
                <i class="fas fa-users"></i>
                <h3>Aile Pro</h3>
                <p>Detaylı</p>
            </div>
            <div class="query-card" onclick='selectQuery("sulale", "Sülale", ["tc"], "11111111110")'>
                <i class="fas fa-tree"></i>
                <h3>Sülale</h3>
                <p>Soy ağacı</p>
            </div>
            <div class="query-card" onclick='selectQuery("adres", "Adres", ["tc"], "11111111110")'>
                <i class="fas fa-map-marker-alt"></i>
                <h3>Adres</h3>
                <p>Adres sorgu</p>
            </div>
            <div class="query-card" onclick='selectQuery("adres_pro", "Adres Pro", ["tc"], "11144576054")'>
                <i class="fas fa-map-marker-alt"></i>
                <h3>Adres Pro</h3>
                <p>Detaylı</p>
            </div>
            <div class="query-card" onclick='selectQuery("isyeri", "İş Yeri", ["tc"], "11144576054")'>
                <i class="fas fa-briefcase"></i>
                <h3>İş Yeri</h3>
                <p>İş bilgileri</p>
            </div>
            <div class="query-card" onclick='selectQuery("isyeri_ark", "İş Arkadaş", ["tc"], "11144576054")'>
                <i class="fas fa-users"></i>
                <h3>İş Arkadaş</h3>
                <p>İş arkadaşları</p>
            </div>
            <div class="query-card" onclick='selectQuery("iban", "IBAN", ["iban"], "TR280006256953335759003718")'>
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
            
            <button class="query-btn" onclick="executeQuery()" id="queryBtn">
                <i class="fas fa-search"></i> SORGULA
            </button>
            
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
                <h2><i class="fas fa-history"></i> SON SORGULAR</h2>
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
            document.getElementById('queryIcon').className = document.querySelector(`[onclick*='${type}'] i`).className;
            document.getElementById('queryTitle').textContent = name;
            
            let html = '<div class="param-grid">';
            let exampleParts = example.split(' ');
            
            for (let i = 0; i < params.length; i++) {
                html += `
                    <div class="param-field">
                        <input type="text" id="param_${i}" placeholder="${params[i].toUpperCase()}" value="${exampleParts[i] || ''}">
                    </div>
                `;
            }
            
            html += `</div><div class="example-text"><i class="fas fa-info-circle"></i> Örnek: ${example}</div>`;
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
                let value = document.getElementById(`param_${i}`).value.trim();
                if (!value) {
                    alert('Lütfen tüm parametreleri doldurun!');
                    return;
                }
                params.push(value);
            }

            document.getElementById('queryLoader').style.display = 'block';
            document.getElementById('resultContainer').style.display = 'none';
            document.getElementById('queryBtn').disabled = true;

            try {
                let paramStr = params.join('|');
                let response = await fetch(`?api_query=1&type=${currentQuery}&param=${encodeURIComponent(paramStr)}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                let data = await response.json();

                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;

                if (data.success) {
                    let resultStr = JSON.stringify(data.data, null, 2);
                    document.getElementById('resultContent').textContent = resultStr;
                    document.getElementById('resultContainer').style.display = 'block';

                    recentQueries.unshift({
                        type: document.getElementById('queryTitle').textContent,
                        param: params.join(' '),
                        time: new Date().toLocaleString('tr-TR')
                    });
                    if (recentQueries.length > 10) recentQueries.pop();
                    localStorage.setItem('recentQueries_<?= $kullanici['id'] ?>', JSON.stringify(recentQueries));
                    loadRecent();

                    let formData = new FormData();
                    formData.append('sorgu_kaydet', '1');
                    formData.append('sorgu_tipi', document.getElementById('queryTitle').textContent);
                    formData.append('sorgu_parametre', params.join(' '));
                    formData.append('sonuc', resultStr.substring(0, 500));
                    fetch('', { method: 'POST', body: formData });

                } else {
                    alert('Hata: ' + (data.error || 'Bilinmeyen hata'));
                }

            } catch (error) {
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
            let content = document.getElementById('resultContent').textContent;
            let blob = new Blob([content], { type: 'text/plain' });
            let url = URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = `sorgu_${Date.now()}.txt`;
            a.click();
            URL.revokeObjectURL(url);
        }

        function loadRecent() {
            let grid = document.getElementById('recentGrid');
            if (!recentQueries.length) {
                grid.innerHTML = '<p style="color: rgba(255,255,255,0.5); text-align: center;">Henüz sorgu yok</p>';
                return;
            }

            grid.innerHTML = recentQueries.map(q => `
                <div class="recent-item" onclick='recentQueryClick("${q.param}", "${q.type}")'>
                    <span class="recent-type">${q.type}</span>
                    <div class="recent-param">${q.param}</div>
                    <div class="recent-time">${q.time}</div>
                </div>
            `).join('');
        }

        function recentQueryClick(param, typeName) {
            let cards = document.querySelectorAll('.query-card');
            for (let card of cards) {
                if (card.querySelector('h3').textContent === typeName) {
                    card.click();
                    let params = param.split(' ');
                    setTimeout(() => {
                        for (let i = 0; i < params.length; i++) {
                            let input = document.getElementById(`param_${i}`);
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
