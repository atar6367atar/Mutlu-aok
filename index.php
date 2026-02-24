<?php
session_start();

// =============================================
// NGB SORGU PANELİM.IO - JOKER TEMALI PROFESYONEL PANEL
// Şifre: @ngbwayfite
// =============================================

define('SIFRE', '@ngbwayfite');

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
    } else {
        $hata = "Hatalı şifre!";
    }
}

$giris_yapildi = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGB SORGU PANELİM.IO | JOKER PANEL</title>
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
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background: linear-gradient(135deg, #000, var(--joker-darker), var(--joker-dark));
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Floating Cards */
        .floating-card {
            position: fixed;
            font-size: 40px;
            color: var(--joker-purple);
            opacity: 0.2;
            pointer-events: none;
            z-index: 1;
            animation: float 10s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(10deg); }
        }

        .card1 { top: 10%; left: 5%; animation-delay: 0s; }
        .card2 { top: 20%; right: 8%; animation-delay: 2s; }
        .card3 { bottom: 15%; left: 10%; animation-delay: 4s; }
        .card4 { bottom: 25%; right: 15%; animation-delay: 6s; }
        .card5 { top: 50%; left: 50%; animation-delay: 8s; }

        @keyframes glow {
            0% { filter: drop-shadow(0 0 5px var(--joker-purple)); }
            50% { filter: drop-shadow(0 0 30px var(--joker-light)); }
            100% { filter: drop-shadow(0 0 5px var(--joker-purple)); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
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
        }

        .login-card {
            background: rgba(46, 16, 101, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            border: 2px solid var(--joker-purple);
            box-shadow: 0 0 50px var(--joker-glow);
            animation: glow 3s ease-in-out infinite;
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
            font-size: 48px;
            color: white;
            text-shadow: 0 0 20px var(--joker-purple);
            margin: 15px 0;
        }

        .login-logo p {
            color: var(--joker-light);
            font-size: 14px;
            letter-spacing: 2px;
        }

        .login-input-group {
            margin-bottom: 25px;
        }

        .login-input-group input {
            width: 100%;
            padding: 18px 25px;
            background: rgba(255,255,255,0.1);
            border: 2px solid var(--joker-purple);
            border-radius: 20px;
            color: white;
            font-size: 16px;
            font-family: 'Orbitron', sans-serif;
        }

        .login-input-group input:focus {
            outline: none;
            border-color: var(--joker-light);
            box-shadow: 0 0 30px var(--joker-glow);
        }

        .login-input-group input::placeholder {
            color: rgba(255,255,255,0.5);
        }

        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border: none;
            border-radius: 20px;
            color: white;
            font-size: 18px;
            font-weight: 800;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 2px;
            animation: pulse 2s ease-in-out infinite;
            font-family: 'Orbitron', sans-serif;
        }

        .login-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(139, 92, 246, 0.5);
        }

        /* Dashboard */
        .dashboard {
            padding: 30px;
            position: relative;
            z-index: 10;
        }

        .header {
            background: rgba(46, 16, 101, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 25px 35px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid var(--joker-purple);
            box-shadow: 0 0 30px var(--joker-glow);
        }

        .header h1 {
            color: white;
            font-size: 32px;
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
            padding: 10px 25px;
            border-radius: 40px;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.3);
            color: white;
            border: 1px solid #ef4444;
            padding: 10px 25px;
            border-radius: 40px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #ef4444;
            transform: translateY(-3px);
        }

        /* Cards */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: rgba(46, 16, 101, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            border: 2px solid var(--joker-purple);
            transition: all 0.3s;
            cursor: pointer;
            text-align: center;
        }

        .card:hover {
            transform: translateY(-10px);
            border-color: var(--joker-light);
            box-shadow: 0 20px 40px rgba(139, 92, 246, 0.3);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .card-icon i {
            font-size: 24px;
            color: white;
        }

        .card h3 {
            color: white;
            font-size: 14px;
            font-weight: 600;
        }

        /* Query */
        .query-box {
            background: rgba(46, 16, 101, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 30px;
            margin-bottom: 30px;
            border: 2px solid var(--joker-purple);
        }

        .query-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--joker-purple);
        }

        .query-header i {
            font-size: 40px;
            color: var(--joker-light);
            animation: pulse 2s ease-in-out infinite;
        }

        .query-header h2 {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }

        .input-group {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .input-group input {
            flex: 1;
            padding: 18px 25px;
            background: rgba(255,255,255,0.1);
            border: 2px solid var(--joker-purple);
            border-radius: 20px;
            color: white;
            font-size: 16px;
            font-family: 'Orbitron', sans-serif;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--joker-light);
            box-shadow: 0 0 30px var(--joker-glow);
        }

        .input-group input::placeholder {
            color: rgba(255,255,255,0.5);
        }

        .input-group button {
            padding: 18px 35px;
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            border: none;
            border-radius: 20px;
            color: white;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-group button:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(139, 92, 246, 0.5);
        }

        .input-group button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .example {
            color: var(--joker-light);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Loader */
        .loader {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--joker-purple);
            border-top-color: var(--joker-light);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loader p {
            color: white;
            font-size: 14px;
        }

        /* Result */
        .result {
            background: rgba(0,0,0,0.3);
            border-radius: 20px;
            padding: 25px;
            margin-top: 25px;
            border: 2px solid var(--joker-purple);
            display: none;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .result-header h3 {
            color: var(--joker-green);
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
            border: 2px solid var(--joker-purple);
            border-radius: 10px;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
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
            font-size: 13px;
            color: var(--joker-light);
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
            border: 1px solid var(--joker-purple);
        }

        .result-content.small {
            background: rgba(16, 185, 129, 0.1);
            border-left: 5px solid var(--joker-green);
            font-size: 14px;
        }

        /* Recent */
        .recent {
            background: rgba(46, 16, 101, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 30px;
            border: 2px solid var(--joker-purple);
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
            gap: 10px;
            font-size: 24px;
        }

        .clear-btn {
            padding: 10px 20px;
            background: rgba(239, 68, 68, 0.3);
            border: 1px solid #ef4444;
            border-radius: 15px;
            color: white;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s;
        }

        .clear-btn:hover {
            background: #ef4444;
            transform: scale(1.05);
        }

        .recent-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .recent-item {
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid transparent;
        }

        .recent-item:hover {
            background: rgba(139, 92, 246, 0.3);
            transform: translateX(10px);
            border-color: var(--joker-purple);
        }

        .recent-type {
            background: linear-gradient(135deg, var(--joker-purple), var(--joker-light));
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            display: inline-block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .recent-param {
            color: white;
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 14px;
            word-break: break-all;
        }

        .recent-time {
            color: rgba(255,255,255,0.5);
            font-size: 11px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
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
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }
            
            .login-card {
                padding: 30px;
            }
            
            .login-logo h1 {
                font-size: 32px;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
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
    </style>
</head>
<body>
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
                <h1>NGB SORGU PANELİM.IO</h1>
                <p>JOKER PROFESYONEL PANEL</p>
            </div>
            
            <?php if (isset($hata)): ?>
                <div style="background: rgba(239,68,68,0.3); color: white; padding: 15px; border-radius: 15px; margin-bottom: 25px; text-align: center;">
                    <?= $hata ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="login-input-group">
                    <input type="password" name="password" placeholder="ŞİFRE" required>
                </div>
                <button type="submit" name="login" class="login-btn">GİRİŞ</button>
            </form>
            
            <div style="text-align: center; margin-top: 25px; color: var(--joker-light); font-size: 12px;">
                Şifre: @ngbwayfite
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- DASHBOARD -->
    <div class="dashboard">
        <div class="header">
            <h1>
                <i class="fas fa-crown"></i>
                NGB SORGU PANELİM.IO
            </h1>
            <div style="display: flex; gap: 20px; align-items: center;">
                <div class="badge">PRO</div>
                <a href="?logout=1" class="logout-btn">ÇIKIŞ</a>
            </div>
        </div>
        
        <!-- Kategoriler -->
        <div class="grid">
            <div class="card" onclick="setCategory('tc')">
                <div class="card-icon"><i class="fas fa-id-card"></i></div>
                <h3>TC</h3>
            </div>
            <div class="card" onclick="setCategory('gsm')">
                <div class="card-icon"><i class="fas fa-mobile-alt"></i></div>
                <h3>GSM</h3>
            </div>
            <div class="card" onclick="setCategory('isim')">
                <div class="card-icon"><i class="fas fa-user"></i></div>
                <h3>İSİM</h3>
            </div>
            <div class="card" onclick="setCategory('aile')">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <h3>AİLE</h3>
            </div>
            <div class="card" onclick="setCategory('adres')">
                <div class="card-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3>ADRES</h3>
            </div>
            <div class="card" onclick="setCategory('is')">
                <div class="card-icon"><i class="fas fa-briefcase"></i></div>
                <h3>İŞ</h3>
            </div>
            <div class="card" onclick="setCategory('finans')">
                <div class="card-icon"><i class="fas fa-coins"></i></div>
                <h3>FİNANS</h3>
            </div>
            <div class="card" onclick="setCategory('sosyal')">
                <div class="card-icon"><i class="fas fa-globe"></i></div>
                <h3>SOSYAL</h3>
            </div>
            <div class="card" onclick="setCategory('egitim')">
                <div class="card-icon"><i cl55,0.5); font-size: 11px; }
    </style>
</head>
<body>
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
                <h1>NGB SORGU PANELİM.IO</h1>
                <p style="color: var(--joker-light);">JOKER PROFESYONEL PANEL</p>
            </div>

            <?php if (isset($hata)): ?>
                <div style="background: rgba(239,68,68,0.3); color: white; padding: 15px; border-radius: 15px; margin-bottom: 25px;">
                    <?= $hata ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="login-input-group">
                    <input type="password" name="password" placeholder="ŞİFRE" required>
                </div>
                <button type="submit" name="login" class="login-btn">GİRİŞ</button>
            </form>

            <div style="text-align: center; margin-top: 25px; color: var(--joker-light);">
                Şifre: @ngbwayfite
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- DASHBOARD -->
    <div class="dashboard">
        <div class="header">
            <h1>
                <i class="fas fa-crown"></i>
                NGB SORGU PANELİM.IO
            </h1>
            <div style="display: flex; gap: 20px;">
                <div class="badge">PRO</div>
                <a href="?logout=1" class="logout-btn">ÇIKIŞ</a>
            </div>
        </div>

        <!-- Kategoriler -->
        <div class="grid">
            <div class="card" onclick="setCategory('tc')">
                <div class="card-icon"><i class="fas fa-id-card"></i></div>
                <h3>TC</h3>
            </div>
            <div class="card" onclick="setCategory('gsm')">
                <div class="card-icon"><i class="fas fa-mobile-alt"></i></div>
                <h3>GSM</h3>
            </div>
            <div class="card" onclick="setCategory('isim')">
                <div class="card-icon"><i class="fas fa-user"></i></div>
                <h3>İSİM</h3>
            </div>
            <div class="card" onclick="setCategory('aile')">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <h3>AİLE</h3>
            </div>
            <div class="card" onclick="setCategory('adres')">
                <div class="card-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3>ADRES</h3>
            </div>
            <div class="card" onclick="setCategory('is')">
                <div class="card-icon"><i class="fas fa-briefcase"></i></div>
                <h3>İŞ</h3>
            </div>
            <div class="card" onclick="setCategory('finans')">
                <div class="card-icon"><i class="fas fa-coins"></i></div>
                <h3>FİNANS</h3>
            </div>
            <div class="card" onclick="setCategory('sosyal')">
                <div class="card-icon"><i class="fas fa-globe"></i></div>
                <h3>SOSYAL</h3>
            </div>
            <div class="card" onclick="setCategory('egitim')">
                <div class="card-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3>EĞİTİM</h3>
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
                <input type="text" id="queryParam" placeholder="Parametre..." onkeypress="if(event.key==='Enter') executeQuery()">
                <button onclick="executeQuery()" id="queryBtn">
                    <i class="fas fa-search"></i> SORGULA
                </button>
            </div>

            <div class="example" id="queryExample">
                <i class="fas fa-info-circle"></i> Örnek: 12345678901
            </div>

            <!-- Loader -->
            <div class="loader" id="queryLoader">
                <div class="spinner"></div>
                <p style="color: white;">Sorgulanıyor...</p>
            </div>

            <!-- Sonuç -->
            <div class="result" id="resultContainer">
                <div class="result-header">
                    <h3><i class="fas fa-check-circle"></i> SONUÇ</h3>
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
                <button class="clear-btn" onclick="clearRecent()">TEMİZLE</button>
            </div>
            <div class="recent-grid" id="recentGrid"></div>
        </div>
    </div>

    <script>
        // API'ler (Güncellendi)
        const apiList = {
            // TC Kategorisi
            'tc': { name: 'TC Sorgulama', icon: 'fa-id-card', example: '11111111110', badge: 'tc', category: 'tc', proxyType: 'tc', params: ['tc'] },
            'tcpro': { name: 'TC Profesyonel', icon: 'fa-id-card', example: '11111111110', badge: 'tcpro', category: 'tc', proxyType: 'tcpro', params: ['tc'] },

            // İSİM Kategorisi
            'adsoyad': { name: 'İsim-Soyisim', icon: 'fa-user', example: 'roket atar', badge: 'adsoyad', category: 'isim', proxyType: 'adsoyad', params: ['ad', 'soyad'] },
            'adsoyadpro': { name: 'İsim-Soyisim-İl', icon: 'fa-user', example: 'roket atar bursa', badge: 'adsoyadpro', category: 'isim', proxyType: 'adsoyadpro', params: ['ad', 'soyad', 'il'] },
            'adililce': { name: 'İsim-İl', icon: 'fa-user', example: 'roket bursa', badge: 'adililce', category: 'isim', proxyType: 'adililce', params: ['ad', 'il'] },

            // AİLE Kategorisi
            'aile': { name: 'Aile (TC)', icon: 'fa-users', example: '11111111110', badge: 'aile', category: 'aile', proxyType: 'aile', params: ['tc'] },
            'ailepro': { name: 'Aile Pro (TC)', icon: 'fa-users', example: '11111111110', badge: 'ailepro', category: 'aile', proxyType: 'ailepro', params: ['tc'] },
            'sulale': { name: 'Sülale (TC)', icon: 'fa-tree', example: '11111111110', badge: 'sulale', category: 'aile', proxyType: 'sulale', params: ['tc'] },
            'soyagaci': { name: 'Soy Ağacı (TC)', icon: 'fa-tree', example: '11111111110', badge: 'soyagaci', category: 'aile', proxyType: 'soyagaci', params: ['tc'] },

            // ADRES Kategorisi
            'adres': { name: 'Adres (TC)', icon: 'fa-map-marker-alt', example: '11111111110', badge: 'adres', category: 'adres', proxyType: 'adres', params: ['tc'] },
            'adrespro': { name: 'Adres Pro (TC)', icon: 'fa-map-marker-alt', example: '11144576054', badge: 'adrespro', category: 'adres', proxyType: 'adrespro', params: ['tc'] },

            // İŞ Kategorisi
            'isyeri': { name: 'İş Yeri (TC)', icon: 'fa-briefcase', example: '11144576054', badge: 'isyeri', category: 'is', proxyType: 'isyeri', params: ['tc'] },
            'isyeriark': { name: 'İş Arkadaşları (TC)', icon: 'fa-users', example: '11144576054', badge: 'isyeriark', category: 'is', proxyType: 'isyeriark', params: ['tc'] },

            // GSM Kategorisi
            'gncloperator': { name: 'Güncel Operatör', icon: 'fa-mobile-alt', example: '5415722525', badge: 'gncloperator', category: 'gsm', proxyType: 'gncloperator', params: ['numara'] },
            'tcgsm': { name: 'TC>GSM', icon: 'fa-mobile-alt', example: '11111111110', badge: 'tcgsm', category: 'gsm', proxyType: 'tcgsm', params: ['tc'] },
            'gsmtc': { name: 'GSM>TC', icon: 'fa-mobile-alt', example: '5415722525', badge: 'gsmtc', category: 'gsm', proxyType: 'gsmtc', params: ['gsm'] },

            // FİNANS Kategorisi
            'iban': { name: 'IBAN Sorgulama', icon: 'fa-coins', example: 'TR200006283386172945624672', badge: 'iban', category: 'finans', proxyType: 'iban', params: ['iban'] },

            // SOSYAL Kategorisi
            'tg': { name: 'Telegram Sorgu', icon: 'fa-telegram', example: 'SanalMeclis', badge: 'tg', category: 'sosyal', proxyType: 'tg', params: ['username'] },

            // EĞİTİM Kategorisi
            'okulno': { name: 'Okul No (TC)', icon: 'fa-graduation-cap', example: '22121212070', badge: 'okulno', category: 'egitim', proxyType: 'okulno', params: ['tc'] }
        };

        let currentType = 'tc';
        let currentCategory = 'tc';
        let recentQueries = JSON.parse(localStorage.getItem('recentQueries')) || [];

        function setCategory(cat) {
            currentCategory = cat;
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

            // Daha uzun timeout (API'ler 10 saniye bekletebiliyor)
            const timeout = setTimeout(() => {
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Zaman aşımı! Lütfen tekrar deneyin.');
            }, 30000); // 30 saniye

            try {
                // Proxy'ye sadece tip ve ham parametreyi gönder
                const response = await fetch(`proxy.php?type=${api.proxyType}&param=${encodeURIComponent(param)}`);
                clearTimeout(timeout);

                if (!response.ok) {
                    throw new Error(`Proxy yanıt vermiyor (HTTP ${response.status})`);
                }

                const data = await response.json();

                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;

                if (data.success) {
                    const resultContent = document.getElementById('resultContent');
                    // Proxy'den gelen veri artık temizlenmiş halde (data.data içinde)
                    const resultStr = JSON.stringify(data.data, null, 2);
                    resultContent.textContent = resultStr;

                    // Veri boyutuna göre stillendirme
                    if (resultStr.length < 1000) {
                        resultContent.classList.add('small');
                    } else {
                        resultContent.classList.remove('small');
                    }

                    document.getElementById('resultContainer').style.display = 'block';

                    // Son sorgulara ekle
                    recentQueries.unshift({
                        type: api.name,
                        param: param,
                        time: new Date().toLocaleString('tr-TR')
                    });

                    if (recentQueries.length > 10) recentQueries.pop();
                    localStorage.setItem('recentQueries', JSON.stringify(recentQueries));
                    loadRecent();
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

        // Son sorguya tıklandığında parametreyi inputa koy ve sorguyu çalıştır
        function recentQueryClick(param, typeName) {
             // Tip ismine göre type key'ini bul (tam eşleme için daha karmaşık yapılabilir, şimdilik basit)
             const foundType = Object.keys(apiList).find(key => apiList[key].name === typeName);
             if (foundType) {
                 setType(foundType);
                 document.getElementById('queryParam').value = param;
                 executeQuery();
             } else {
                 // Eski kayıtlar için tip bulunamazsa sadece inputa yaz
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

        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', () => {
            setType('tc');
            loadRecent();
        });
    </script>
    <?php endif; ?>
</body>
</html>
