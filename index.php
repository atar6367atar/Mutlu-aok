<?php
session_start();

// =============================================
// DASSY TAG - ULTRA PROFESYONEL SORGU PANELİ
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
    <title>DASSY TAG | Profesyonel Sorgu Sistemi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ===== RESET & VARIABLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* ===== ANİMASYONLAR ===== */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        @keyframes glow {
            0% { box-shadow: 0 0 5px rgba(99, 102, 241, 0.2); }
            50% { box-shadow: 0 0 30px rgba(99, 102, 241, 0.6); }
            100% { box-shadow: 0 0 5px rgba(99, 102, 241, 0.2); }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
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
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ===== LOGIN SAYFASI ===== */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeInUp 0.8s ease;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            padding: 50px;
            width: 100%;
            max-width: 450px;
            animation: float 6s ease-in-out infinite;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 40px;
            animation: slideIn 0.6s ease;
        }

        .login-logo i {
            font-size: 70px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s ease-in-out infinite;
        }

        .login-logo h1 {
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 15px 0 5px;
            letter-spacing: -1px;
        }

        .login-input-group {
            margin-bottom: 25px;
            animation: slideIn 0.7s ease;
        }

        .login-input-group input {
            width: 100%;
            padding: 18px 25px;
            border: 2px solid var(--gray-200);
            border-radius: 20px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
            background: white;
        }

        .login-input-group input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            transform: scale(1.02);
        }

        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 20px;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            animation: pulse 2s ease-in-out infinite;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
        }

        .login-error {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: var(--danger);
            padding: 15px 20px;
            border-radius: 20px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 600;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        /* ===== DASHBOARD ===== */
        .dashboard {
            min-height: 100vh;
            background: var(--gray-50);
            padding: 30px;
            animation: fadeInUp 0.8s ease;
        }

        .header {
            background: white;
            border-radius: 30px;
            padding: 25px 35px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            animation: slideIn 0.6s ease;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .header h1 {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header h1 i {
            font-size: 32px;
            animation: rotate 10s linear infinite;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .status {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            padding: 10px 20px;
            border-radius: 40px;
            color: #065f46;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status i {
            color: #10b981;
            animation: pulse 2s ease-in-out infinite;
        }

        .logout-btn {
            background: #fee2e2;
            color: var(--danger);
            border: none;
            padding: 10px 25px;
            border-radius: 40px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #fecaca;
            transform: translateY(-2px);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 25px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            animation: slideIn 0.7s ease;
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .stat-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .stat-icon i {
            font-size: 28px;
            color: var(--primary);
            animation: pulse 2s ease-in-out infinite;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--gray-800);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--gray-500);
            font-weight: 600;
            font-size: 14px;
        }

        /* Category Grid */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .category-btn {
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: 20px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: var(--gray-700);
            animation: fadeInUp 0.5s ease;
        }

        .category-btn i {
            font-size: 28px;
            color: var(--primary);
            transition: all 0.3s;
        }

        .category-btn:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
        }

        .category-btn:hover i {
            transform: scale(1.1) rotate(5deg);
        }

        .category-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-color: transparent;
            color: white;
        }

        .category-btn.active i {
            color: white;
        }

        /* Query Section */
        .query-section {
            background: white;
            border-radius: 30px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            animation: slideIn 0.8s ease;
        }

        .query-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--gray-100);
        }

        .query-header i {
            font-size: 40px;
            color: var(--primary);
            background: rgba(99, 102, 241, 0.1);
            padding: 15px;
            border-radius: 20px;
            animation: pulse 2s ease-in-out infinite;
        }

        .query-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: var(--gray-800);
        }

        .query-header .badge {
            margin-left: auto;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 14px;
        }

        .input-group {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .input-group input {
            flex: 1;
            padding: 18px 25px;
            border: 2px solid var(--gray-200);
            border-radius: 20px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            transform: scale(1.02);
        }

        .input-group button {
            padding: 18px 35px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 20px;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }

        .input-group button:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3);
        }

        .input-group button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .query-example {
            background: var(--gray-50);
            border-radius: 15px;
            padding: 12px 20px;
            color: var(--gray-600);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px dashed var(--gray-300);
        }

        .query-example i {
            color: var(--primary);
            animation: pulse 2s ease-in-out infinite;
        }

        /* Loader */
        .query-loader {
            text-align: center;
            padding: 40px;
            display: none;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--gray-200);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .query-loader p {
            color: var(--gray-600);
            font-weight: 600;
        }

        /* Result Container */
        .result-container {
            background: var(--gray-50);
            border-radius: 20px;
            padding: 25px;
            margin-top: 25px;
            border: 2px solid var(--gray-200);
            display: none;
            animation: fadeInUp 0.5s ease;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .result-header h3 {
            font-size: 18px;
            font-weight: 800;
            color: var(--success);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .result-header h3 i {
            animation: pulse 2s ease-in-out infinite;
        }

        .result-actions {
            display: flex;
            gap: 10px;
        }

        .result-actions button {
            padding: 8px 15px;
            border: 2px solid var(--gray-200);
            background: white;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--gray-700);
        }

        .result-actions button:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .result-content {
            background: white;
            border-radius: 15px;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.8;
            max-height: 500px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            border: 1px solid var(--gray-200);
        }

        .result-content.small {
            background: linear-gradient(135deg, #f0f9ff, #e6f0fa);
            border-left: 5px solid var(--primary);
            font-weight: 500;
        }

        /* Recent Queries */
        .recent-section {
            background: white;
            border-radius: 30px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            animation: slideIn 0.9s ease;
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .recent-header h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .recent-header h2 i {
            color: var(--primary);
            animation: pulse 2s ease-in-out infinite;
        }

        .clear-recent {
            padding: 10px 20px;
            background: var(--gray-100);
            border: none;
            border-radius: 15px;
            color: var(--gray-600);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .clear-recent:hover {
            background: #fee2e2;
            color: var(--danger);
            transform: translateY(-2px);
        }

        .recent-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
        }

        .recent-item {
            background: var(--gray-50);
            border-radius: 15px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            animation: fadeInUp 0.5s ease;
        }

        .recent-item:hover {
            border-color: var(--primary);
            transform: translateX(5px);
            background: white;
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.1);
        }

        .recent-type {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 8px;
        }

        .recent-param {
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 5px;
            font-size: 14px;
        }

        .recent-time {
            font-size: 11px;
            color: var(--gray-500);
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
            
            .input-group {
                flex-direction: column;
            }
            
            .category-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .recent-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>
    <?php if (!$giris_yapildi): ?>
    <!-- LOGIN -->
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-shield-hal"></i>
                <h1>DASSY TAG</h1>
                <p style="color: var(--gray-500);">Profesyonel Sorgu Sistemi</p>
            </div>
            
            <?php if (isset($hata)): ?>
                <div class="login-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $hata ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="login-input-group">
                    <input type="password" name="password" placeholder="Şifre" required autofocus>
                </div>
                <button type="submit" name="login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    GİRİŞ YAP
                </button>
            </form>
        </div>
    </div>
    
    <?php else: ?>
    <!-- DASHBOARD -->
    <div class="dashboard">
        <div class="header">
            <h1>
                <i class="fas fa-shield-hal"></i>
                DASSY TAG
            </h1>
            <div class="user-badge">
                <div class="status">
                    <i class="fas fa-circle"></i>
                    <span>PREMIUM</span>
                </div>
                <a href="?logout=1" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Çıkış
                </a>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-value">19</div>
                <div class="stat-label">API Sayısı</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="stat-value">PRO</div>
                <div class="stat-label">Profesyonel</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-infinity"></i>
                </div>
                <div class="stat-value">∞</div>
                <div class="stat-label">Sınırsız</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shield"></i>
                </div>
                <div class="stat-value">SSL</div>
                <div class="stat-label">Güvenli</div>
            </div>
        </div>
        
        <!-- Categories -->
        <div class="category-grid">
            <button class="category-btn active" onclick="setCategory('tc')">
                <i class="fas fa-id-card"></i>
                <span>TC Kimlik</span>
            </button>
            <button class="category-btn" onclick="setCategory('gsm')">
                <i class="fas fa-mobile-alt"></i>
                <span>GSM</span>
            </button>
            <button class="category-btn" onclick="setCategory('isim')">
                <i class="fas fa-user"></i>
                <span>İsim</span>
            </button>
            <button class="category-btn" onclick="setCategory('aile')">
                <i class="fas fa-users"></i>
                <span>Aile</span>
            </button>
            <button class="category-btn" onclick="setCategory('adres')">
                <i class="fas fa-map-marker-alt"></i>
                <span>Adres</span>
            </button>
            <button class="category-btn" onclick="setCategory('is')">
                <i class="fas fa-briefcase"></i>
                <span>İş</span>
            </button>
            <button class="category-btn" onclick="setCategory('finans')">
                <i class="fas fa-coins"></i>
                <span>Finans</span>
            </button>
            <button class="category-btn" onclick="setCategory('sosyal')">
                <i class="fas fa-globe"></i>
                <span>Sosyal</span>
            </button>
            <button class="category-btn" onclick="setCategory('egitim')">
                <i class="fas fa-graduation-cap"></i>
                <span>Eğitim</span>
            </button>
        </div>
        
        <!-- Query Section -->
        <div class="query-section">
            <div class="query-header">
                <i class="fas fa-id-card" id="queryIcon"></i>
                <h2 id="queryTitle">TC Sorgulama</h2>
                <div class="badge" id="queryBadge">tc</div>
            </div>
            
            <div class="input-group">
                <input type="text" id="queryParam" placeholder="Parametre girin..." onkeypress="if(event.key==='Enter') executeQuery()">
                <button onclick="executeQuery()" id="queryBtn">
                    <i class="fas fa-search"></i>
                    Sorgula
                </button>
            </div>
            
            <div class="query-example" id="queryExample">
                <i class="fas fa-info-circle"></i>
                Örnek: 12345678901
            </div>
            
            <!-- Loader -->
            <div class="query-loader" id="queryLoader">
                <div class="spinner"></div>
                <p>Sorgulanıyor... (max 30 saniye)</p>
            </div>
            
            <!-- Result -->
            <div class="result-container" id="resultContainer">
                <div class="result-header">
                    <h3>
                        <i class="fas fa-check-circle"></i>
                        Sorgu Sonucu
                    </h3>
                    <div class="result-actions">
                        <button onclick="copyResult()">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button onclick="downloadResult()">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="result-content" id="resultContent"></div>
            </div>
        </div>
        
        <!-- Recent Queries -->
        <div class="recent-section">
            <div class="recent-header">
                <h2>
                    <i class="fas fa-history"></i>
                    Son Sorgular
                </h2>
                <button class="clear-recent" onclick="clearRecent()">
                    <i class="fas fa-trash"></i>
                    Temizle
                </button>
            </div>
            <div class="recent-grid" id="recentGrid"></div>
        </div>
    </div>
    
    <script>
        // =============================================
        // DASSY TAG - PROFESYONEL JAVASCRIPT
        // Şifre: @ngbwayfite
        // =============================================
        
        // API Base URL
        const API_BASE = 'https://punisher.alwaysdata.net/apiservices/';
        
        // State
        let currentType = 'tc';
        let currentCategory = 'tc';
        let recentQueries = JSON.parse(localStorage.getItem('recentQueries')) || [];
        
        // API Listesi (19 API)
        const apiList = {
            // TC Kimlik
            'tc': {
                name: 'TC Sorgulama',
                icon: 'fa-id-card',
                example: '12345678901',
                badge: 'tc',
                category: 'tc',
                url: (p) => `${API_BASE}tc.php?tc=${p}`
            },
            'tcpro': {
                name: 'TC Profesyonel',
                icon: 'fa-id-card',
                example: '12345678901',
                badge: 'tcpro',
                category: 'tc',
                url: (p) => `${API_BASE}tcpro.php?tc=${p}`
            },
            
            // İsim
            'adsoyad': {
                name: 'İsim-Soyisim',
                icon: 'fa-user',
                example: 'roket atar',
                badge: 'adsoyad',
                category: 'isim',
                url: (p) => {
                    const parts = p.split(' ');
                    return `${API_BASE}adsoyad.php?ad=${parts[0]}&soyad=${parts[1] || ''}`;
                }
            },
            'adsoyadpro': {
                name: 'İsim-Soyisim-İl',
                icon: 'fa-user',
                example: 'roket atar bursa',
                badge: 'adsoyadpro',
                category: 'isim',
                url: (p) => {
                    const parts = p.split(' ');
                    return `${API_BASE}adsoyadpro.php?ad=${parts[0]}&soyad=${parts[1] || ''}&il=${parts[2] || ''}`;
                }
            },
            'adililce': {
                name: 'İsim-İl',
                icon: 'fa-user',
                example: 'roket bursa',
                badge: 'adililce',
                category: 'isim',
                url: (p) => {
                    const parts = p.split(' ');
                    return `${API_BASE}adililce.php?ad=${parts[0]}&il=${parts[1] || ''}`;
                }
            },
            
            // Aile
            'aile': {
                name: 'Aile Sorgulama',
                icon: 'fa-users',
                example: '12345678901',
                badge: 'aile',
                category: 'aile',
                url: (p) => `${API_BASE}aile.php?tc=${p}`
            },
            'ailepro': {
                name: 'Aile Profesyonel',
                icon: 'fa-users',
                example: '12345678901',
                badge: 'ailepro',
                category: 'aile',
                url: (p) => `${API_BASE}ailepro.php?tc=${p}`
            },
            'sulale': {
                name: 'Sülale Sorgulama',
                icon: 'fa-tree',
                example: '12345678901',
                badge: 'sulale',
                category: 'aile',
                url: (p) => `${API_BASE}sulale.php?tc=${p}`
            },
            'soyagaci': {
                name: 'Soy Ağacı',
                icon: 'fa-tree',
                example: '12345678901',
                badge: 'soyagaci',
                category: 'aile',
                url: (p) => `${API_BASE}soyagaci.php?tc=${p}`
            },
            
            // Adres
            'adres': {
                name: 'Adres Sorgulama',
                icon: 'fa-map-marker-alt',
                example: '12345678901',
                badge: 'adres',
                category: 'adres',
                url: (p) => `${API_BASE}adres.php?tc=${p}`
            },
            'adrespro': {
                name: 'Adres Profesyonel',
                icon: 'fa-map-marker-alt',
                example: '12345678901',
                badge: 'adrespro',
                category: 'adres',
                url: (p) => `${API_BASE}adrespro.php?tc=${p}`
            },
            
            // İş
            'isyeri': {
                name: 'İş Yeri Sorgulama',
                icon: 'fa-briefcase',
                example: '12345678901',
                badge: 'isyeri',
                category: 'is',
                url: (p) => `${API_BASE}isyeri.php?tc=${p}`
            },
            'isyeriark': {
                name: 'İş Arkadaşları',
                icon: 'fa-users',
                example: '12345678901',
                badge: 'isyeriark',
                category: 'is',
                url: (p) => `${API_BASE}isyeriark.php?tc=${p}`
            },
            
            // GSM
            'gncloperator': {
                name: 'Güncel Operatör',
                icon: 'fa-mobile-alt',
                example: '5415722525',
                badge: 'gncloperator',
                category: 'gsm',
                url: (p) => `${API_BASE}gncloperator.php?numara=${p}`
            },
            'tcgsm': {
                name: 'TC\'den GSM',
                icon: 'fa-mobile-alt',
                example: '12345678901',
                badge: 'tcgsm',
                category: 'gsm',
                url: (p) => `${API_BASE}tcgsm.php?tc=${p}`
            },
            'gsmtc': {
                name: 'GSM\'den TC',
                icon: 'fa-mobile-alt',
                example: '5415722525',
                badge: 'gsmtc',
                category: 'gsm',
                url: (p) => `${API_BASE}gsmtc.php?gsm=${p}`
            },
            
            // Finans
            'iban': {
                name: 'IBAN Sorgulama',
                icon: 'fa-coins',
                example: 'TR200006283386172945624672',
                badge: 'iban',
                category: 'finans',
                url: (p) => `${API_BASE}iban.php?iban=${p}`
            },
            'sms': {
                name: 'SMS Sorgulama',
                icon: 'fa-comment',
                example: '5415722525',
                badge: 'sms',
                category: 'finans',
                url: (p) => `${API_BASE}sms.php?gsm=${p}`
            },
            
            // Sosyal
            'tg': {
                name: 'Telegram Sorgulama',
                icon: 'fa-telegram',
                example: 'SanalMeclis',
                badge: 'tg',
                category: 'sosyal',
                url: (p) => `${API_BASE}tg.php?username=${p.replace('@', '')}`
            },
            
            // Eğitim
            'okulno': {
                name: 'Okul Numarası',
                icon: 'fa-graduation-cap',
                example: '12345678901',
                badge: 'okulno',
                category: 'egitim',
                url: (p) => `${API_BASE}okulno.php?tc=${p}`
            }
        };
        
        // Set category
        function setCategory(category) {
            currentCategory = category;
            
            // Update active buttons
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
            
            // Show first API in category
            const firstApi = Object.values(apiList).find(api => api.category === category);
            if (firstApi) {
                setType(Object.keys(apiList).find(key => apiList[key] === firstApi));
            }
        }
        
        // Set query type
        function setType(type) {
            currentType = type;
            const api = apiList[type];
            
            document.getElementById('queryIcon').className = `fas ${api.icon}`;
            document.getElementById('queryTitle').textContent = api.name;
            document.getElementById('queryBadge').textContent = api.badge;
            document.getElementById('queryExample').innerHTML = `<i class="fas fa-info-circle"></i> Örnek: ${api.example}`;
            document.getElementById('queryParam').placeholder = api.example;
        }
        
        // Execute query
        async function executeQuery() {
            const param = document.getElementById('queryParam').value.trim();
            
            if (!param) {
                alert('Lütfen parametre girin!');
                return;
            }
            
            const api = apiList[currentType];
            const url = api.url(param);
            
            // Show loader
            document.getElementById('queryLoader').style.display = 'block';
            document.getElementById('resultContainer').style.display = 'none';
            document.getElementById('queryBtn').disabled = true;
            
            // Timeout 30 seconds
            const timeout = setTimeout(() => {
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Zaman aşımı! (30 saniye)');
            }, 30000);
            
            try {
                const response = await fetch(url);
                clearTimeout(timeout);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const data = await response.json();
                
                // Hide loader
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                
                // Format and show result
                const resultContent = document.getElementById('resultContent');
                let resultStr;
                
                // Eğer data varsa ve reklam varsa temizle
                if (data && typeof data === 'object') {
                    // Reklam alanlarını temizle
                    delete data.geliştirici;
                    delete data.sürüm;
                    delete data.reklam;
                    delete data.auth;
                    delete data.api_sahibi;
                    
                    resultStr = JSON.stringify(data, null, 2);
                } else {
                    resultStr = JSON.stringify(data, null, 2);
                }
                
                resultContent.textContent = resultStr;
                
                // Küçük sonuçsa özel stil
                if (resultStr.length < 1000) {
                    resultContent.classList.add('small');
                } else {
                    resultContent.classList.remove('small');
                }
                
                document.getElementById('resultContainer').style.display = 'block';
                
                // Add to recent
                addToRecent(api.name, param, data);
                
            } catch (error) {
                clearTimeout(timeout);
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Hata: ' + error.message);
            }
        }
        
        // Copy result
        function copyResult() {
            const content = document.getElementById('resultContent').textContent;
            navigator.clipboard.writeText(content).then(() => {
                alert('Sonuç kopyalandı!');
            });
        }
        
        // Download result
        function downloadResult() {
            const content = document.getElementById('resultContent').textContent;
            const blob = new Blob([content], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `sorgu_${currentType}_${Date.now()}.txt`;
            a.click();
            URL.revokeObjectURL(url);
        }
        
        // Add to recent
        function addToRecent(type, param, data) {
            const query = {
                type: type,
                param: param,
                time: new Date().toLocaleString('tr-TR'),
                preview: JSON.stringify(data).substring(0, 50) + '...'
            };
            
            recentQueries.unshift(query);
            if (recentQueries.length > 10) recentQueries.pop();
            
            localStorage.setItem('recentQueries', JSON.stringify(recentQueries));
            loadRecent();
        }
        
        // Load recent
        function loadRecent() {
            const grid = document.getElementById('recentGrid');
            
            if (recentQueries.length === 0) {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: var(--gray-400); padding: 40px;">Henüz sorgu yapılmadı</p>';
                return;
            }
            
            grid.innerHTML = recentQueries.map(q => `
                <div class="recent-item" onclick='showRecent(${JSON.stringify(q)})'>
                    <span class="recent-type">${q.type}</span>
                    <div class="recent-param">${q.param}</div>
                    <div class="recent-time">${q.time}</div>
                </div>
            `).join('');
        }
        
        // Show recent
        function showRecent(query) {
            document.getElementById('resultContent').textContent = JSON.stringify(query, null, 2);
            document.getElementById('resultContainer').style.display = 'block';
        }
        
        // Clear recent
        function clearRecent() {
            if (confirm('Tüm son sorgular silinsin mi?')) {
                recentQueries = [];
                localStorage.removeItem('recentQueries');
                loadRecent();
            }
        }
        
        // Load on start
        document.addEventListener('DOMContentLoaded', () => {
            setType('tc');
            loadRecent();
            
            // Add animation to cards
            document.querySelectorAll('.stat-card').forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
            
            document.querySelectorAll('.category-btn').forEach((btn, index) => {
                btn.style.animationDelay = `${index * 0.05}s`;
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
