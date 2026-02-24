<?php
session_start();

// =============================================
// NGB SORGU PANELİ - ÇOK KULLANICILI SİSTEM
// HER KULLANICI KENDİ PANELİNİ KULLANIR
// Şifre: @ngbsorguata44 (Admin)
// =============================================

define('BOT_TOKEN', '8588404115:AAG7BD9FebTCIy-3VR7h4byCidwDcrIZXWw');
define('CHAT_ID', '8444268448');

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
    
    // API anahtarları tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS api_keys (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        api_key TEXT UNIQUE,
        created_at DATETIME,
        last_used DATETIME,
        expires_at DATETIME,
        is_active BOOLEAN DEFAULT 1
    )");
    
    // İşlem logları
    $db->exec("CREATE TABLE IF NOT EXISTS activity_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        username TEXT,
        action TEXT,
        details TEXT,
        ip TEXT,
        created_at DATETIME
    )");
    
    // Admin ayarları
    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        setting_key TEXT UNIQUE,
        setting_value TEXT,
        updated_at DATETIME
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

// Telegram log gönderme fonksiyonu
function sendTelegramLog($message) {
    @file_get_contents("https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage?chat_id=" . CHAT_ID . "&text=" . urlencode($message) . "&parse_mode=Markdown");
}

// Aktivite loglama
function logActivity($user_id, $username, $action, $details = '') {
    global $db;
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, username, action, details, ip, created_at) VALUES (?, ?, ?, ?, ?, datetime('now'))");
    $stmt->execute([$user_id, $username, $action, $details, $ip]);
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
        
        // Son giriş bilgilerini güncelle
        $stmt = $db->prepare("UPDATE users SET last_login = datetime('now'), last_ip = ? WHERE id = ?");
        $stmt->execute([$ip, $user['id']]);
        
        logActivity($user['id'], $user['username'], 'GİRİŞ', "Başarılı giriş");
        
        $mesaj = "🔐 *YENİ GİRİŞ*\n\n";
        $mesaj .= "👤 *Kullanıcı:* {$user['username']}\n";
        $mesaj .= "🌐 *IP:* `$ip`\n";
        $mesaj .= "🕒 *Tarih:* " . date('Y-m-d H:i:s');
        sendTelegramLog($mesaj);
        
        header('Location: index.php');
        exit;
    } else {
        $hata = "Hatalı kullanıcı adı veya şifre!";
        
        $mesaj = "⚠️ *BAŞARISIZ GİRİŞ DENEMESİ*\n\n";
        $mesaj .= "👤 *Kullanıcı:* $username\n";
        $mesaj .= "🌐 *IP:* `$ip`\n";
        $mesaj .= "🕒 *Tarih:* " . date('Y-m-d H:i:s');
        sendTelegramLog($mesaj);
    }
}

// Register işlemi
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Kullanıcı adı kontrolü
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $kayit_hata = "Bu kullanıcı adı zaten kullanılıyor!";
    } else {
        $stmt = $db->prepare("INSERT INTO users (username, password, email, fullname, role, created_at, last_ip) VALUES (?, ?, ?, ?, 'user', datetime('now'), ?)");
        $stmt->execute([$username, $password, $email, $fullname, $ip]);
        
        logActivity($db->lastInsertId(), $username, 'KAYIT', "Yeni kullanıcı kaydı");
        
        $mesaj = "✅ *YENİ KULLANICI KAYDI*\n\n";
        $mesaj .= "👤 *Kullanıcı:* $username\n";
        $mesaj .= "📧 *Email:* $email\n";
        $mesaj .= "👤 *İsim:* $fullname\n";
        $mesaj .= "🌐 *IP:* `$ip`\n";
        $mesaj .= "🕒 *Tarih:* " . date('Y-m-d H:i:s');
        sendTelegramLog($mesaj);
        
        $kayit_basarili = "Kayıt başarılı! Giriş yapabilirsiniz.";
    }
}

// Logout
if (isset($_GET['logout'])) {
    if (isset($_SESSION['user_id'])) {
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'ÇIKIŞ', 'Kullanıcı çıkış yaptı');
    }
    session_destroy();
    header('Location: index.php');
    exit;
}

