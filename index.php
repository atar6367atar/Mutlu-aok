<?php
session_start();

// =============================================
// NGB SORGU PANELİM.IO - JOKER ULTIMATE PANEL
// OPTİMİZE EDİLMİŞ - AKICI ANİMASYONLAR
// Şifre: @ngbwayfite
// =============================================

define('SIFRE', '@ngbwayfite');
define('BOT_TOKEN', '8588404115:AAG7BD9FebTCIy-3VR7h4byCidwDcrIZXWw');
define('CHAT_ID', '8444268448');

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
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $tarih = date('Y-m-d H:i:s');
        
        $mesaj = "🔐 *YENİ GİRİŞ*\n\n";
        $mesaj .= "👤 *Kullanıcı:* Panel Admin\n";
        $mesaj .= "🌐 *IP:* `$ip`\n";
        $mesaj .= "📱 *Cihaz:* $user_agent\n";
        $mesaj .= "🕒 *Tarih:* $tarih\n";
        
        @file_get_contents("https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage?chat_id=" . CHAT_ID . "&text=" . urlencode($mesaj) . "&parse_mode=Markdown");
    } else {
        $hata = "Hatalı şifre!";
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $tarih = date('Y-m-d H:i:s');
        
        $mesaj = "⚠️ *BAŞARISIZ GİRİŞ DENEMESİ*\n\n";
        $mesaj .= "🌐 *IP:* `$ip`\n";
        $mesaj .= "🕒 *Tarih:* $tarih\n";
        
        @file_get_contents("https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage?chat_id=" . CHAT_ID . "&text=" . urlencode($mesaj) . "&parse_mode=Markdown");
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
    
    @file_get_contents("https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage?chat_id=" . CHAT_ID . "&text=" . urlencode($mesaj) . "&parse_mode=Markdown");
    
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
    <title>NGB SORGU PANELİ | JOKER ULTIMATE</title>
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
            --joker-darker: #1a0b2e;
            --joker-light: #c4b5fd;
            --joker-glow: #a78bfa;
            --joker-green: #10b981;
            --joker-red: #ef4444;
            --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-medium: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background: linear-gradient(135deg, #000000, var(--joker-darker), var(--joker-dark));
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Optimized Background Effects */
        .bg-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(196, 181, 253, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
            animation: bgShift 20s ease-in-out infinite alternate;
        }

        @keyframes bgShift {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(1.2); opacity: 1; }
        }

        /* Floating Cards - Optimized */
        .floating-card {
            position: fixed;
            font-size: 60px;
            color: var(--joker-purple);
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
            animation: float 25s ease-in-out infinite;
            transform: translateZ(0);
            will-change: transform;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            33% { transform: translateY(-50px) rotate(5deg) scale(1.1); }
            66% { transform: translateY(-30px) rotate(-5deg) scale(0.95); }
        }

        .card1 { top: 10%; left: 5%; animation-delay: 0s; }
        .card2 { top: 20%; right: 10%; animation-delay: 5s; }
        .card3 { bottom: 15%; left: 15%; animation-delay: 10s; }
        .card4 { bottom: 25%; right: 20%; animation-delay: 15s; }
        .card5 { top: 50%; left: 50%; animation-delay: 20s; }

        /* Particles - Optimized with transform3d */
        .particle {
            position: fixed;
            width: 2px;
            height: 2px;
            background: var(--joker-light);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.2;
            animation: particle 20s linear infinite;
            transform: translate3d(0, 0, 0);
            will-change: transform;
        }

        @keyframes particle {
            0% { transform: translate3d(0, 100vh, 0) scale(1); opacity: 0; }
            10% { opacity: 0.5; }
            90% { opacity: 0.5; }
            100% { transform: translate3d(100px, -100vh, 0) scale(0); opacity: 0; }
        }

        /* Glow Effect */
        @keyframes glow {
            0% { filter: drop-shadow(0 0 5px var(--joker-purple)); }
            50% { filter: drop-shadow(0 0 25px var(--joker-light)); }
            100% { filter: drop-shadow(0 0 5px var(--joker-purple)); }
        }

        /* Pulse Effect */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        /* Slide In */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translate3d(0, 30px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        /* Hamburger Menu - Optimized */
        .menu-toggle {
            position: fixed;
            top: 30px;
            left: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            z-index: 1000;
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 0 30px var(--joker-glow);
            transition: var(--transition-medium);
            transform: translateZ(0);
            will-change: transform;
        }

        .menu-toggle:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .menu-toggle span {
            width: 25px;
            height: 3px;
            background: white;
            border-radius: 3px;
            transition: var(--transition-medium);
            transform: translateZ(0);
        }

        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
            transform: scale(0);
        }

        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Side Menu - Optimized */
        .side-menu {
            position: fixed;
            top: 0;
            left: -350px;
            width: 320px;
            height: 100vh;
            background: rgba(26, 11, 46, 0.98);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 999;
            transition: left var(--transition-slow);
            border-right: 2px solid var(--joker-purple);
            box-shadow: 0 0 40px var(--joker-glow);
            overflow-y: auto;
            padding: 90px 20px 30px;
            transform: translateZ(0);
            will-change: left;
        }

        .side-menu.active {
            left: 0;
        }

        .menu-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--joker-purple);
            animation: slideIn 0.5s ease-out;
        }

        .menu-header i {
            font-size: 50px;
            color: var(--joker-light);
            animation: pulse 3s ease-in-out infinite;
        }

        .menu-header h2 {
            color: white;
            font-size: 20px;
            margin-top: 10px;
        }

        .menu-category {
            margin-bottom: 25px;
            animation: slideIn 0.5s ease-out;
            animation-fill-mode: both;
        }

        .menu-category:nth-child(2) { animation-delay: 0.1s; }
        .menu-category:nth-child(3) { animation-delay: 0.2s; }
        .menu-category:nth-child(4) { animation-delay: 0.3s; }
        .menu-category:nth-child(5) { animation-delay: 0.4s; }
        .menu-category:nth-child(6) { animation-delay: 0.5s; }
        .menu-category:nth-child(7) { animation-delay: 0.6s; }

        .menu-category-title {
            color: var(--joker-light);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            padding-left: 10px;
            border-left: 3px solid var(--joker-purple);
            letter-spacing: 1px;
        }

        .menu-items {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .menu-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--joker-purple);
            border-radius: 12px;
            padding: 10px 5px;
            color: white;
            text-align: center;
            cursor: pointer;
            transition: var(--transition-fast);
            font-size: 11px;
            font-weight: 600;
            transform: translateZ(0);
            will-change: transform;
        }

        .menu-item:hover {
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 20px rgba(139,92,246,0.3);
        }

        .menu-item i {
            display: block;
            font-size: 18px;
            margin-bottom: 4px;
        }

        /* Login - Optimized */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 10;
            animation: slideIn 0.8s ease-out;
        }

        .login-card {
            background: rgba(26, 11, 46, 0.98);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 40px;
            padding: 50px 40px;
            width: 100%;
            max-width: 450px;
            border: 2px solid var(--joker-purple);
            box-shadow: 0 0 60px var(--joker-glow);
            animation: glow 3s ease-in-out infinite;
            transform: translateZ(0);
            will-change: box-shadow;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo i {
            font-size: 80px;
            color: var(--joker-light);
            animation: pulse 2s ease-in-out infinite;
        }

        .login-logo h1 {
            font-size: 36px;
            color: white;
            text-shadow: 0 0 30px var(--joker-purple);
            margin: 15px 0;
        }

        .login-logo p {
            color: var(--joker-light);
            font-size: 13px;
            letter-spacing: 2px;
        }

        .login-input-group {
            margin-bottom: 25px;
        }

        .login-input-group input {
            width: 100%;
            padding: 16px 25px;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--joker-purple);
            border-radius: 30px;
            color: white;
            font-size: 15px;
            font-family: 'Orbitron', sans-serif;
            transition: var(--transition-fast);
            transform: translateZ(0);
        }

        .login-input-group input:focus {
            outline: none;
            border-color: var(--joker-light);
            box-shadow: 0 0 40px var(--joker-glow);
            transform: scale(1.01);
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: var(--transition-fast);
            font-family: 'Orbitron', sans-serif;
            transform: translateZ(0);
            will-change: transform;
        }

        .login-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 40px rgba(139, 92, 246, 0.4);
        }

        /* Dashboard - Optimized */
        .dashboard {
            padding: 30px 30px 30px 90px;
            position: relative;
            z-index: 10;
        }

        .header {
            background: rgba(26, 11, 46, 0.98);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 20px 30px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid var(--joker-purple);
            box-shadow: 0 0 40px var(--joker-glow);
            animation: slideIn 0.5s ease-out;
            transform: translateZ(0);
        }

        .header h1 {
            color: white;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header h1 i {
            color: var(--joker-light);
            animation: pulse 2s ease-in-out infinite;
        }

        .badge {
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            padding: 8px 20px;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 1px;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.2);
            color: white;
            border: 1px solid #ef4444;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            transition: var(--transition-fast);
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #ef4444;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(239,68,68,0.3);
        }

        /* Cards Grid - Optimized */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .card {
            background: rgba(26, 11, 46, 0.95);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border-radius: 25px;
            padding: 20px 15px;
            border: 2px solid var(--joker-purple);
            cursor: pointer;
            text-align: center;
            transition: var(--transition-fast);
            animation: slideIn 0.5s ease-out;
            animation-fill-mode: both;
            transform: translateZ(0);
            will-change: transform;
        }

        .card:nth-child(1) { animation-delay: 0.05s; }
        .card:nth-child(2) { animation-delay: 0.1s; }
        .card:nth-child(3) { animation-delay: 0.15s; }
        .card:nth-child(4) { animation-delay: 0.2s; }
        .card:nth-child(5) { animation-delay: 0.25s; }
        .card:nth-child(6) { animation-delay: 0.3s; }
        .card:nth-child(7) { animation-delay: 0.35s; }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: var(--joker-light);
            box-shadow: 0 20px 40px rgba(139, 92, 246, 0.3);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }

        .card-icon i {
            font-size: 24px;
            color: white;
        }

        .card h3 {
            color: white;
            font-size: 13px;
            font-weight: 600;
        }

        /* Query Box - Optimized */
        .query-box {
            background: rgba(26, 11, 46, 0.98);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 30px;
            margin-bottom: 25px;
            border: 2px solid var(--joker-purple);
            animation: slideIn 0.6s ease-out;
            transform: translateZ(0);
        }

        .query-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--joker-purple);
        }

        .query-header i {
            font-size: 40px;
            color: var(--joker-light);
            animation: pulse 2s ease-in-out infinite;
        }

        .query-header h2 {
            color: white;
            font-size: 22px;
            font-weight: 700;
        }

        .input-group {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
        }

        .input-group input {
            flex: 1;
            padding: 16px 25px;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--joker-purple);
            border-radius: 25px;
            color: white;
            font-size: 15px;
            font-family: 'Orbitron', sans-serif;
            transition: var(--transition-fast);
            transform: translateZ(0);
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--joker-light);
            box-shadow: 0 0 30px var(--joker-glow);
            transform: scale(1.01);
        }

        .input-group button {
            padding: 16px 35px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-fast);
            font-family: 'Orbitron', sans-serif;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transform: translateZ(0);
            will-change: transform;
        }

        .input-group button:hover:not(:disabled) {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 30px rgba(139, 92, 246, 0.4);
        }

        .input-group button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .example {
            color: var(--joker-light);
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0.8;
        }

        /* Loader - Optimized */
        .loader {
            display: none;
            text-align: center;
            padding: 30px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--joker-purple);
            border-top-color: var(--joker-light);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 12px;
            transform: translateZ(0);
            will-change: transform;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loader p {
            color: white;
            font-size: 14px;
            animation: pulse 1.5s ease-in-out infinite;
        }

        /* Result - Optimized */
        .result {
            background: rgba(0,0,0,0.3);
            border-radius: 25px;
            padding: 20px;
            margin-top: 20px;
            border: 2px solid var(--joker-purple);
            display: none;
            animation: slideIn 0.4s ease-out;
            transform: translateZ(0);
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .result-header h3 {
            color: var(--joker-green);
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
            padding: 6px 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--joker-purple);
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: var(--transition-fast);
            font-size: 12px;
            transform: translateZ(0);
        }

        .result-actions button:hover {
            background: var(--joker-purple);
            transform: scale(1.05);
        }

        .result-content {
            background: rgba(0,0,0,0.5);
            border-radius: 15px;
            padding: 20px;
            font-family: monospace;
            font-size: 12px;
            color: var(--joker-light);
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
            border: 1px solid var(--joker-purple);
        }

        /* Recent - Optimized */
        .recent {
            background: rgba(26, 11, 46, 0.98);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 30px;
            border: 2px solid var(--joker-purple);
            animation: slideIn 0.7s ease-out;
            transform: translateZ(0);
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
            padding: 8px 18px;
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #ef4444;
            border-radius: 20px;
            color: white;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            transition: var(--transition-fast);
            font-size: 12px;
        }

        .clear-btn:hover {
            background: #ef4444;
            transform: scale(1.02);
        }

        .recent-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 12px;
        }

        .recent-item {
            background: rgba(0,0,0,0.3);
            border-radius: 18px;
            padding: 15px;
            cursor: pointer;
            transition: var(--transition-fast);
            border: 1px solid transparent;
            transform: translateZ(0);
            will-change: transform;
        }

        .recent-item:hover {
            background: rgba(139, 92, 246, 0.2);
            transform: translateX(8px) scale(1.01);
            border-color: var(--joker-purple);
        }

        .recent-type {
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            display: inline-block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .recent-param {
            color: white;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 13px;
            word-break: break-all;
        }

        .recent-time {
            color: rgba(255,255,255,0.4);
            font-size: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard {
                padding: 20px;
            }
            
            .menu-toggle {
                top: 20px;
                left: 20px;
                width: 45px;
                height: 45px;
            }
            
            .side-menu {
                width: 100%;
                left: -100%;
            }
            
            .header {
                flex-direction: column;
                gap: 12px;
                text-align: center;
                padding: 15px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .input-group {
                flex-direction: column;
            }
            
            .query-header {
                flex-direction: column;
                text-align: center;
            }
            
            .grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .login-card {
                padding: 30px 20px;
            }
            
            .login-logo h1 {
                font-size: 24px;
            }
        }

        /* Scrollbar - Optimized */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--joker-darker);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--joker-purple);
            border-radius: 8px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--joker-light);
        }

        /* Utility Classes */
        .success-icon {
            color: var(--joker-green);
            animation: pulse 1s ease-in-out;
        }

        .error-icon {
            color: var(--joker-red);
            animation: pulse 1s ease-in-out;
        }
    </style>
