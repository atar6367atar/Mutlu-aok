<?php
session_start();

// =============================================
// NGB SORGU PANELİM.IO - JOKER ULTIMATE PANEL
// TÜM ÖZELLİKLER AKTİF - REKLAM TEMİZLEME AKTİF
// TELEGRAM ENTEGRASYONU - GİRİŞ KAYITLARI
// Şifre: @ngbwayfite
// =============================================

define('SIFRE', '@ngbwayfite');
define('BOT_TOKEN', '8588404115:AAG7BD9FebTCIy-3VR7h4byCidwDcrIZXWw');
define('CHAT_ID', '8444268448'); // Logların gideceği chat ID

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Login
if (isset($_POST['login'])) {
    if ($_POST['password'] === SIFRE) {
        $_SESSION['loggedin'] = true;
        
        // Giriş kaydını Telegram'a gönder
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $tarih = date('Y-m-d H:i:s');
        
        $mesaj = "🔐 *YENİ GİRİŞ*\n\n";
        $mesaj .= "👤 *Kullanıcı:* Panel Admin\n";
        $mesaj .= "🌐 *IP:* `$ip`\n";
        $mesaj .= "📱 *Cihaz:* $user_agent\n";
        $mesaj .= "🕒 *Tarih:* $tarih\n";
        
        file_get_contents("https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage?chat_id=" . CHAT_ID . "&text=" . urlencode($mesaj) . "&parse_mode=Markdown");
    } else {
        $hata = "Hatalı şifre!";
        
        // Başarısız giriş denemesini kaydet
        $ip = $_SERVER['REMOTE_ADDR'];
        $tarih = date('Y-m-d H:i:s');
        
        $mesaj = "⚠️ *BAŞARISIZ GİRİŞ DENEMESİ*\n\n";
        $mesaj .= "🌐 *IP:* `$ip`\n";
        $mesaj .= "🕒 *Tarih:* $tarih\n";
        
        file_get_contents("https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage?chat_id=" . CHAT_ID . "&text=" . urlencode($mesaj) . "&parse_mode=Markdown");
    }
}