// Kullanıcı bilgilerini al
$kullanici = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Ban kontrolü
    if ($kullanici['status'] == 'banned') {
        if ($kullanici['banned_until'] && strtotime($kullanici['banned_until']) > time()) {
            session_destroy();
            $ban_hata = "Hesabınız " . $kullanici['banned_until'] . " tarihine kadar banlanmıştır. Sebep: " . $kullanici['ban_reason'];
        } elseif (!$kullanici['banned_until']) {
            session_destroy();
            $ban_hata = "Hesabınız kalıcı olarak banlanmıştır. Sebep: " . $kullanici['ban_reason'];
        }
    }
}

// Admin kontrolü
$isAdmin = ($kullanici && $kullanici['role'] == 'admin');

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
    
    logActivity($kullanici['id'], $kullanici['username'], 'SORGU', "$sorgu_tipi: $sorgu_parametre");
    
    $mesaj = "🔍 *YENİ SORGU*\n\n";
    $mesaj .= "👤 *Kullanıcı:* {$kullanici['username']}\n";
    $mesaj .= "📌 *Tip:* $sorgu_tipi\n";
    $mesaj .= "🔎 *Parametre:* `$sorgu_parametre`\n";
    $mesaj .= "🌐 *IP:* `$ip`\n";
    $mesaj .= "🕒 *Tarih:* " . date('Y-m-d H:i:s');
    sendTelegramLog($mesaj);
    
    echo json_encode(['success' => true]);
    exit;
}

