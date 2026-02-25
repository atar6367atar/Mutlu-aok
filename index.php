<?php
session_start();

// =============================================
// NGB SORGU PANELİ - ULTIMATE PRO EDITION
// PROFESYONEL ANİMASYONLAR - 3D EFEKTLER
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
        api_calls INTEGER DEFAULT 0,
        notes TEXT,
        banned_until DATETIME,
        ban_reason TEXT
    )");
    
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

// API Proxy
if (isset($_GET['api_query'])) {
    header('Content-Type: application/json');
    
    $type = $_GET['type'];
    $param = $_GET['param'];
    
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

// Veri temizleme
function cleanApiData($data) {
    if (is_array($data)) {
        $cleaned = [];
        foreach ($data as $key => $value) {
            if (preg_match('/developer|geliştirici|version|sürüm|reklam|kanal|telegram|punisher|admin|destek/i', $key)) {
                continue;
            }
            
            if (is_array($value)) {
                $cleanedValue = cleanApiData($value);
                if (!empty($cleanedValue)) {
                    $cleaned[$key] = $cleanedValue;
                }
            } else if (is_string($value)) {
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
    <title>NGB SORGU PANELİ | ULTIMATE PRO</title>
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
            --glass-bg: rgba(26, 11, 46, 0.7);
            --glass-border: rgba(139, 92, 246, 0.3);
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background: #000;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* 3D Arka Plan */
        #canvas-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* Işık Efektleri */
        .light {
            position: fixed;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(139,92,246,0.3) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            mix-blend-mode: screen;
        }

        .light-1 {
            top: -200px;
            left: -200px;
            animation: lightMove1 20s ease-in-out infinite;
        }

        .light-2 {
            bottom: -200px;
            right: -200px;
            animation: lightMove2 25s ease-in-out infinite;
        }

        .light-3 {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(196,181,253,0.2) 0%, transparent 70%);
            animation: lightPulse 10s ease-in-out infinite;
        }

        @keyframes lightMove1 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(100px, 100px); }
        }

        @keyframes lightMove2 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-100px, -100px); }
        }

        @keyframes lightPulse {
            0%, 100% { opacity: 0.3; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.6; transform: translate(-50%, -50%) scale(1.2); }
        }

        /* Partiküller */
        .particle {
            position: fixed;
            background: var(--primary-light);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            animation: particleFloat linear infinite;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.5;
            }
            90% {
                opacity: 0.5;
            }
            100% {
                transform: translateY(-100vh) translateX(100px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Glitch Efekti */
        .glitch {
            position: relative;
            animation: glitch 3s infinite;
        }

        @keyframes glitch {
            0%, 100% { transform: translate(0); }
            33% { transform: translate(-2px, 2px); }
            66% { transform: translate(2px, -2px); }
        }

        /* Neon Işıma */
        .neon {
            animation: neon 2s ease-in-out infinite;
        }

        @keyframes neon {
            0%, 100% { text-shadow: 0 0 10px var(--primary), 0 0 20px var(--primary), 0 0 30px var(--primary); }
            50% { text-shadow: 0 0 20px var(--primary-light), 0 0 40px var(--primary-light), 0 0 60px var(--primary-light); }
        }

        /* 3D Dönen Kartlar */
        .floating-card-3d {
            position: fixed;
            width: 100px;
            height: 140px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 15px;
            opacity: 0.1;
            transform-style: preserve-3d;
            animation: float3d 20s infinite linear;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes float3d {
            0% { transform: rotateX(0deg) rotateY(0deg) translateZ(0); }
            100% { transform: rotateX(360deg) rotateY(360deg) translateZ(100px); }
        }

        .card-1 { top: 10%; left: 5%; animation-duration: 25s; }
        .card-2 { top: 20%; right: 10%; animation-duration: 30s; }
        .card-3 { bottom: 15%; left: 15%; animation-duration: 35s; }
        .card-4 { bottom: 25%; right: 20%; animation-duration: 40s; }

        /* Auth Container */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 10;
            perspective: 1000px;
        }

        .auth-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 40px;
            padding: 50px 40px;
            width: 100%;
            max-width: 500px;
            border: 2px solid var(--glass-border);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 0 1px rgba(139,92,246,0.3), 0 0 30px var(--primary);
            transform-style: preserve-3d;
            animation: cardFloat 6s ease-in-out infinite;
        }

        @keyframes cardFloat {
            0%, 100% { transform: translateY(0) rotateX(0deg); }
            50% { transform: translateY(-20px) rotateX(2deg); }
        }

        .auth-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            position: relative;
        }

        .auth-tab {
            flex: 1;
            padding: 15px;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--glass-border);
            border-radius: 30px;
            color: white;
            font-family: 'Orbitron', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .auth-tab::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .auth-tab:hover::before {
            left: 100%;
        }

        .auth-tab.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-color: transparent;
            box-shadow: 0 0 30px var(--primary);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 40px;
            animation: logoGlow 3s ease-in-out infinite;
        }

        @keyframes logoGlow {
            0%, 100% { filter: drop-shadow(0 0 20px var(--primary)); }
            50% { filter: drop-shadow(0 0 50px var(--primary-light)); }
        }

        .auth-logo i {
            font-size: 80px;
            color: white;
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotateY(0deg); }
            to { transform: rotateY(360deg); }
        }

        .auth-logo h1 {
            font-size: 36px;
            color: white;
            margin-top: 20px;
            text-shadow: 0 0 20px var(--primary);
            animation: neon 2s ease-in-out infinite;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 18px 25px;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--glass-border);
            border-radius: 35px;
            color: white;
            font-size: 16px;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 30px var(--primary);
            transform: scale(1.02);
        }

        .input-group input::placeholder {
            color: rgba(255,255,255,0.5);
        }

        .input-group i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-light);
            opacity: 0.5;
        }

        .auth-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            border-radius: 35px;
            color: white;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }

        .auth-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .auth-btn:hover::before {
            left: 100%;
        }

        .auth-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 40px rgba(139,92,246,0.5);
        }

        .alert {
            padding: 15px;
            border-radius: 25px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background: rgba(239,68,68,0.2);
            border: 1px solid var(--danger);
            color: white;
            box-shadow: 0 0 20px rgba(239,68,68,0.3);
        }

        .alert-success {
            background: rgba(16,185,129,0.2);
            border: 1px solid var(--success);
            color: white;
            box-shadow: 0 0 20px rgba(16,185,129,0.3);
        }

        /* Dashboard */
        .dashboard {
            padding: 30px;
            position: relative;
            z-index: 10;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .glass-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 25px 35px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid var(--glass-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 0 0 1px rgba(139,92,246,0.3), 0 0 30px var(--primary);
            animation: headerGlow 4s ease-in-out infinite;
        }

        @keyframes headerGlow {
            0%, 100% { box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 0 0 1px rgba(139,92,246,0.3), 0 0 30px var(--primary); }
            50% { box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 0 0 1px rgba(139,92,246,0.3), 0 0 60px var(--primary-light); }
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .avatar-3d {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: white;
            transform-style: preserve-3d;
            animation: avatarRotate 10s linear infinite;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        @keyframes avatarRotate {
            from { transform: rotateY(0deg); }
            to { transform: rotateY(360deg); }
        }

        .user-text h2 {
            color: white;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .user-text p {
            color: var(--primary-light);
            font-size: 14px;
        }

        .badge-pro {
            background: linear-gradient(135deg, var(--warning), #fbbf24);
            padding: 10px 25px;
            border-radius: 30px;
            color: black;
            font-weight: 700;
            font-size: 14px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .logout-btn {
            background: rgba(239,68,68,0.2);
            color: white;
            border: 2px solid var(--danger);
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .logout-btn:hover {
            background: var(--danger);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(239,68,68,0.3);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 25px;
            border: 2px solid var(--glass-border);
            display: flex;
            align-items: center;
            gap: 20px;
            transform-style: preserve-3d;
            animation: cardHover 5s ease-in-out infinite;
            cursor: pointer;
            transition: all 0.3s;
        }

        .stat-card-3d:hover {
            transform: translateY(-10px) rotateX(5deg);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(139,92,246,0.3);
        }

        @keyframes cardHover {
            0%, 100% { transform: translateY(0) rotateX(0deg); }
            50% { transform: translateY(-10px) rotateX(5deg); }
        }

        .stat-icon-3d {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: white;
            transform: rotateY(0deg);
            animation: iconSpin 10s linear infinite;
        }

        @keyframes iconSpin {
            from { transform: rotateY(0deg); }
            to { transform: rotateY(360deg); }
        }

        .stat-info h3 {
            color: rgba(255,255,255,0.7);
            font-size: 14px;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: white;
            font-size: 32px;
            font-weight: 700;
            text-shadow: 0 0 20px var(--primary);
        }

        /* Query Grid */
        .query-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .query-card-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 20px;
            border: 2px solid var(--glass-border);
            cursor: pointer;
            text-align: center;
            transform-style: preserve-3d;
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
        }

        .query-card-3d::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            animation: shine 6s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            20%, 100% { transform: translateX(100%) rotate(45deg); }
        }

        .query-card-3d:hover {
            transform: translateY(-10px) rotateX(10deg) scale(1.05);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(139,92,246,0.4);
        }

        .query-card-3d i {
            font-size: 40px;
            color: var(--primary-light);
            margin-bottom: 15px;
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .query-card-3d h3 {
            color: white;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .query-card-3d p {
            color: rgba(255,255,255,0.5);
            font-size: 12px;
        }

        /* Query Box */
        .query-box {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 40px;
            padding: 35px;
            margin-bottom: 35px;
            border: 2px solid var(--glass-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: boxGlow 4s ease-in-out infinite;
        }

        @keyframes boxGlow {
            0%, 100% { box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 0 30px var(--primary); }
            50% { box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 0 60px var(--primary-light); }
        }

        .query-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--glass-border);
        }

        .query-header i {
            font-size: 50px;
            color: var(--primary-light);
            animation: iconRotate 10s linear infinite;
        }

        @keyframes iconRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .query-header h2 {
            color: white;
            font-size: 28px;
            text-shadow: 0 0 20px var(--primary);
        }

        .param-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .param-field {
            position: relative;
        }

        .param-field input {
            width: 100%;
            padding: 18px 25px;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--glass-border);
            border-radius: 30px;
            color: white;
            font-size: 16px;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s;
        }

        .param-field input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 30px var(--primary);
            transform: scale(1.02);
        }

        .param-field label {
            position: absolute;
            top: -10px;
            left: 20px;
            background: var(--primary-darker);
            padding: 0 10px;
            color: var(--primary-light);
            font-size: 12px;
            border-radius: 10px;
        }

        .query-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            border-radius: 35px;
            color: white;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            margin-top: 15px;
        }

        .query-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 40px rgba(139,92,246,0.5);
        }

        .example-text {
            color: var(--primary-light);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
            opacity: 0.8;
        }

        /* Loader */
        .loader-3d {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .spinner-3d {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            position: relative;
            transform-style: preserve-3d;
            animation: spin3d 2s linear infinite;
        }

        .spinner-3d div {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid transparent;
            border-top-color: var(--primary);
            border-right-color: var(--primary-light);
            border-radius: 50%;
            animation: spin 1.5s linear infinite;
        }

        .spinner-3d div:nth-child(2) {
            border-top-color: var(--primary-light);
            border-right-color: var(--primary);
            animation-direction: reverse;
        }

        @keyframes spin3d {
            0% { transform: rotateX(0deg) rotateY(0deg); }
            100% { transform: rotateX(360deg) rotateY(360deg); }
        }

        .loader-3d p {
            color: white;
            font-size: 16px;
            animation: pulse 1.5s ease-in-out infinite;
        }

        /* Result */
        .result-container {
            background: rgba(0,0,0,0.5);
            border-radius: 30px;
            padding: 25px;
            margin-top: 25px;
            border: 2px solid var(--primary);
            display: none;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .result-header h3 {
            color: var(--success);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }

        .result-actions {
            display: flex;
            gap: 10px;
        }

        .result-actions button {
            padding: 8px 15px;
            background: rgba(255,255,255,0.1);
            border: 2px solid var(--primary);
            border-radius: 15px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .result-actions button:hover {
            background: var(--primary);
            transform: scale(1.1) rotate(5deg);
        }

        .result-content {
            background: rgba(0,0,0,0.5);
            border-radius: 20px;
            padding: 20px;
            font-family: monospace;
            font-size: 13px;
            color: var(--primary-light);
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
            border: 1px solid var(--primary);
        }

        /* Recent Queries */
        .recent-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 40px;
            padding: 35px;
            border: 2px solid var(--glass-border);
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .recent-header h2 {
            color: white;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 24px;
        }

        .clear-btn {
            padding: 12px 25px;
            background: rgba(239,68,68,0.2);
            border: 2px solid var(--danger);
            border-radius: 30px;
            color: white;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            font-size: 14px;
            transition: all 0.3s;
        }

        .clear-btn:hover {
            background: var(--danger);
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(239,68,68,0.3);
        }

        .recent-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .recent-item {
            background: rgba(0,0,0,0.3);
            border-radius: 20px;
            padding: 18px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            animation: itemAppear 0.5s ease-out;
        }

        @keyframes itemAppear {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .recent-item:hover {
            background: rgba(139,92,246,0.2);
            transform: translateX(10px) scale(1.02);
            border-color: var(--primary);
            box-shadow: 0 10px 20px rgba(139,92,246,0.2);
        }

        .recent-type {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .recent-param {
            color: white;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            word-break: break-all;
        }

        .recent-time {
            color: rgba(255,255,255,0.4);
            font-size: 11px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--primary-darker);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-light);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard {
                padding: 15px;
            }
            
            .glass-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
                padding: 20px;
            }
            
            .user-info {
                flex-direction: column;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .query-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .auth-card {
                padding: 30px 20px;
            }
            
            .auth-logo h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- 3D Canvas Arka Plan -->
    <canvas id="canvas-bg"></canvas>
    
    <!-- Işık Efektleri -->
    <div class="light light-1"></div>
    <div class="light light-2"></div>
    <div class="light light-3"></div>
    
    <!-- 3D Dönen Kartlar -->
    <div class="floating-card-3d card-1"></div>
    <div class="floating-card-3d card-2"></div>
    <div class="floating-card-3d card-3"></div>
    <div class="floating-card-3d card-4"></div>

    <?php
    // Partiküller
    for ($i = 0; $i < 50; $i++):
        $size = rand(2, 6);
        $left = rand(0, 100);
        $delay = rand(0, 20);
        $duration = rand(15, 30);
    ?>
    <div class="particle" style="width: <?= $size ?>px; height: <?= $size ?>px; left: <?= $left ?>%; animation-delay: <?= $delay ?>s; animation-duration: <?= $duration ?>s;"></div>
    <?php endfor; ?>

    <?php if (!$kullanici): ?>
    <!-- Login/Register -->
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <i class="fas fa-crown"></i>
                <h1 class="neon">NGB SORGU</h1>
            </div>
            
            <div class="auth-tabs">
                <button class="auth-tab active" onclick="switchTab('login')">GİRİŞ</button>
                <button class="auth-tab" onclick="switchTab('register')">KAYIT</button>
            </div>
            
            <?php if (isset($hata)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $hata ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($kayit_basarili)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $kayit_basarili ?>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form class="auth-form active" id="loginForm" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Kullanıcı Adı" required>
                    <i class="fas fa-user"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Şifre" required>
                    <i class="fas fa-lock"></i>
                </div>
                <button type="submit" name="login" class="auth-btn">
                    <i class="fas fa-sign-in-alt"></i> GİRİŞ YAP
                </button>
            </form>
            
            <!-- Register Form -->
            <form class="auth-form" id="registerForm" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Kullanıcı Adı" required>
                    <i class="fas fa-user"></i>
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="E-posta" required>
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="input-group">
                    <input type="text" name="fullname" placeholder="Ad Soyad" required>
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Şifre" required>
                    <i class="fas fa-lock"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Şifre Tekrar" required>
                    <i class="fas fa-lock"></i>
                </div>
                <button type="submit" name="register" class="auth-btn">
                    <i class="fas fa-user-plus"></i> KAYIT OL
                </button>
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
        <div class="glass-header">
            <div class="user-info">
                <div class="avatar-3d">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-text">
                    <h2 class="neon"><?= htmlspecialchars($kullanici['fullname']) ?></h2>
                    <p>@<?= htmlspecialchars($kullanici['username']) ?></p>
                </div>
            </div>
            <div style="display: flex; gap: 20px; align-items: center;">
                <div class="badge-pro">
                    <i class="fas fa-crown"></i> <?= $kullanici['role'] == 'admin' ? 'ADMIN' : 'PRO USER' ?>
                </div>
                <a href="?logout=1" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> ÇIKIŞ
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card-3d">
                <div class="stat-icon-3d">
                    <i class="fas fa-search"></i>
                </div>
                <div class="stat-info">
                    <h3>Toplam Sorgu</h3>
                    <p><?= $kullanici['total_queries'] ?></p>
                </div>
            </div>
            <div class="stat-card-3d">
                <div class="stat-icon-3d">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-info">
                    <h3>Kayıt Tarihi</h3>
                    <p><?= date('d.m.Y', strtotime($kullanici['created_at'])) ?></p>
                </div>
            </div>
            <div class="stat-card-3d">
                <div class="stat-icon-3d">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Son Giriş</h3>
                    <p><?= $kullanici['last_login'] ? date('d.m.Y H:i', strtotime($kullanici['last_login'])) : 'Yeni' ?></p>
                </div>
            </div>
        </div>

        <!-- Query Grid -->
        <div class="query-grid">
            <div class="query-card-3d" onclick='selectQuery("tc1", "TC Sorgu-1", ["tc"], "11111111110")'>
                <i class="fas fa-id-card"></i>
                <h3>TC Sorgu-1</h3>
                <p>Temel TC</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("tc2", "TC Sorgu-2", ["tc"], "11111111110")'>
                <i class="fas fa-id-card"></i>
                <h3>TC Sorgu-2</h3>
                <p>Profesyonel</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("tcgsm", "TC'den GSM", ["tc"], "11111111110")'>
                <i class="fas fa-mobile-alt"></i>
                <h3>TC'den GSM</h3>
                <p>GSM bul</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("gsmtc", "GSM'den TC", ["gsm"], "5415722525")'>
                <i class="fas fa-mobile-alt"></i>
                <h3>GSM'den TC</h3>
                <p>TC bul</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("gncloperator", "Operatör", ["numara"], "5415722525")'>
                <i class="fas fa-signal"></i>
                <h3>Operatör</h3>
                <p>Güncel</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("isim", "İsim Sorgu", ["ad", "soyad"], "roket atar")'>
                <i class="fas fa-user"></i>
                <h3>İsim Sorgu</h3>
                <p>İsimden TC</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("isim_pro", "İsim Pro", ["ad", "soyad", "il"], "roket atar bursa")'>
                <i class="fas fa-user"></i>
                <h3>İsim Pro</h3>
                <p>İsim + İl</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("isim_il", "İsim+İlçe", ["ad", "il", "ilce"], "roket bursa osmangazi")'>
                <i class="fas fa-map-marker-alt"></i>
                <h3>İsim+İlçe</h3>
                <p>Detaylı</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("aile", "Aile", ["tc"], "11111111110")'>
                <i class="fas fa-users"></i>
                <h3>Aile</h3>
                <p>Aile bireyleri</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("aile_pro", "Aile Pro", ["tc"], "11111111110")'>
                <i class="fas fa-users"></i>
                <h3>Aile Pro</h3>
                <p>Detaylı</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("sulale", "Sülale", ["tc"], "11111111110")'>
                <i class="fas fa-tree"></i>
                <h3>Sülale</h3>
                <p>Soy ağacı</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("adres", "Adres", ["tc"], "11111111110")'>
                <i class="fas fa-map-marker-alt"></i>
                <h3>Adres</h3>
                <p>Adres sorgu</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("adres_pro", "Adres Pro", ["tc"], "11144576054")'>
                <i class="fas fa-map-marker-alt"></i>
                <h3>Adres Pro</h3>
                <p>Detaylı</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("isyeri", "İş Yeri", ["tc"], "11144576054")'>
                <i class="fas fa-briefcase"></i>
                <h3>İş Yeri</h3>
                <p>İş bilgileri</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("isyeri_ark", "İş Arkadaş", ["tc"], "11144576054")'>
                <i class="fas fa-users"></i>
                <h3>İş Arkadaş</h3>
                <p>İş arkadaşları</p>
            </div>
            <div class="query-card-3d" onclick='selectQuery("iban", "IBAN", ["iban"], "TR280006256953335759003718")'>
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
            
            <div class="loader-3d" id="queryLoader">
                <div class="spinner-3d">
                    <div></div>
                    <div></div>
                </div>
                <p>Sorgulanıyor...</p>
            </div>
            
            <div class="result-container" id="resultContainer">
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
                <button class="clear-btn" onclick="clearRecent()"><i class="fas fa-trash"></i> TEMİZLE</button>
            </div>
            <div class="recent-grid" id="recentGrid"></div>
        </div>
    </div>

    <script>
        // 3D Canvas Arka Plan
        const canvas = document.getElementById('canvas-bg');
        const ctx = canvas.getContext('2d');
        
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        const stars = [];
        for (let i = 0; i < 200; i++) {
            stars.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                z: Math.random() * 1000,
                size: Math.random() * 2
            });
        }
        
        function animate() {
            ctx.fillStyle = 'rgba(0,0,0,0.1)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            stars.forEach(star => {
                star.z -= 2;
                if (star.z <= 0) {
                    star.z = 1000;
                    star.x = Math.random() * canvas.width;
                    star.y = Math.random() * canvas.height;
                }
                
                const x = (star.x - canvas.width/2) * (1000 / star.z) + canvas.width/2;
                const y = (star.y - canvas.height/2) * (1000 / star.z) + canvas.height/2;
                const size = star.size * (1000 / star.z);
                
                if (x > 0 && x < canvas.width && y > 0 && y < canvas.height) {
                    ctx.fillStyle = `rgba(139, 92, 246, ${1 - star.z/1000})`;
                    ctx.fillRect(x, y, size, size);
                }
            });
            
            requestAnimationFrame(animate);
        }
        
        animate();
        
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        // Query Functions
        let currentQuery = null;
        let currentParams = [];
        let recentQueries = JSON.parse(localStorage.getItem('recentQueries_<?= $kullanici['id'] ?>')) || [];

        function selectQuery(type, name, params, example) {
            currentQuery = type;
            currentParams = params;
            
            document.getElementById('queryBox').style.display = 'block';
            document.getElementById('queryIcon').className = document.querySelector(`[onclick*='${type}'] i`).className;
            document.getElementById('queryTitle').textContent = name;
            
            let html = '';
            let exampleParts = example.split(' ');
            
            for (let i = 0; i < params.length; i++) {
                html += `
                    <div class="param-field">
                        <input type="text" id="param_${i}" placeholder="${params[i].toUpperCase()}" value="${exampleParts[i] || ''}">
                        <label>${params[i].toUpperCase()}</label>
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

            let timeout = setTimeout(() => {
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Zaman aşımı! Lütfen tekrar deneyin.');
            }, 30000);

            try {
                let paramStr = params.join('|');
                let response = await fetch(`?api_query=1&type=${currentQuery}&param=${encodeURIComponent(paramStr)}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                let data = await response.json();
                clearTimeout(timeout);

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
            let cards = document.querySelectorAll('.query-card-3d');
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