// Sorgu kaydı
if (isset($_POST['sorgu_kaydet']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $sorgu_tipi = $_POST['sorgu_tipi'];
    $sorgu_parametre = $_POST['sorgu_parametre'];
    $sonuc = $_POST['sonuc'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $tarih = date('Y-m-d H:i:s');
    
    $mesaj = "🔍 *YENİ SORGU*\n\n";
    $mesaj .= "📌 *Tip:* $sorgu_tipi\n";
    $mesaj .= "🔎 *Parametre:* `$sorgu_parametre`\n";
    $mesaj .= "🌐 *IP:* `$ip`\n";
    $mesaj .= "🕒 *Tarih:* $tarih\n";
    $mesaj .= "📊 *Sonuç:* \n```" . substr($sonuc, 0, 500) . "```";
    
    file_get_contents("https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage?chat_id=" . CHAT_ID . "&text=" . urlencode($mesaj) . "&parse_mode=Markdown");
    
    echo json_encode(['success' => true]);
    exit;
}

$giris_yapildi = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGB SORGU PANELİM.IO | JOKER ULTIMATE PANEL</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --joker-purple: #8b5cf6;
            --joker-dark: #2e1065;
            --joker-darker: #1e0b36;
            --joker-light: #c4b5fd;
            --joker-glow: #a78bfa;
            --joker-green: #10b981;
            --joker-red: #ef4444;
            --joker-yellow: #f59e0b;
            --joker-blue: #3b82f6;
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background: linear-gradient(135deg, #000, var(--joker-darker), var(--joker-dark));
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            animation: bgPulse 15s ease-in-out infinite;
        }

        @keyframes bgPulse {
            0%, 100% { background-size: 100% 100%; }
            50% { background-size: 150% 150%; }
        }

        /* Floating Cards with Enhanced Animation */
        .floating-card {
            position: fixed;
            font-size: 80px;
            color: var(--joker-purple);
            opacity: 0.1;
            pointer-events: none;
            z-index: 1;
            animation: float 20s ease-in-out infinite;
            filter: drop-shadow(0 0 30px var(--joker-glow));
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            25% { transform: translateY(-100px) rotate(10deg) scale(1.1); }
            50% { transform: translateY(-200px) rotate(20deg) scale(1.2); }
            75% { transform: translateY(-100px) rotate(10deg) scale(1.1); }
        }

        .card1 { top: 5%; left: 5%; animation-delay: 0s; }
        .card2 { top: 15%; right: 10%; animation-delay: 3s; }
        .card3 { bottom: 10%; left: 15%; animation-delay: 6s; }
        .card4 { bottom: 20%; right: 20%; animation-delay: 9s; }
        .card5 { top: 50%; left: 50%; animation-delay: 12s; }
        .card6 { top: 30%; left: 30%; animation-delay: 15s; }
        .card7 { bottom: 40%; right: 40%; animation-delay: 18s; }

        /* Particle Background */
        .particle {
            position: fixed;
            width: 4px;
            height: 4px;
            background: var(--joker-light);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            animation: particle 15s linear infinite;
            opacity: 0.3;
        }

        @keyframes particle {
            0% { transform: translateY(100vh) translateX(0) scale(1); opacity: 0; }
            10% { opacity: 0.8; transform: scale(1.5); }
            90% { opacity: 0.8; transform: scale(1.5); }
            100% { transform: translateY(-100vh) translateX(200px) scale(0); opacity: 0; }
        }

        /* Hamburger Menu */
        .menu-toggle {
            position: fixed;
            top: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            z-index: 1000;
            border: 2px solid white;
            box-shadow: 0 0 30px var(--joker-glow);
            animation: pulse 2s ease-in-out infinite;
            transition: all 0.3s;
        }

        .menu-toggle:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .menu-toggle span {
            width: 30px;
            height: 4px;
            background: white;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(8px, 8px);
        }

        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(8px, -8px);
        }

        /* Side Menu */
        .side-menu {
            position: fixed;
            top: 0;
            left: -400px;
            width: 350px;
            height: 100vh;
            background: rgba(46, 16, 101, 0.98);
            backdrop-filter: blur(20px);
            z-index: 999;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-right: 3px solid var(--joker-purple);
            box-shadow: 0 0 50px var(--joker-glow);
            overflow-y: auto;
            padding: 100px 20px 30px;
        }

        .side-menu.active {
            left: 0;
        }

        .menu-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--joker-purple);
        }

        .menu-header i {
            font-size: 60px;
            color: var(--joker-light);
            animation: pulse 2s ease-in-out infinite;
        }

        .menu-header h2 {
            color: white;
            font-size: 24px;
            margin-top: 15px;
        }

        .menu-category {
            margin-bottom: 25px;
        }

        .menu-category-title {
            color: var(--joker-light);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
            padding-left: 15px;
            border-left: 4px solid var(--joker-purple);
            animation: glow 2s ease-in-out infinite;
        }

        .menu-items {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .menu-item {
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--joker-purple);
            border-radius: 15px;
            padding: 12px;
            color: white;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 12px;
            font-weight: 600;
            animation: slideIn 0.5s ease-out;
        }

        .menu-item:hover {
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 20px rgba(139,92,246,0.3);
        }

        .menu-item i {
            display: block;
            font-size: 20px;
            margin-bottom: 5px;
        }

        @keyframes glow {
            0% { filter: drop-shadow(0 0 5px var(--joker-purple)); }
            50% { filter: drop-shadow(0 0 40px var(--joker-light)); }
            100% { filter: drop-shadow(0 0 5px var(--joker-purple)); }
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 30px var(--joker-purple); }
            50% { transform: scale(1.05); box-shadow: 0 0 60px var(--joker-glow); }
            100% { transform: scale(1); box-shadow: 0 0 30px var(--joker-purple); }
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

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Login */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 10;
            animation: slideIn 1s ease-out;
        }

        .login-card {
            background: rgba(46, 16, 101, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 60px;
            padding: 70px 60px;
            width: 100%;
            max-width: 550px;
            border: 3px solid var(--joker-purple);
            box-shadow: 0 0 100px var(--joker-glow);
            animation: glow 3s ease-in-out infinite, pulse 3s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(139, 92, 246, 0.1),
                transparent
            );
            animation: rotate 15s linear infinite;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            z-index: 1;
        }

        .login-logo i {
            font-size: 120px;
            color: var(--joker-light);
            animation: pulse 2s ease-in-out infinite, glow 2s ease-in-out infinite;
        }

        .login-logo h1 {
            font-size: 52px;
            color: white;
            text-shadow: 0 0 40px var(--joker-purple);
            margin: 20px 0;
            animation: glow 2s ease-in-out infinite;
        }

        .login-logo p {
            color: var(--joker-light);
            font-size: 16px;
            letter-spacing: 4px;
        }

        .login-input-group {
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .login-input-group input {
            width: 100%;
            padding: 20px 30px;
            background: rgba(255,255,255,0.1);
            border: 3px solid var(--joker-purple);
            border-radius: 40px;
            color: white;
            font-size: 18px;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s;
        }

        .login-input-group input:focus {
            outline: none;
            border-color: var(--joker-light);
            box-shadow: 0 0 60px var(--joker-glow);
            transform: scale(1.02);
        }

        .login-input-group input::placeholder {
            color: rgba(255,255,255,0.5);
        }

        .login-btn {
            width: 100%;
            padding: 20px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border: none;
            border-radius: 40px;
            color: white;
            font-size: 20px;
            font-weight: 800;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 3px;
            animation: pulse 2s ease-in-out infinite;
            font-family: 'Orbitron', sans-serif;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(139, 92, 246, 0.5);
        }

        /* Dashboard */
        .dashboard {
            padding: 30px 30px 30px 100px;
            position: relative;
            z-index: 10;
        }

        .header {
            background: rgba(46, 16, 101, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 50px;
            padding: 25px 40px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 3px solid var(--joker-purple);
            box-shadow: 0 0 60px var(--joker-glow);
            animation: slideIn 0.5s ease-out;
        }

        .header h1 {
            color: white;
            font-size: 36px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header h1 i {
            color: var(--joker-light);
            animation: pulse 2s ease-in-out infinite;
        }

        .badge {
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            padding: 12px 30px;
            border-radius: 50px;
            color: white;
            font-weight: 700;
            font-size: 16px;
            animation: glow 2s ease-in-out infinite;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.3);
            color: white;
            border: 2px solid #ef4444;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 16px;
        }

        .logout-btn:hover {
            background: #ef4444;
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 30px rgba(239,68,68,0.3);
        }

        /* Cards */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
            animation: slideIn 0.7s ease-out;
        }

        .card {
            background: rgba(46, 16, 101, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 35px;
            padding: 30px 20px;
            border: 3px solid var(--joker-purple);
            transition: all 0.3s;
            cursor: pointer;
            text-align: center;
            animation: slideIn 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(139, 92, 246, 0.2),
                transparent
            );
            transition: all 0.5s;
        }

        .card:hover::before {
            transform: rotate(180deg);
        }

        .card:hover {
            transform: translateY(-15px) scale(1.05);
            border-color: var(--joker-light);
            box-shadow: 0 30px 60px rgba(139, 92, 246, 0.5);
        }

        .card-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 3s ease-in-out infinite;
        }

        .card-icon i {
            font-size: 32px;
            color: white;
        }

        .card h3 {
            color: white;
            font-size: 16px;
            font-weight: 700;
        }

        /* Query */
        .query-box {
            background: rgba(46, 16, 101, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 50px;
            padding: 40px;
            margin-bottom: 30px;
            border: 3px solid var(--joker-purple);
            animation: slideIn 0.9s ease-out;
        }

        .query-header {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 3px solid var(--joker-purple);
        }

        .query-header i {
            font-size: 60px;
            color: var(--joker-light);
            animation: pulse 2s ease-in-out infinite, glow 2s ease-in-out infinite;
        }

        .query-header h2 {
            color: white;
            font-size: 32px;
            font-weight: 700;
        }

        .input-group {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .input-group input {
            flex: 1;
            padding: 22px 35px;
            background: rgba(255,255,255,0.1);
            border: 3px solid var(--joker-purple);
            border-radius: 40px;
            color: white;
            font-size: 18px;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--joker-light);
            box-shadow: 0 0 60px var(--joker-glow);
            transform: scale(1.02);
        }

        .input-group input::placeholder {
            color: rgba(255,255,255,0.5);
        }

        .input-group button {
            padding: 22px 50px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border: none;
            border-radius: 40px;
            color: white;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 18px;
            position: relative;
            overflow: hidden;
        }

        .input-group button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .input-group button:hover::before {
            left: 100%;
        }

        .input-group button:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 30px 60px rgba(139, 92, 246, 0.5);
        }

        .input-group button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .example {
            color: var(--joker-light);
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: glow 2s ease-in-out infinite;
        }

        /* Loader */
        .loader {
            display: none;
            text-align: center;
            padding: 50px;
        }

        .spinner {
            width: 70px;
            height: 70px;
            border: 5px solid var(--joker-purple);
            border-top-color: var(--joker-light);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        .spinner-pulse {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border-radius: 50%;
            margin: 0 auto 20px;
            animation: pulse 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loader p {
            color: white;
            font-size: 18px;
            animation: glow 1s ease-in-out infinite;
        }

        /* Result */
        .result {
            background: rgba(0,0,0,0.4);
            border-radius: 40px;
            padding: 30px;
            margin-top: 30px;
            border: 3px solid var(--joker-purple);
            display: none;
            animation: slideIn 0.5s ease-out;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .result-header h3 {
            color: var(--joker-green);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
        }

        .result-actions {
            display: flex;
            gap: 15px;
        }

        .result-actions button {
            padding: 10px 20px;
            background: rgba(255,255,255,0.1);
            border: 2px solid var(--joker-purple);
            border-radius: 20px;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 16px;
        }

        .result-actions button:hover {
            background: var(--joker-purple);
            transform: scale(1.1) rotate(5deg);
        }

        .result-content {
            background: rgba(0,0,0,0.6);
            border-radius: 25px;
            padding: 30px;
            font-family: monospace;
            font-size: 14px;
            color: var(--joker-light);
            max-height: 600px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
            border: 2px solid var(--joker-purple);
        }

        .result-content.small {
            background: rgba(16, 185, 129, 0.15);
            border-left: 5px solid var(--joker-green);
            font-size: 15px;
        }

        /* Recent */
        .recent {
            background: rgba(46, 16, 101, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 50px;
            padding: 40px;
            border: 3px solid var(--joker-purple);
            animation: slideIn 1s ease-out;
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .recent-header h2 {
            color: white;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 28px;
        }

        .clear-btn {
            padding: 12px 25px;
            background: rgba(239, 68, 68, 0.3);
            border: 2px solid #ef4444;
            border-radius: 30px;
            color: white;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s;
            font-size: 16px;
        }

        .clear-btn:hover {
            background: #ef4444;
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 15px 30px rgba(239,68,68,0.3);
        }

        .recent-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .recent-item {
            background: rgba(0,0,0,0.4);
            border-radius: 25px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            animation: slideIn 1.1s ease-out;
        }

        .recent-item:hover {
            background: rgba(139, 92, 246, 0.3);
            transform: translateX(15px) scale(1.02);
            border-color: var(--joker-purple);
            box-shadow: 0 15px 30px rgba(139,92,246,0.3);
        }

        .recent-type {
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            color: white;
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .recent-param {
            color: white;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 16px;
            word-break: break-all;
        }

        .recent-time {
            color: rgba(255,255,255,0.5);
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard {
                padding: 30px 20px;
            }
            
            .menu-toggle {
                top: 20px;
                left: 20px;
                width: 50px;
                height: 50px;
            }
            
            .side-menu {
                width: 100%;
                left: -100%;
            }
            
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .input-group {
                flex-direction: column;
            }
            
            .query-header {
                flex-direction: column;
                text-align: center;
            }
            
            .grid {
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            }
            
            .login-card {
                padding: 40px 30px;
            }
            
            .login-logo h1 {
                font-size: 32px;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        ::-webkit-scrollbar-track {
            background: var(--joker-darker);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--joker-purple);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--joker-light);
        }

        /* Success/Error Animations */
        @keyframes success {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        .success-icon {
            color: var(--joker-green);
            animation: success 0.5s ease-in-out;
        }

        .error-icon {
            color: var(--joker-red);
            animation: success 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <?php
    // Rastgele partiküller oluştur
    for ($i = 0; $i < 100; $i++): 
        $left = rand(0, 100);
        $delay = rand(0, 30);
        $duration = rand(10, 25);
        $size = rand(2, 6);
    ?>
    <div class="particle" style="left: <?= $left ?>%; animation-delay: <?= $delay ?>s; animation-duration: <?= $duration ?>s; width: <?= $size ?>px; height: <?= $size ?>px;"></div>
    <?php endfor; ?>

    <div class="floating-card card1">♠</div>
    <div class="floating-card card2">♣</div>
    <div class="floating-card card3">♥</div>
    <div class="floating-card card4">♦</div>
    <div class="floating-card card5">🃏</div>
    <div class="floating-card card6">♠</div>
    <div class="floating-card card7">♣</div>

    <?php if (!$giris_yapildi): ?>
    <!-- LOGIN -->
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-crown"></i>
                <h1>NGB SORGU</h1>
                <p>ULTIMATE PANEL</p>
            </div>
            
            <?php if (isset($hata)): ?>
                <div style="background: rgba(239,68,68,0.3); color: white; padding: 15px; border-radius: 25px; margin-bottom: 25px; text-align: center; animation: slideIn 0.5s ease-out;">
                    <i class="fas fa-exclamation-triangle error-icon"></i> <?= $hata ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="login-input-group">
                    <input type="password" name="password" placeholder="ŞİFRE" required>
                </div>
                <button type="submit" name="login" class="login-btn">GİRİŞ</button>
            </form>
            
            <div style="text-align: center; margin-top: 30px; color: var(--joker-light); font-size: 14px; animation: glow 2s ease-in-out infinite;">
                Şifre: @ngbwayfite
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Hamburger Menu Toggle -->
    <div class="menu-toggle" id="menuToggle" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- Side Menu -->
    <div class="side-menu" id="sideMenu">
        <div class="menu-header">
            <i class="fas fa-crown"></i>
            <h2>SORGU MENÜSÜ</h2>
        </div>
        
        <!-- TC Kategorisi -->
        <div class="menu-category">
            <div class="menu-category-title">🔴 TC SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('tc1')"><i class="fas fa-id-card"></i> TC-1</div>
                <div class="menu-item" onclick="setTypeAndClose('tc2')"><i class="fas fa-id-card"></i> TC-2</div>
                <div class="menu-item" onclick="setTypeAndClose('tcgsm')"><i class="fas fa-mobile-alt"></i> TC'den GSM</div>
                <div class="menu-item" onclick="setTypeAndClose('okulno')"><i class="fas fa-graduation-cap"></i> Okul No</div>
            </div>
        </div>
        
        <!-- AİLE Kategorisi -->
        <div class="menu-category">
            <div class="menu-category-title">👪 AİLE SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('aile')"><i class="fas fa-users"></i> Aile</div>
                <div class="menu-item" onclick="setTypeAndClose('aile_pro')"><i class="fas fa-users"></i> Aile Pro</div>
                <div class="menu-item" onclick="setTypeAndClose('sulale')"><i class="fas fa-tree"></i> Sülale</div>
            </div>
        </div>
        
        <!-- İSİM Kategorisi -->
        <div class="menu-category">
            <div class="menu-category-title">👤 İSİM SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('isim')"><i class="fas fa-user"></i> İsim</div>
                <div class="menu-item" onclick="setTypeAndClose('isim_pro')"><i class="fas fa-user"></i> İsim Pro</div>
                <div class="menu-item" onclick="setTypeAndClose('isim_il')"><i class="fas fa-map-marker-alt"></i> İsim+İlçe</div>
            </div>
        </div>
        
        <!-- ADRES Kategorisi -->
        <div class="menu-category">
            <div class="menu-category-title">🏠 ADRES SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('adres')"><i class="fas fa-map-marker-alt"></i> Adres</div>
                <div class="menu-item" onclick="setTypeAndClose('adres_pro')"><i class="fas fa-map-marker-alt"></i> Adres Pro</div>
            </div>
        </div>
        
        <!-- İŞ Kategorisi -->
        <div class="menu-category">
            <div class="menu-category-title">💼 İŞ SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('isyeri')"><i class="fas fa-briefcase"></i> İş Yeri</div>
                <div class="menu-item" onclick="setTypeAndClose('isyeri_ark')"><i class="fas fa-users"></i> İş Arkadaş</div>
            </div>
        </div>
        
        <!-- GSM Kategorisi -->
        <div class="menu-category">
            <div class="menu-category-title">📱 GSM SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('gsmtc')"><i class="fas fa-mobile-alt"></i> GSM>TC</div>
                <div class="menu-item" onclick="setTypeAndClose('gncloperator')"><i class="fas fa-signal"></i> Operatör</div>
            </div>
        </div>
        
        <!-- FİNANS Kategorisi -->
        <div class="menu-category">
            <div class="menu-category-title">💰 FİNANS</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('iban')"><i class="fas fa-coins"></i> IBAN</div>
            </div>
        </div>
    </div>

    <!-- DASHBOARD -->
    <div class="dashboard">
        <div class="header">
            <h1>
                <i class="fas fa-crown"></i>
                NGB SORGU PANELİ
            </h1>
            <div style="display: flex; gap: 20px; align-items: center;">
                <div class="badge">ULTIMATE PRO</div>
                <a href="?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> ÇIKIŞ</a>
            </div>
        </div>
        
        <!-- Kategoriler -->
        <div class="grid">
            <div class="card" onclick="setCategory('tc')">
                <div class="card-icon"><i class="fas fa-id-card"></i></div>
                <h3>TC SORGULARI</h3>
            </div>
            <div class="card" onclick="setCategory('aile')">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <h3>AİLE SORGULARI</h3>
            </div>
            <div class="card" onclick="setCategory('isim')">
                <div class="card-icon"><i class="fas fa-user"></i></div>
                <h3>İSİM SORGULARI</h3>
            </div>
            <div class="card" onclick="setCategory('adres')">
                <div class="card-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3>ADRES SORGULARI</h3>
            </div>
            <div class="card" onclick="setCategory('is')">
                <div class="card-icon"><i class="fas fa-briefcase"></i></div>
                <h3>İŞ SORGULARI</h3>
            </div>
            <div class="card" onclick="setCategory('gsm')">
                <div class="card-icon"><i class="fas fa-mobile-alt"></i></div>
                <h3>GSM SORGULARI</h3>
            </div>
            <div class="card" onclick="setCategory('finans')">
                <div class="card-icon"><i class="fas fa-coins"></i></div>
                <h3>FİNANS</h3>
            </div>
        </div>
        
        <!-- Sorgu -->
        <div class="query-box">
            <div class="query-header">
                <i class="fas fa-id-card" id="queryIcon"></i>
                <h2 id="queryTitle">TC Sorgulama</h2>
                <div class="badge" id="queryBadge">tc</div>
            </div>
            
            <div class="input-group">
                <input type="text" id="queryParam" placeholder="Parametre girin..." onkeypress="if(event.key==='Enter') executeQuery()">
                <button onclick="executeQuery()" id="queryBtn">
                    <i class="fas fa-search"></i> SORGULA
                </button>
            </div>
            
            <div class="example" id="queryExample">
                <i class="fas fa-info-circle"></i> Örnek: 11111111110
            </div>
            
            <!-- Loader -->
            <div class="loader" id="queryLoader">
                <div class="spinner-pulse"></div>
                <div class="spinner"></div>
                <p>Sorgulanıyor...</p>
            </div>
            
            <!-- Sonuç -->
            <div class="result" id="resultContainer">
                <div class="result-header">
                    <h3><i class="fas fa-check-circle success-icon"></i> SONUÇ</h3>
                    <div class="result-actions">
                        <button onclick="copyResult()" id="copyBtn" title="Panoya Kopyala"><i class="fas fa-copy"></i></button>
                        <button onclick="downloadResult()" id="downloadBtn" title="TXT Olarak İndir"><i class="fas fa-download"></i></button>
                    </div>
                </div>
                <div class="result-content" id="resultContent"></div>
            </div>
        </div>
        
        <!-- Son Sorgular -->
        <div class="recent">
            <div class="recent-header">
                <h2><i class="fas fa-history"></i> SON SORGULAR</h2>
                <button class="clear-btn" onclick="clearRecent()"><i class="fas fa-trash"></i> TEMİZLE</button>
            </div>
            <div class="recent-grid" id="recentGrid"></div>
        </div>
    </div>

    <!-- Proxy PHP dosyası -->
    <script>
        // API'ler
        const apiList = {
            // TC Kategorisi
            'tc1': { name: 'TC Sorgu-1 (Temel)', icon: 'fa-id-card', example: '11111111110', badge: 'TC-1', category: 'tc', endpoint: '/apiservices/tc.php', params: ['tc'] },
            'tc2': { name: 'TC Sorgu-2 (Profesyonel)', icon: 'fa-id-card', example: '11111111110', badge: 'TC-2', category: 'tc', endpoint: '/apiservices/tcpro.php', params: ['tc'] },
            
            // İSİM Kategorisi
            'isim': { name: 'İsim Sorgu', icon: 'fa-user', example: 'roket atar', badge: 'İSİM', category: 'isim', endpoint: '/apiservices/adsoyad.php', params: ['ad', 'soyad'] },
            'isim_pro': { name: 'İsim Profesyonel', icon: 'fa-user', example: 'roket atar bursa', badge: 'İSİM PRO', category: 'isim', endpoint: '/apiservices/adsoyadpro.php', params: ['ad', 'soyad', 'il'] },
            'isim_il': { name: 'İsim + İl/İlçe', icon: 'fa-user', example: 'roket bursa osmangazi', badge: 'İSİM+İL', category: 'isim', endpoint: '/apiservices/adililce.php', params: ['ad', 'il', 'ilce'] },
            
            // AİLE Kategorisi
            'aile': { name: 'Aile Sorgu', icon: 'fa-users', example: '11111111110', badge: 'AİLE', category: 'aile', endpoint: '/apiservices/aile.php', params: ['tc'] },
            'aile_pro': { name: 'Aile Profesyonel', icon: 'fa-users', example: '11111111110', badge: 'AİLE PRO', category: 'aile', endpoint: '/apiservices/ailepro.php', params: ['tc'] },
            'sulale': { name: 'Sülale Sorgu', icon: 'fa-tree', example: '11111111110', badge: 'SÜLALE', category: 'aile', endpoint: '/apiservices/sulale.php', params: ['tc'] },
            
            // ADRES Kategorisi
            'adres': { name: 'Adres Sorgu', icon: 'fa-map-marker-alt', example: '11111111110', badge: 'ADRES', category: 'adres', endpoint: '/apiservices/adres.php', params: ['tc'] },
            'adres_pro': { name: 'Adres Profesyonel', icon: 'fa-map-marker-alt', example: '11144576054', badge: 'ADRES PRO', category: 'adres', endpoint: '/apiservices/adrespro.php', params: ['tc'] },
            
            // İŞ Kategorisi
            'isyeri': { name: 'İşyeri Sorgu', icon: 'fa-briefcase', example: '11144576054', badge: 'İŞYERİ', category: 'is', endpoint: '/apiservices/isyeri.php', params: ['tc'] },
            'isyeri_ark': { name: 'İş Arkadaşları', icon: 'fa-users', example: '11144576054', badge: 'İŞ ARK', category: 'is', endpoint: '/apiservices/isyeriark.php', params: ['tc'] },
            
            // GSM Kategorisi
            'gncloperator': { name: 'Güncel Operatör', icon: 'fa-signal', example: '5415722525', badge: 'OPERATÖR', category: 'gsm', endpoint: '/apiservices/gncloperator.php', params: ['numara'] },
            'tcgsm': { name: 'TC\'den GSM', icon: 'fa-mobile-alt', example: '11111111110', badge: 'TC>GSM', category: 'gsm', endpoint: '/apiservices/tcgsm.php', params: ['tc'] },
            'gsmtc': { name: 'GSM\'den TC', icon: 'fa-mobile-alt', example: '5415722525', badge: 'GSM>TC', category: 'gsm', endpoint: '/apiservices/gsmtc.php', params: ['gsm'] },
            
            // FİNANS Kategorisi
            'iban': { name: 'IBAN Sorgulama', icon: 'fa-coins', example: 'TR280006256953335759003718', badge: 'IBAN', category: 'finans', endpoint: '/apiservices/iban.php', params: ['iban'] }
        };

        let currentType = 'tc1';
        let currentCategory = 'tc';
        let recentQueries = JSON.parse(localStorage.getItem('recentQueries')) || [];

        function toggleMenu() {
            const menu = document.getElementById('sideMenu');
            const toggle = document.getElementById('menuToggle');
            menu.classList.toggle('active');
            toggle.classList.toggle('active');
        }

        function setTypeAndClose(type) {
            setType(type);
            toggleMenu();
        }

        function setCategory(cat) {
            currentCategory = cat;
            const first = Object.values(apiList).find(api => api.category === cat);
            if (first) {
                const typeKey = Object.keys(apiList).find(key => apiList[key] === first);
                setType(typeKey);
            }
            toggleMenu();
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

        // Veri temizleme fonksiyonu
        function cleanData(data) {
            // Reklam içeren anahtarları temizle
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
                                // Metin içindeki reklamları temizle
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

            document.getElementById('queryLoader').style.display = 'block';
            document.getElementById('resultContainer').style.display = 'none';
            document.getElementById('queryBtn').disabled = true;

            const timeout = setTimeout(() => {
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Zaman aşımı! Lütfen tekrar deneyin.');
            }, 30000);

            try {
                // Parametreleri hazırla
                const params = param.split(' ');
                if (params.length < api.params.length) {
                    alert(`Eksik parametre! ${api.params.length} parametre girmelisiniz.`);
                    document.getElementById('queryLoader').style.display = 'none';
                    document.getElementById('queryBtn').disabled = false;
                    clearTimeout(timeout);
                    return;
                }

                const queryParams = new URLSearchParams();
                for (let i = 0; i < api.params.length; i++) {
                    queryParams.append(api.params[i], params[i]);
                }

                const url = `https://punisherapi.alwaysdata.net${api.endpoint}?${queryParams.toString()}`;
                const response = await fetch(url);
                clearTimeout(timeout);

                if (!response.ok) {
                    throw new Error(`API yanıt vermiyor (HTTP ${response.status})`);
                }

                const data = await response.json();

                // Veriyi temizle
                const cleanedData = cleanData(data);

                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;

                const resultContent = document.getElementById('resultContent');
                const resultStr = JSON.stringify(cleanedData, null, 2);
                resultContent.textContent = resultStr;

                if (resultStr.length < 1000) {
                    resultContent.classList.add('small');
                } else {
                    resultContent.classList.remove('small');
                }

                document.getElementById('resultContainer').style.display = 'block';

                // Sorguyu kaydet
                recentQueries.unshift({
                    type: api.name,
                    param: param,
                    time: new Date().toLocaleString('tr-TR')
                });

                if (recentQueries.length > 10) recentQueries.pop();
                localStorage.setItem('recentQueries', JSON.stringify(recentQueries));
                loadRecent();

                // Telegram'a gönder
                const formData = new FormData();
                formData.append('sorgu_kaydet', '1');
                formData.append('sorgu_tipi', api.name);
                formData.append('sorgu_parametre', param);
                formData.append('sonuc', resultStr);

                fetch('', {
                    method: 'POST',
                    body: formData
                });

            } catch (error) {
                clearTimeout(timeout);
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Bağlantı hatası: ' + error.message);
            }
        }

        function copyResult() {
            const content = document.getElementById('resultContent').textContent;
            navigator.clipboard.writeText(content).then(() => {
                alert('Sonuç panoya kopyalandı!');
            }).catch(err => {
                alert('Kopyalama başarısız: ' + err);
            });
        }

        function downloadResult() {
            const content = document.getElementById('resultContent').textContent;
            const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `ngb_sorgu_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function loadRecent() {
            const grid = document.getElementById('recentGrid');
            if (!recentQueries || recentQueries.length === 0) {
                grid.innerHTML = '<p style="color: rgba(255,255,255,0.5); text-align: center; grid-column: 1/-1;">Henüz sorgu yapılmadı.</p>';
                return;
            }

            grid.innerHTML = recentQueries.map((q, index) => `
                <div class="recent-item" onclick="recentQueryClick('${q.param}', '${q.type}')" title="Tekrar sorgula: ${q.param}">
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
             } else {
                 document.getElementById('queryParam').value = param;
                 alert('Tip bilgisi bulunamadı, parametre inputa yazıldı.');
             }
        }

        function clearRecent() {
            if (confirm('Tüm son sorgular temizlensin mi?')) {
                recentQueries = [];
                localStorage.removeItem('recentQueries');
                loadRecent();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setType('tc1');
            loadRecent();
        });

        // Menü dışına tıklandığında kapat
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('sideMenu');
            const toggle = document.getElementById('menuToggle');
            if (!menu.contains(event.target) && !toggle.contains(event.target) && menu.classList.contains('active')) {
                menu.classList.remove('active');
                toggle.classList.remove('active');
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