// Admin işlemleri
if ($isAdmin && isset($_POST['admin_action'])) {
    $action = $_POST['admin_action'];
    
    if ($action == 'ban_user') {
        $user_id = $_POST['user_id'];
        $reason = $_POST['reason'];
        $duration = $_POST['duration']; // days or 'permanent'
        
        if ($duration == 'permanent') {
            $stmt = $db->prepare("UPDATE users SET status = 'banned', ban_reason = ?, banned_until = NULL WHERE id = ?");
        } else {
            $until = date('Y-m-d H:i:s', strtotime("+$duration days"));
            $stmt = $db->prepare("UPDATE users SET status = 'banned', ban_reason = ?, banned_until = ? WHERE id = ?");
            $stmt->execute([$reason, $until, $user_id]);
        }
        
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'ADMIN_BAN', "Kullanıcı $user_id banlandı: $reason");
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($action == 'unban_user') {
        $user_id = $_POST['user_id'];
        $stmt = $db->prepare("UPDATE users SET status = 'active', ban_reason = NULL, banned_until = NULL WHERE id = ?");
        $stmt->execute([$user_id]);
        
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'ADMIN_UNBAN', "Kullanıcı $user_id banı kaldırıldı");
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($action == 'change_role') {
        $user_id = $_POST['user_id'];
        $role = $_POST['role'];
        $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $user_id]);
        
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'ADMIN_ROLE', "Kullanıcı $user_id rolü: $role");
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($action == 'add_note') {
        $user_id = $_POST['user_id'];
        $note = $_POST['note'];
        $stmt = $db->prepare("UPDATE users SET notes = ? WHERE id = ?");
        $stmt->execute([$note, $user_id]);
        
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'ADMIN_NOTE', "Kullanıcı $user_id not: $note");
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($action == 'delete_user') {
        $user_id = $_POST['user_id'];
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$user_id]);
        
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'ADMIN_DELETE', "Kullanıcı $user_id silindi");
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($action == 'clear_logs') {
        $days = $_POST['days'];
        $stmt = $db->prepare("DELETE FROM query_logs WHERE created_at < datetime('now', '-' || ? || ' days')");
        $stmt->execute([$days]);
        
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'ADMIN_CLEAR_LOGS', "$days gün öncesi loglar temizlendi");
        
        echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);
        exit;
    }
    
    if ($action == 'generate_api_key') {
        $user_id = $_POST['user_id'];
        $api_key = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $stmt = $db->prepare("INSERT INTO api_keys (user_id, api_key, created_at, expires_at) VALUES (?, ?, datetime('now'), ?)");
        $stmt->execute([$user_id, $api_key, $expires]);
        
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'ADMIN_API_KEY', "Kullanıcı $user_id için API anahtarı oluşturuldu");
        
        echo json_encode(['success' => true, 'api_key' => $api_key]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGB SORGU PANELİ | ÇOK KULLANICILI SİSTEM</title>
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

        /* Animations */
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

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
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

        .card1 { top: 10%; left: 5%; animation-delay: 0s; }
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

        /* Login/Register Container */
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

        .form-group input::placeholder {
            color: rgba(255,255,255,0.5);
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

        /* Category Grid */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .category-card {
            background: rgba(26, 11, 46, 0.95);
            border-radius: 20px;
            padding: 15px;
            border: 2px solid var(--primary);
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }

        .category-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-light);
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);
        }

        .category-card i {
            font-size: 30px;
            color: var(--primary-light);
            margin-bottom: 10px;
        }

        .category-card h3 {
            color: white;
            font-size: 12px;
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

        .example-text {
            color: var(--primary-light);
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            opacity: 0.8;
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
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
        }

        /* Recent Queries */
        .recent-section {
            background: rgba(26, 11, 46, 0.98);
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

        /* Admin Panel */
        .admin-panel {
            margin-top: 25px;
            padding: 25px;
            background: rgba(26, 11, 46, 0.98);
            border-radius: 30px;
            border: 3px solid var(--warning);
        }

        .admin-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--warning);
        }

        .admin-header i {
            font-size: 40px;
            color: var(--warning);
        }

        .admin-header h2 {
            color: white;
            font-size: 22px;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .admin-stat-card {
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
            padding: 15px;
            border: 1px solid var(--warning);
        }

        .admin-stat-card h4 {
            color: var(--warning);
            font-size: 12px;
            margin-bottom: 5px;
        }

        .admin-stat-card p {
            color: white;
            font-size: 20px;
            font-weight: 700;
        }

        .admin-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .admin-tab {
            padding: 10px 20px;
            background: transparent;
            border: 2px solid var(--warning);
            border-radius: 25px;
            color: white;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            font-size: 12px;
            transition: all 0.3s;
        }

        .admin-tab.active {
            background: var(--warning);
            border-color: transparent;
        }

        .admin-content {
            display: none;
        }

        .admin-content.active {
            display: block;
            animation: slideIn 0.5s ease-out;
        }

        .admin-table {
            width: 100%;
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
            overflow: hidden;
        }

        .admin-table th {
            background: var(--warning);
            color: white;
            padding: 12px;
            font-size: 12px;
            text-align: left;
        }

        .admin-table td {
            padding: 12px;
            color: white;
            font-size: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .admin-table tr:hover {
            background: rgba(255,255,255,0.05);
        }

        .admin-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 11px;
            margin: 0 2px;
        }

        .btn-warning { background: var(--warning); color: black; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-info { background: var(--info); color: white; }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: rgba(26, 11, 46, 0.98);
            border-radius: 30px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            border: 3px solid var(--warning);
        }

        .modal-content h3 {
            color: white;
            margin-bottom: 20px;
        }

        .modal-content input,
        .modal-content select,
        .modal-content textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--primary);
            border-radius: 15px;
            color: white;
            font-family: 'Orbitron', sans-serif;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
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
            
            <?php if (isset($ban_hata)): ?>
                <div class="alert alert-error"><?= $ban_hata ?></div>
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
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-globe"></i></div>
                <div class="stat-info">
                    <h3>IP Adresi</h3>
                    <p><?= $kullanici['last_ip'] ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Categories -->
        <div class="category-grid">
            <div class="category-card" onclick="setCategory('tc')">
                <i class="fas fa-id-card"></i>
                <h3>TC</h3>
            </div>
            <div class="category-card" onclick="setCategory('aile')">
                <i class="fas fa-users"></i>
                <h3>AİLE</h3>
            </div>
            <div class="category-card" onclick="setCategory('isim')">
                <i class="fas fa-user"></i>
                <h3>İSİM</h3>
            </div>
            <div class="category-card" onclick="setCategory('adres')">
                <i class="fas fa-map-marker-alt"></i>
                <h3>ADRES</h3>
            </div>
            <div class="category-card" onclick="setCategory('is')">
                <i class="fas fa-briefcase"></i>
                <h3>İŞ</h3>
            </div>
            <div class="category-card" onclick="setCategory('gsm')">
                <i class="fas fa-mobile-alt"></i>
                <h3>GSM</h3>
            </div>
            <div class="category-card" onclick="setCategory('finans')">
                <i class="fas fa-coins"></i>
                <h3>FİNANS</h3>
            </div>
        </div>

        <!-- Query Box -->
        <div class="query-box">
            <div class="query-header">
                <i class="fas fa-id-card" id="queryIcon"></i>
                <h2 id="queryTitle">TC Sorgulama</h2>
                <div class="badge" id="queryBadge">TC-1</div>
            </div>
            
            <div class="query-input-group">
                <input type="text" id="queryParam" placeholder="Parametre girin..." onkeypress="if(event.key==='Enter') executeQuery()">
                <button onclick="executeQuery()" id="queryBtn">
                    <i class="fas fa-search"></i> SORGULA
                </button>
            </div>
            
            <div class="example-text" id="queryExample">
                <i class="fas fa-info-circle"></i> Örnek: 11111111110
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

        <?php if ($isAdmin): ?>
        <!-- Admin Panel -->
        <div class="admin-panel">
            <div class="admin-header">
                <i class="fas fa-crown"></i>
                <h2>ADMIN PANELİ</h2>
            </div>

            <?php
            // Admin istatistikleri
            $stmt = $db->query("SELECT COUNT(*) as total_users FROM users");
            $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
            
            $stmt = $db->query("SELECT COUNT(*) as total_queries FROM query_logs");
            $total_queries = $stmt->fetch(PDO::FETCH_ASSOC)['total_queries'];
            
            $stmt = $db->query("SELECT COUNT(*) as active_today FROM users WHERE last_login > datetime('now', '-1 day')");
            $active_today = $stmt->fetch(PDO::FETCH_ASSOC)['active_today'];
            
            $stmt = $db->query("SELECT COUNT(*) as banned_users FROM users WHERE status = 'banned'");
            $banned_users = $stmt->fetch(PDO::FETCH_ASSOC)['banned_users'];
            ?>

            <div class="admin-stats">
                <div class="admin-stat-card">
                    <h4>Toplam Kullanıcı</h4>
                    <p><?= $total_users ?></p>
                </div>
                <div class="admin-stat-card">
                    <h4>Toplam Sorgu</h4>
                    <p><?= $total_queries ?></p>
                </div>
                <div class="admin-stat-card">
                    <h4>Aktif (24s)</h4>
                    <p><?= $active_today ?></p>
                </div>
                <div class="admin-stat-card">
                    <h4>Banlı</h4>
                    <p><?= $banned_users ?></p>
                </div>
            </div>

            <div class="admin-tabs">
                <button class="admin-tab active" onclick="switchAdminTab('users')">KULLANICILAR</button>
                <button class="admin-tab" onclick="switchAdminTab('queries')">SORGULAR</button>
                <button class="admin-tab" onclick="switchAdminTab('logs')">LOG KAYITLARI</button>
                <button class="admin-tab" onclick="switchAdminTab('settings')">AYARLAR</button>
            </div>

            <!-- Users Tab -->
            <div class="admin-content active" id="adminUsers">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı</th>
                            <th>Ad Soyad</th>
                            <th>Rol</th>
                            <th>Sorgu</th>
                            <th>Durum</th>
                            <th>Son Giriş</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->query("SELECT * FROM users ORDER BY id DESC LIMIT 20");
                        while($user = $stmt->fetch(PDO::FETCH_ASSOC)):
                        ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['fullname']) ?></td>
                            <td><?= $user['role'] ?></td>
                            <td><?= $user['total_queries'] ?></td>
                            <td>
                                <span style="color: <?= $user['status'] == 'active' ? 'var(--success)' : 'var(--danger)' ?>">
                                    <?= $user['status'] ?>
                                </span>
                            </td>
                            <td><?= $user['last_login'] ? date('d.m H:i', strtotime($user['last_login'])) : '-' ?></td>
                            <td>
                                <button class="admin-btn btn-info" onclick="viewUser(<?= $user['id'] ?>)"><i class="fas fa-eye"></i></button>
                                <button class="admin-btn btn-warning" onclick="banUser(<?= $user['id'] ?>, '<?= $user['username'] ?>')"><i class="fas fa-ban"></i></button>
                                <button class="admin-btn btn-success" onclick="changeRole(<?= $user['id'] ?>, '<?= $user['username'] ?>')"><i class="fas fa-crown"></i></button>
                                <button class="admin-btn btn-danger" onclick="deleteUser(<?= $user['id'] ?>, '<?= $user['username'] ?>')"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Queries Tab -->
            <div class="admin-content" id="adminQueries">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı</th>
                            <th>Sorgu Tipi</th>
                            <th>Parametre</th>
                            <th>IP</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->query("SELECT * FROM query_logs ORDER BY id DESC LIMIT 20");
                        while($log = $stmt->fetch(PDO::FETCH_ASSOC)):
                        ?>
                        <tr>
                            <td><?= $log['id'] ?></td>
                            <td><?= htmlspecialchars($log['username']) ?></td>
                            <td><?= $log['query_type'] ?></td>
                            <td><?= htmlspecialchars($log['query_param']) ?></td>
                            <td><?= $log['ip'] ?></td>
                            <td><?= date('d.m H:i', strtotime($log['created_at'])) ?></td>
                            <td>
                                <span style="color: var(--success)">✓</span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div style="margin-top: 15px;">
                    <button class="admin-btn btn-danger" onclick="clearOldLogs()">
                        <i class="fas fa-trash"></i> 30 GÜNDEN ESKİ LOGLARI TEMİZLE
                    </button>
                </div>
            </div>

            <!-- Logs Tab -->
            <div class="admin-content" id="adminLogs">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı</th>
                            <th>İşlem</th>
                            <th>Detay</th>
                            <th>IP</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->query("SELECT * FROM activity_logs ORDER BY id DESC LIMIT 20");
                        while($log = $stmt->fetch(PDO::FETCH_ASSOC)):
                        ?>
                        <tr>
                            <td><?= $log['id'] ?></td>
                            <td><?= htmlspecialchars($log['username']) ?></td>
                            <td><?= $log['action'] ?></td>
                            <td><?= htmlspecialchars($log['details']) ?></td>
                            <td><?= $log['ip'] ?></td>
                            <td><?= date('d.m H:i', strtotime($log['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Settings Tab -->
            <div class="admin-content" id="adminSettings">
                <div style="display: grid; gap: 20px;">
                    <div>
                        <h3 style="color: white; margin-bottom: 15px;">API Ayarları</h3>
                        <button class="admin-btn btn-success" onclick="generateAPIKey()">
                            <i class="fas fa-key"></i> Yeni API Anahtarı Oluştur
                        </button>
                    </div>
                    
                    <div>
                        <h3 style="color: white; margin-bottom: 15px;">Sistem Bilgileri</h3>
                        <table class="admin-table">
                            <tr>
                                <td>PHP Versiyon</td>
                                <td><?= phpversion() ?></td>
                            </tr>
                            <tr>
                                <td>Veritabanı</td>
                                <td>SQLite3</td>
                            </tr>
                            <tr>
                                <td>Sunucu Zamanı</td>
                                <td><?= date('Y-m-d H:i:s') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modals -->
    <div class="modal" id="banModal">
        <div class="modal-content">
            <h3>Kullanıcı Banla</h3>
            <input type="hidden" id="banUserId">
            <input type="text" id="banUsername" readonly style="background: rgba(255,255,255,0.1);">
            <textarea id="banReason" placeholder="Ban sebebi" rows="3"></textarea>
            <select id="banDuration">
                <option value="1">1 Gün</option>
                <option value="7">7 Gün</option>
                <option value="30">30 Gün</option>
                <option value="permanent">Süresiz</option>
            </select>
            <div class="modal-buttons">
                <button class="admin-btn btn-success" onclick="confirmBan()">BANLA</button>
                <button class="admin-btn btn-danger" onclick="closeModal('banModal')">İPTAL</button>
            </div>
        </div>
    </div>

    <div class="modal" id="roleModal">
        <div class="modal-content">
            <h3>Yetki Değiştir</h3>
            <input type="hidden" id="roleUserId">
            <input type="text" id="roleUsername" readonly style="background: rgba(255,255,255,0.1);">
            <select id="newRole">
                <option value="user">Kullanıcı</option>
                <option value="admin">Admin</option>
            </select>
            <div class="modal-buttons">
                <button class="admin-btn btn-success" onclick="confirmRole()">DEĞİŞTİR</button>
                <button class="admin-btn btn-danger" onclick="closeModal('roleModal')">İPTAL</button>
            </div>
        </div>
    </div>

    <script>
        // API Listesi
        const apiList = {
            'tc1': { name: 'TC Sorgu-1', icon: 'fa-id-card', example: '11111111110', badge: 'TC-1', category: 'tc', endpoint: '/apiservices/tc.php', params: ['tc'] },
            'tc2': { name: 'TC Sorgu-2', icon: 'fa-id-card', example: '11111111110', badge: 'TC-2', category: 'tc', endpoint: '/apiservices/tcpro.php', params: ['tc'] },
            'isim': { name: 'İsim Sorgu', icon: 'fa-user', example: 'roket atar', badge: 'İSİM', category: 'isim', endpoint: '/apiservices/adsoyad.php', params: ['ad', 'soyad'] },
            'isim_pro': { name: 'İsim Pro', icon: 'fa-user', example: 'roket atar bursa', badge: 'İSİM PRO', category: 'isim', endpoint: '/apiservices/adsoyadpro.php', params: ['ad', 'soyad', 'il'] },
            'isim_il': { name: 'İsim+İlçe', icon: 'fa-user', example: 'roket bursa osmangazi', badge: 'İSİM+İL', category: 'isim', endpoint: '/apiservices/adililce.php', params: ['ad', 'il', 'ilce'] },
            'aile': { name: 'Aile Sorgu', icon: 'fa-users', example: '11111111110', badge: 'AİLE', category: 'aile', endpoint: '/apiservices/aile.php', params: ['tc'] },
            'aile_pro': { name: 'Aile Pro', icon: 'fa-users', example: '11111111110', badge: 'AİLE PRO', category: 'aile', endpoint: '/apiservices/ailepro.php', params: ['tc'] },
            'sulale': { name: 'Sülale', icon: 'fa-tree', example: '11111111110', badge: 'SÜLALE', category: 'aile', endpoint: '/apiservices/sulale.php', params: ['tc'] },
            'adres': { name: 'Adres', icon: 'fa-map-marker-alt', example: '11111111110', badge: 'ADRES', category: 'adres', endpoint: '/apiservices/adres.php', params: ['tc'] },
            'adres_pro': { name: 'Adres Pro', icon: 'fa-map-marker-alt', example: '11144576054', badge: 'ADRES PRO', category: 'adres', endpoint: '/apiservices/adrespro.php', params: ['tc'] },
            'isyeri': { name: 'İş Yeri', icon: 'fa-briefcase', example: '11144576054', badge: 'İŞYERİ', category: 'is', endpoint: '/apiservices/isyeri.php', params: ['tc'] },
            'isyeri_ark': { name: 'İş Arkadaş', icon: 'fa-users', example: '11144576054', badge: 'İŞ ARK', category: 'is', endpoint: '/apiservices/isyeriark.php', params: ['tc'] },
            'gncloperator': { name: 'Operatör', icon: 'fa-signal', example: '5415722525', badge: 'OPERATÖR', category: 'gsm', endpoint: '/apiservices/gncloperator.php', params: ['numara'] },
            'tcgsm': { name: 'TC>GSM', icon: 'fa-mobile-alt', example: '11111111110', badge: 'TC>GSM', category: 'gsm', endpoint: '/apiservices/tcgsm.php', params: ['tc'] },
            'gsmtc': { name: 'GSM>TC', icon: 'fa-mobile-alt', example: '5415722525', badge: 'GSM>TC', category: 'gsm', endpoint: '/apiservices/gsmtc.php', params: ['gsm'] },
            'iban': { name: 'IBAN', icon: 'fa-coins', example: 'TR280006256953335759003718', badge: 'IBAN', category: 'finans', endpoint: '/apiservices/iban.php', params: ['iban'] }
        };

        let currentType = 'tc1';
        let recentQueries = JSON.parse(localStorage.getItem('recentQueries_' + <?= $kullanici['id'] ?>)) || [];

        function setCategory(cat) {
            const first = Object.values(apiList).find(api => api.category === cat);
            if (first) {
                const typeKey = Object.keys(apiList).find(key => apiList[key] === first);
                setType(typeKey);
            }
        }

        function setType(type) {
            currentType = type;
            const api = apiList[type];
            document.getElementById('queryIcon').className = `fas ${api.icon}`;
            document.getElementById('queryTitle').textContent = api.name;
            document.getElementById('queryBadge').textContent = api.badge;
            document.getElementById('queryExample').innerHTML = `<i class="fas fa-info-circle"></i> Örnek: ${api.example}`;
            document.getElementById('queryParam').placeholder = api.example;
        }

        function cleanData(data) {
            const bannedKeys = ['developer', 'geliştirici', 'version', 'sürüm', 'v1', 'v2', 'v3', 'v4', 
                              'reklam', 'kanal', 'telegram', 't.me', 'punisher', 'admin', 'destek'];
            
            if (typeof data === 'object' && data !== null) {
                if (Array.isArray(data)) {
                    return data.map(item => cleanData(item)).filter(item => item !== null);
                } else {
                    const cleaned = {};
                    for (const [key, value] of Object.entries(data)) {
                        if (!bannedKeys.some(banned => key.toLowerCase().includes(banned))) {
                            if (typeof value === 'object' && value !== null) {
                                const cleanedValue = cleanData(value);
                                if (cleanedValue && Object.keys(cleanedValue).length > 0) {
                                    cleaned[key] = cleanedValue;
                                }
                            } else if (typeof value === 'string') {
                                let cleanedStr = value
                                    .replace(/@\w+/g, '')
                                    .replace(/t\.me\/\w+/g, '')
                                    .replace(/https?:\/\/\S+/g, '')
                                    .replace(/punishe\w+/gi, '')
                                    .trim();
                                if (cleanedStr) {
                                    cleaned[key] = cleanedStr;
                                }
                            } else {
                                cleaned[key] = value;
                            }
                        }
                    }
                    return cleaned;
                }
            }
            return data;
        }

        async function executeQuery() {
            const param = document.getElementById('queryParam').value.trim();
            if (!param) {
                alert('Parametre girin!');
                return;
            }

            const api = apiList[currentType];
            const params = param.split(' ');

            if (params.length < api.params.length) {
                alert(`Eksik parametre! ${api.params.length} parametre girmelisiniz.`);
                return;
            }

            document.getElementById('queryLoader').style.display = 'block';
            document.getElementById('resultContainer').style.display = 'none';
            document.getElementById('queryBtn').disabled = true;

            const timeout = setTimeout(() => {
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Zaman aşımı!');
            }, 30000);

            try {
                const queryParams = new URLSearchParams();
                for (let i = 0; i < api.params.length; i++) {
                    queryParams.append(api.params[i], params[i]);
                }

                const response = await fetch(`https://punisherapi.alwaysdata.net${api.endpoint}?${queryParams.toString()}`);
                clearTimeout(timeout);

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();
                const cleanedData = cleanData(data);
                const resultStr = JSON.stringify(cleanedData, null, 2);

                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                
                const resultContent = document.getElementById('resultContent');
                resultContent.textContent = resultStr;
                document.getElementById('resultContainer').style.display = 'block';

                recentQueries.unshift({
                    type: api.name,
                    param: param,
                    time: new Date().toLocaleString('tr-TR')
                });
                if (recentQueries.length > 10) recentQueries.pop();
                localStorage.setItem('recentQueries_' + <?= $kullanici['id'] ?>, JSON.stringify(recentQueries));
                loadRecent();

                const formData = new FormData();
                formData.append('sorgu_kaydet', '1');
                formData.append('sorgu_tipi', api.name);
                formData.append('sorgu_parametre', param);
                formData.append('sonuc', resultStr.substring(0, 500));
                fetch('', { method: 'POST', body: formData });

            } catch (error) {
                clearTimeout(timeout);
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Hata: ' + error.message);
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
            const foundType = Object.keys(apiList).find(key => apiList[key].name === typeName);
            if (foundType) {
                setType(foundType);
                document.getElementById('queryParam').value = param;
                executeQuery();
            }
        }

        function clearRecent() {
            if (confirm('Tüm son sorgular temizlensin mi?')) {
                recentQueries = [];
                localStorage.removeItem('recentQueries_' + <?= $kullanici['id'] ?>);
                loadRecent();
            }
        }

        // Admin Functions
        <?php if ($isAdmin): ?>
        function switchAdminTab(tab) {
            document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.admin-content').forEach(c => c.classList.remove('active'));
            
            if (tab === 'users') {
                document.querySelector('.admin-tab:first-child').classList.add('active');
                document.getElementById('adminUsers').classList.add('active');
            } else if (tab === 'queries') {
                document.querySelector('.admin-tab:nth-child(2)').classList.add('active');
                document.getElementById('adminQueries').classList.add('active');
            } else if (tab === 'logs') {
                document.querySelector('.admin-tab:nth-child(3)').classList.add('active');
                document.getElementById('adminLogs').classList.add('active');
            } else {
                document.querySelector('.admin-tab:last-child').classList.add('active');
                document.getElementById('adminSettings').classList.add('active');
            }
        }

        function viewUser(userId) {
            // Kullanıcı detaylarını göster
        }

        function banUser(userId, username) {
            document.getElementById('banUserId').value = userId;
            document.getElementById('banUsername').value = username;
            document.getElementById('banModal').classList.add('active');
        }

        function confirmBan() {
            const userId = document.getElementById('banUserId').value;
            const reason = document.getElementById('banReason').value;
            const duration = document.getElementById('banDuration').value;

            const formData = new FormData();
            formData.append('admin_action', 'ban_user');
            formData.append('user_id', userId);
            formData.append('reason', reason);
            formData.append('duration', duration);

            fetch('', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Kullanıcı banlandı!');
                        location.reload();
                    }
                });

            closeModal('banModal');
        }

        function changeRole(userId, username) {
            document.getElementById('roleUserId').value = userId;
            document.getElementById('roleUsername').value = username;
            document.getElementById('roleModal').classList.add('active');
        }

        function confirmRole() {
            const userId = document.getElementById('roleUserId').value;
            const role = document.getElementById('newRole').value;

            const formData = new FormData();
            formData.append('admin_action', 'change_role');
            formData.append('user_id', userId);
            formData.append('role', role);

            fetch('', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Yetki değiştirildi!');
                        location.reload();
                    }
                });

            closeModal('roleModal');
        }

        function deleteUser(userId, username) {
            if (confirm(username + ' kullanıcısını silmek istediğinize emin misiniz?')) {
                const formData = new FormData();
                formData.append('admin_action', 'delete_user');
                formData.append('user_id', userId);

                fetch('', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Kullanıcı silindi!');
                            location.reload();
                        }
                    });
            }
        }

        function clearOldLogs() {
            if (confirm('30 günden eski logları silmek istediğinize emin misiniz?')) {
                const formData = new FormData();
                formData.append('admin_action', 'clear_logs');
                formData.append('days', '30');

                fetch('', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.deleted + ' log silindi!');
                            location.reload();
                        }
                    });
            }
        }

        function generateAPIKey() {
            const formData = new FormData();
            formData.append('admin_action', 'generate_api_key');
            formData.append('user_id', <?= $kullanici['id'] ?>);

            fetch('', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('API Anahtarı: ' + data.api_key);
                    }
                });
        }
        <?php endif; ?>

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
            setType('tc1');
            loadRecent();
        });
    </script>
    <?php endif; ?>
</body>
</html>