</head>
<body>
    <!-- Background Elements -->
    <div class="bg-gradient"></div>
    
    <?php
    // Optimized particles - fewer but smoother
    for ($i = 0; $i < 30; $i++): 
        $left = rand(0, 100);
        $delay = rand(0, 15);
        $duration = rand(15, 25);
    ?>
    <div class="particle" style="left: <?= $left ?>%; animation-delay: <?= $delay ?>s; animation-duration: <?= $duration ?>s;"></div>
    <?php endfor; ?>

    <!-- Floating Cards -->
    <div class="floating-card card1">♠</div>
    <div class="floating-card card2">♣</div>
    <div class="floating-card card3">♥</div>
    <div class="floating-card card4">♦</div>
    <div class="floating-card card5">🃏</div>

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
                <div style="background: rgba(239,68,68,0.2); color: white; padding: 12px; border-radius: 20px; margin-bottom: 20px; text-align: center; font-size: 14px; border: 1px solid #ef4444;">
                    <i class="fas fa-exclamation-triangle error-icon"></i> <?= $hata ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="login-input-group">
                    <input type="password" name="password" placeholder="ŞİFRE" required>
                </div>
                <button type="submit" name="login" class="login-btn">GİRİŞ</button>
            </form>
            
            <div style="text-align: center; margin-top: 25px; color: var(--joker-light); font-size: 12px; opacity: 0.7;">
                Şifre: @ngbwayfite
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Hamburger Menu -->
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
        
        <div class="menu-category">
            <div class="menu-category-title">🔴 TC SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('tc1')"><i class="fas fa-id-card"></i> TC-1</div>
                <div class="menu-item" onclick="setTypeAndClose('tc2')"><i class="fas fa-id-card"></i> TC-2</div>
                <div class="menu-item" onclick="setTypeAndClose('tcgsm')"><i class="fas fa-mobile-alt"></i> TC'den GSM</div>
            </div>
        </div>
        
        <div class="menu-category">
            <div class="menu-category-title">👪 AİLE SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('aile')"><i class="fas fa-users"></i> Aile</div>
                <div class="menu-item" onclick="setTypeAndClose('aile_pro')"><i class="fas fa-users"></i> Aile Pro</div>
                <div class="menu-item" onclick="setTypeAndClose('sulale')"><i class="fas fa-tree"></i> Sülale</div>
            </div>
        </div>
        
        <div class="menu-category">
            <div class="menu-category-title">👤 İSİM SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('isim')"><i class="fas fa-user"></i> İsim</div>
                <div class="menu-item" onclick="setTypeAndClose('isim_pro')"><i class="fas fa-user"></i> İsim Pro</div>
                <div class="menu-item" onclick="setTypeAndClose('isim_il')"><i class="fas fa-map-marker-alt"></i> İsim+İlçe</div>
            </div>
        </div>
        
        <div class="menu-category">
            <div class="menu-category-title">🏠 ADRES SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('adres')"><i class="fas fa-map-marker-alt"></i> Adres</div>
                <div class="menu-item" onclick="setTypeAndClose('adres_pro')"><i class="fas fa-map-marker-alt"></i> Adres Pro</div>
            </div>
        </div>
        
        <div class="menu-category">
            <div class="menu-category-title">💼 İŞ SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('isyeri')"><i class="fas fa-briefcase"></i> İş Yeri</div>
                <div class="menu-item" onclick="setTypeAndClose('isyeri_ark')"><i class="fas fa-users"></i> İş Arkadaş</div>
            </div>
        </div>
        
        <div class="menu-category">
            <div class="menu-category-title">📱 GSM SORGULARI</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('gsmtc')"><i class="fas fa-mobile-alt"></i> GSM>TC</div>
                <div class="menu-item" onclick="setTypeAndClose('gncloperator')"><i class="fas fa-signal"></i> Operatör</div>
            </div>
        </div>
        
        <div class="menu-category">
            <div class="menu-category-title">💰 FİNANS</div>
            <div class="menu-items">
                <div class="menu-item" onclick="setTypeAndClose('iban')"><i class="fas fa-coins"></i> IBAN</div>
            </div>
        </div>
    </div>

    <!-- Dashboard -->
    <div class="dashboard">
        <div class="header">
            <h1>
                <i class="fas fa-crown"></i>
                NGB SORGU
            </h1>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="badge">ULTIMATE</div>
                <a href="?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> ÇIKIŞ</a>
            </div>
        </div>
        
        <!-- Quick Categories -->
        <div class="grid">
            <div class="card" onclick="setCategory('tc')">
                <div class="card-icon"><i class="fas fa-id-card"></i></div>
                <h3>TC</h3>
            </div>
            <div class="card" onclick="setCategory('aile')">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <h3>AİLE</h3>
            </div>
            <div class="card" onclick="setCategory('isim')">
                <div class="card-icon"><i class="fas fa-user"></i></div>
                <h3>İSİM</h3>
            </div>
            <div class="card" onclick="setCategory('adres')">
                <div class="card-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3>ADRES</h3>
            </div>
            <div class="card" onclick="setCategory('is')">
                <div class="card-icon"><i class="fas fa-briefcase"></i></div>
                <h3>İŞ</h3>
            </div>
            <div class="card" onclick="setCategory('gsm')">
                <div class="card-icon"><i class="fas fa-mobile-alt"></i></div>
                <h3>GSM</h3>
            </div>
            <div class="card" onclick="setCategory('finans')">
                <div class="card-icon"><i class="fas fa-coins"></i></div>
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
            
            <div class="input-group">
                <input type="text" id="queryParam" placeholder="Parametre girin..." onkeypress="if(event.key==='Enter') executeQuery()">
                <button onclick="executeQuery()" id="queryBtn">
                    <i class="fas fa-search"></i> SORGULA
                </button>
            </div>
            
            <div class="example" id="queryExample">
                <i class="fas fa-info-circle"></i> Örnek: 11111111110
            </div>
            
            <div class="loader" id="queryLoader">
                <div class="spinner"></div>
                <p>Sorgulanıyor...</p>
            </div>
            
            <div class="result" id="resultContainer">
                <div class="result-header">
                    <h3><i class="fas fa-check-circle success-icon"></i> SONUÇ</h3>
                    <div class="result-actions">
                        <button onclick="copyResult()" title="Kopyala"><i class="fas fa-copy"></i></button>
                        <button onclick="downloadResult()" title="İndir"><i class="fas fa-download"></i></button>
                    </div>
                </div>
                <div class="result-content" id="resultContent"></div>
            </div>
        </div>
        
        <!-- Recent Queries -->
        <div class="recent">
            <div class="recent-header">
                <h2><i class="fas fa-history"></i> SON SORGULAR</h2>
                <button class="clear-btn" onclick="clearRecent()">TEMİZLE</button>
            </div>
            <div class="recent-grid" id="recentGrid"></div>
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
        let recentQueries = JSON.parse(localStorage.getItem('recentQueries')) || [];

        // Menu Functions
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

        // Data Cleaner
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

        // Query Execution
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

            // Show loader
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

                // Show result
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                
                const resultContent = document.getElementById('resultContent');
                resultContent.textContent = resultStr;
                resultContent.classList.toggle('small', resultStr.length < 1000);
                document.getElementById('resultContainer').style.display = 'block';

                // Save to recent
                recentQueries.unshift({
                    type: api.name,
                    param: param,
                    time: new Date().toLocaleString('tr-TR')
                });
                if (recentQueries.length > 10) recentQueries.pop();
                localStorage.setItem('recentQueries', JSON.stringify(recentQueries));
                loadRecent();

                // Send to Telegram
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

        // Utility Functions
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
                localStorage.removeItem('recentQueries');
                loadRecent();
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            setType('tc1');
            loadRecent();
        });

        // Close menu on outside click
        document.addEventListener('click', (e) => {
            const menu = document.getElementById('sideMenu');
            const toggle = document.getElementById('menuToggle');
            if (!menu.contains(e.target) && !toggle.contains(e.target) && menu.classList.contains('active')) {
                menu.classList.remove('active');
                toggle.classList.remove('active');
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
