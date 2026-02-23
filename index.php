<?php
session_start();

// Giriş kontrolü
define('SIFRE', 'CDNmutluhosting');

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Giriş işlemi
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
    <title>DASSY TAG | Ultra Profesyonel Sorgu Paneli</title>
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
            --darker: #020617;
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
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M10 10 L90 10 L90 90 L10 90 Z" fill="none" stroke="white" stroke-width="2"/><circle cx="50" cy="50" r="20" fill="none" stroke="white" stroke-width="2"/></svg>') repeat;
            pointer-events: none;
            z-index: -1;
        }

        /* ===== LOGIN PAGE ===== */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            animation: floatIn 0.8s cubic-bezier(0.23, 1, 0.32, 1);
            transform-origin: center;
        }

        @keyframes floatIn {
            0% {
                opacity: 0;
                transform: scale(0.9) translateY(30px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .login-logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo i {
            font-size: 70px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 10px 20px rgba(99, 102, 241, 0.3));
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

        .login-logo p {
            color: var(--gray-500);
            font-size: 14px;
            font-weight: 500;
        }

        .login-input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .login-input-group i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 18px;
            transition: all 0.3s;
            z-index: 1;
        }

        .login-input-group input {
            width: 100%;
            padding: 18px 20px 18px 50px;
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
        }

        .login-input-group input:focus + i {
            color: var(--primary);
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
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

        .login-btn:active {
            transform: translateY(-1px);
        }

        .login-error {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: var(--danger);
            padding: 15px 20px;
            border-radius: 20px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #fecaca;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .login-info {
            text-align: center;
            margin-top: 30px;
            color: var(--gray-500);
            font-size: 13px;
            font-weight: 500;
        }

        .login-info i {
            color: var(--primary);
            margin-right: 5px;
        }

        /* ===== DASHBOARD ===== */
        .dashboard {
            min-height: 100vh;
            background: var(--gray-50);
            position: relative;
        }

        /* Hamburger Menu */
        .hamburger {
            position: fixed;
            top: 25px;
            left: 25px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 20px;
            color: white;
            font-size: 24px;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hamburger:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 15px 35px rgba(99, 102, 241, 0.4);
        }

        .hamburger.active {
            left: 315px;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -350px;
            width: 350px;
            height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 20px 0 40px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: left 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar-header {
            padding: 40px 30px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header i {
            font-size: 50px;
            margin-bottom: 15px;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.2));
        }

        .sidebar-header h2 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .sidebar-header p {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 500;
        }

        .user-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 20px;
            border-radius: 15px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(5px);
        }

        .user-badge i {
            font-size: 24px;
            margin: 0;
        }

        .user-badge span {
            font-weight: 600;
        }

        .sidebar-search {
            padding: 20px;
            position: relative;
        }

        .sidebar-search i {
            position: absolute;
            left: 35px;
            top: 35px;
            color: var(--gray-400);
            font-size: 16px;
        }

        .sidebar-search input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid var(--gray-200);
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .sidebar-search input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .menu-category {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gray-400);
            margin: 25px 0 15px;
            padding-left: 15px;
        }

        .menu-category:first-of-type {
            margin-top: 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            border-radius: 15px;
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .menu-item i {
            width: 24px;
            font-size: 18px;
            color: var(--gray-500);
            transition: all 0.3s;
        }

        .menu-item:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
            transform: translateX(5px);
        }

        .menu-item:hover i {
            color: var(--primary);
            transform: scale(1.1);
        }

        .menu-item.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
        }

        .menu-item.active i {
            color: white;
        }

        .badge {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 11px;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 25px;
            border-top: 2px solid var(--gray-100);
        }

        .logout-menu-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            background: var(--gray-100);
            border: none;
            border-radius: 15px;
            color: var(--danger);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .logout-menu-btn:hover {
            background: #fee2e2;
            transform: translateX(5px);
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .overlay.show {
            display: block;
            opacity: 1;
        }

        /* Main Content */
        .main-content {
            margin-left: 0;
            padding: 30px;
            transition: margin-left 0.3s;
            max-width: 1400px;
            margin: 0 auto;
        }

        .main-content.shifted {
            margin-left: 350px;
        }

        /* Top Bar */
        .top-bar {
            background: white;
            border-radius: 30px;
            padding: 20px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-title i {
            font-size: 28px;
            color: var(--primary);
            background: rgba(99, 102, 241, 0.1);
            padding: 12px;
            border-radius: 20px;
        }

        .page-title h1 {
            font-size: 26px;
            font-weight: 800;
            color: var(--gray-800);
            letter-spacing: -0.5px;
        }

        .page-title p {
            color: var(--gray-500);
            font-size: 14px;
            font-weight: 500;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .status-badge {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 700;
            color: #065f46;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #6ee7b7;
        }

        .status-badge i {
            color: #10b981;
        }

        .hamburger-top {
            display: none;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 15px;
            color: white;
            font-size: 20px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 30px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .stat-card:hover {
            transform: translateY(-5px);
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
            margin-bottom: 20px;
        }

        .stat-icon i {
            font-size: 28px;
            color: var(--primary);
        }

        .stat-value {
            font-size: 36px;
            font-weight: 800;
            color: var(--gray-800);
            margin-bottom: 5px;
            letter-spacing: -1px;
        }

        .stat-label {
            color: var(--gray-500);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 30px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .quick-actions h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--gray-800);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quick-actions h2 i {
            color: var(--primary);
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }

        .quick-btn {
            padding: 20px;
            background: linear-gradient(135deg, var(--gray-50), white);
            border: 2px solid var(--gray-200);
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: var(--gray-700);
        }

        .quick-btn i {
            font-size: 28px;
            color: var(--primary);
            transition: all 0.3s;
        }

        .quick-btn:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
        }

        .quick-btn:hover i {
            transform: scale(1.1);
        }

        .quick-btn.premium {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-color: #fbbf24;
        }

        .quick-btn.premium i {
            color: #d97706;
        }

        /* Query Section */
        .query-section {
            background: white;
            border-radius: 30px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .query-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .query-header h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .query-header h2 i {
            color: var(--primary);
        }

        .query-type-badge {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .query-input-group {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .query-input-group input {
            flex: 1;
            padding: 18px 25px;
            border: 2px solid var(--gray-200);
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .query-input-group input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .query-input-group button {
            padding: 18px 35px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }

        .query-input-group button:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3);
        }

        .query-input-group button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .query-example {
            background: var(--gray-50);
            border-radius: 20px;
            padding: 15px 20px;
            color: var(--gray-600);
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px dashed var(--gray-300);
        }

        .query-example i {
            color: var(--primary);
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
            border-radius: 25px;
            padding: 25px;
            margin-top: 25px;
            border: 2px solid var(--gray-200);
            display: none;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .result-header h3 {
            font-size: 18px;
            font-weight: 800;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .result-header h3 i {
            color: var(--success);
        }

        .result-actions {
            display: flex;
            gap: 10px;
        }

        .result-actions button {
            padding: 10px 20px;
            border: 2px solid var(--gray-200);
            background: white;
            border-radius: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-700);
        }

        .result-actions button:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .result-content {
            background: white;
            border-radius: 20px;
            padding: 25px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.8;
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid var(--gray-200);
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .small-result {
            background: linear-gradient(135deg, #f0f9ff, #e6f0fa);
            border-left: 5px solid var(--primary);
        }

        /* Recent Queries */
        .recent-section {
            background: white;
            border-radius: 30px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
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
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .clear-recent:hover {
            background: #fee2e2;
            color: var(--danger);
        }

        .recent-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }

        .recent-item {
            background: var(--gray-50);
            border-radius: 20px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .recent-item:hover {
            border-color: var(--primary);
            transform: translateX(5px);
            background: white;
        }

        .recent-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .recent-type {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            color: var(--primary);
        }

        .recent-time {
            font-size: 11px;
            color: var(--gray-400);
            font-weight: 500;
        }

        .recent-param {
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .recent-preview {
            font-size: 12px;
            color: var(--gray-500);
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content.shifted {
                margin-left: 0;
            }
            
            .sidebar {
                width: 300px;
            }
            
            .hamburger.active {
                left: 275px;
            }
        }

        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }
            
            .hamburger-top {
                display: flex;
            }
            
            .top-bar {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }
            
            .query-input-group {
                flex-direction: column;
            }
            
            .quick-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .recent-grid {
                grid-template-columns: 1fr;
            }
            
            .result-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .quick-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .slide-in {
            animation: slideIn 0.3s;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            border-radius: 20px;
            padding: 15px 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 9999;
            animation: slideUp 0.3s;
            border-left: 5px solid var(--success);
        }

        .toast.error {
            border-left-color: var(--danger);
        }

        .toast i {
            font-size: 24px;
            color: var(--success);
        }

        .toast.error i {
            color: var(--danger);
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
    </style>
</head>
<body>
    <?php if (!$giris_yapildi): ?>
    <!-- LOGIN SAYFASI -->
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-shield-hal"></i>
                <h1>DASSY TAG</h1>
                <p>Profesyonel Sorgu Paneli v2.0</p>
            </div>
            
            <?php if (isset($hata)): ?>
                <div class="login-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $hata ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="login-input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Şifre" required autofocus>
                </div>
                <button type="submit" name="login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    GİRİŞ YAP
                </button>
            </form>
            
            <div class="login-info">
                <i class="fas fa-info-circle"></i>
                Yetkili kullanıcılar içindir
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- DASHBOARD -->
    <div class="dashboard">
        <!-- Hamburger Menu -->
        <button class="hamburger" id="hamburgerBtn">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Overlay -->
        <div class="overlay" id="overlay"></div>
        
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-shield-hal"></i>
                <h2>DASSY TAG</h2>
                <p>Profesyonel Sorgu Sistemi</p>
                <div class="user-badge">
                    <i class="fas fa-user-cog"></i>
                    <div>
                        <span>Admin Kullanıcı</span>
                        <p style="font-size: 11px; opacity: 0.8;">Yetkili Panel</p>
                    </div>
                </div>
            </div>
            
            <div class="sidebar-search">
                <i class="fas fa-search"></i>
                <input type="text" id="menuSearch" placeholder="Sorgu ara..." onkeyup="filterMenu()">
            </div>
            
            <div class="sidebar-menu" id="menuItems">
                <div class="menu-category">HIZLI ERİŞİM</div>
                <div class="menu-item" onclick="setQueryType('tc')">
                    <i class="fas fa-id-card"></i>
                    <span>TC Sorgu</span>
                    <span class="badge">Hızlı</span>
                </div>
                <div class="menu-item" onclick="setQueryType('gsm')">
                    <i class="fas fa-mobile-alt"></i>
                    <span>GSM Sorgu</span>
                    <span class="badge">Hızlı</span>
                </div>
                <div class="menu-item" onclick="setQueryType('ad')">
                    <i class="fas fa-user"></i>
                    <span>Ad-Soyad</span>
                    <span class="badge">Hızlı</span>
                </div>
                
                <div class="menu-category">TC KİMLİK</div>
                <div class="menu-item" onclick="setQueryType('tc')">
                    <i class="fas fa-id-card"></i>
                    <span>TC Sorgu</span>
                </div>
                <div class="menu-item" onclick="setQueryType('tc2')">
                    <i class="fas fa-id-card"></i>
                    <span>TC Detaylı</span>
                </div>
                <div class="menu-item" onclick="setQueryType('tc-ikametgah')">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>İkametgah</span>
                </div>
                <div class="menu-item" onclick="setQueryType('vesika')">
                    <i class="fas fa-id-card"></i>
                    <span>Vesika</span>
                </div>
                
                <div class="menu-category">GSM</div>
                <div class="menu-item" onclick="setQueryType('gsm')">
                    <i class="fas fa-mobile-alt"></i>
                    <span>GSM Sorgu</span>
                </div>
                <div class="menu-item" onclick="setQueryType('gsm2')">
                    <i class="fas fa-mobile-alt"></i>
                    <span>GSM Detaylı</span>
                </div>
                
                <div class="menu-category">İSİM</div>
                <div class="menu-item" onclick="setQueryType('ad')">
                    <i class="fas fa-user"></i>
                    <span>Ad-Soyad Sorgu</span>
                </div>
                
                <div class="menu-category">AİLE</div>
                <div class="menu-item" onclick="setQueryType('aile')">
                    <i class="fas fa-users"></i>
                    <span>Aile Sorgu</span>
                </div>
                <div class="menu-item" onclick="setQueryType('sulale')">
                    <i class="fas fa-tree"></i>
                    <span>Sülale Sorgu</span>
                </div>
                <div class="menu-item" onclick="setQueryType('hane')">
                    <i class="fas fa-home"></i>
                    <span>Hane Sorgu</span>
                </div>
                
                <div class="menu-category">DİĞER</div>
                <div class="menu-item" onclick="setQueryType('isyeri')">
                    <i class="fas fa-briefcase"></i>
                    <span>İş Yeri</span>
                </div>
                <div class="menu-item" onclick="setQueryType('plaka')">
                    <i class="fas fa-car"></i>
                    <span>Plaka</span>
                </div>
            </div>
            
            <div class="sidebar-footer">
                <a href="?logout=1" class="logout-menu-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Çıkış Yap</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="page-title">
                    <i class="fas fa-shield-hal"></i>
                    <div>
                        <h1 id="pageTitle">DASSY TAG Sorgu Paneli</h1>
                        <p id="pageSubtitle">Profesyonel veri sorgulama sistemi</p>
                    </div>
                </div>
                <div class="user-menu">
                    <div class="status-badge">
                        <i class="fas fa-circle"></i>
                        <span>ONLINE</span>
                    </div>
                </div>
            </div>
            
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="stat-value">16</div>
                    <div class="stat-label">API Sayısı</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">40s</div>
                    <div class="stat-label">Timeout</div>
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
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>
                    <i class="fas fa-bolt"></i>
                    Hızlı Giriş Butonları
                </h2>
                <div class="quick-grid">
                    <div class="quick-btn" onclick="setQueryType('tc')">
                        <i class="fas fa-id-card"></i>
                        <span>TC Sorgu</span>
                    </div>
                    <div class="quick-btn" onclick="setQueryType('gsm')">
                        <i class="fas fa-mobile-alt"></i>
                        <span>GSM Sorgu</span>
                    </div>
                    <div class="quick-btn" onclick="setQueryType('ad')">
                        <i class="fas fa-user"></i>
                        <span>Ad-Soyad</span>
                    </div>
                    <div class="quick-btn premium" onclick="setQueryType('aile')">
                        <i class="fas fa-users"></i>
                        <span>Aile</span>
                    </div>
                    <div class="quick-btn" onclick="setQueryType('plaka')">
                        <i class="fas fa-car"></i>
                        <span>Plaka</span>
                    </div>
                    <div class="quick-btn premium" onclick="setQueryType('sulale')">
                        <i class="fas fa-tree"></i>
                        <span>Sülale</span>
                    </div>
                </div>
            </div>
            
            <!-- Query Section -->
            <div class="query-section">
                <div class="query-header">
                    <h2>
                        <i class="fas fa-search" id="queryIcon"></i>
                        <span id="queryTitle">TC Sorgulama</span>
                    </h2>
                    <div class="query-type-badge" id="queryTypeBadge">
                        <i class="fas fa-id-card"></i>
                        <span id="currentType">tc</span>
                    </div>
                </div>
                
                <div class="query-input-group">
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
                    <p>Sorgulanıyor... (max 40 saniye)</p>
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
                                Kopyala
                            </button>
                            <button onclick="downloadResult()" id="downloadBtn">
                                <i class="fas fa-download"></i>
                                TXT İndir
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
                <div class="recent-grid" id="recentGrid">
                    <!-- Recent items will be added here -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast container -->
    <div id="toast"></div>
    
    <script>
        // ==================== GLOBAL VARIABLES ====================
        let currentType = 'tc';
        let recentQueries = JSON.parse(localStorage.getItem('recentQueries')) || [];
        let timeoutId = null;
        
        // Query type configurations
        const queryTypes = {
            'tc': {
                name: 'TC Sorgulama',
                icon: 'fa-id-card',
                placeholder: '11 haneli TC kimlik numarası',
                example: '12345678901',
                badge: 'tc'
            },
            'tc2': {
                name: 'TC Detaylı Sorgulama',
                icon: 'fa-id-card',
                placeholder: '11 haneli TC kimlik numarası',
                example: '12345678901',
                badge: 'tc2'
            },
            'gsm': {
                name: 'GSM Sorgulama',
                icon: 'fa-mobile-alt',
                placeholder: '10 haneli GSM numarası',
                example: '5346149118',
                badge: 'gsm'
            },
            'gsm2': {
                name: 'GSM Detaylı Sorgulama',
                icon: 'fa-mobile-alt',
                placeholder: '10 haneli GSM numarası',
                example: '5346149118',
                badge: 'gsm2'
            },
            'ad': {
                name: 'Ad-Soyad Sorgulama',
                icon: 'fa-user',
                placeholder: 'Ad ve soyad (örn: EYMEN YAVUZ)',
                example: 'EYMEN YAVUZ',
                badge: 'ad'
            },
            'aile': {
                name: 'Aile Sorgulama',
                icon: 'fa-users',
                placeholder: 'TC kimlik numarası',
                example: '12345678901',
                badge: 'aile'
            },
            'sulale': {
                name: 'Sülale Sorgulama',
                icon: 'fa-tree',
                placeholder: 'TC kimlik numarası',
                example: '12345678901',
                badge: 'sulale'
            },
            'hane': {
                name: 'Hane Sorgulama',
                icon: 'fa-home',
                placeholder: 'TC kimlik numarası',
                example: '12345678901',
                badge: 'hane'
            },
            'isyeri': {
                name: 'İş Yeri Sorgulama',
                icon: 'fa-briefcase',
                placeholder: 'TC kimlik numarası',
                example: '12345678901',
                badge: 'isyeri'
            },
            'plaka': {
                name: 'Plaka Sorgulama',
                icon: 'fa-car',
                placeholder: 'Plaka (örn: 34AKP34)',
                example: '34AKP34',
                badge: 'plaka'
            },
            'vesika': {
                name: 'Vesika Sorgulama',
                icon: 'fa-id-card',
                placeholder: 'TC kimlik numarası',
                example: '12345678901',
                badge: 'vesika'
            },
            'tc-ikametgah': {
                name: 'İkametgah Sorgulama',
                icon: 'fa-map-marker-alt',
                placeholder: 'TC kimlik numarası',
                example: '12345678901',
                badge: 'ikametgah'
            }
        };
        
        // ==================== HAMBURGER MENU ====================
        const hamburger = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const mainContent = document.getElementById('mainContent');
        
        function toggleMenu() {
            sidebar.classList.toggle('open');
            hamburger.classList.toggle('active');
            overlay.classList.toggle('show');
            mainContent.classList.toggle('shifted');
        }
        
        hamburger.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);
        
        // ==================== MENU FILTER ====================
        function filterMenu() {
            const search = document.getElementById('menuSearch').value.toLowerCase();
            const items = document.querySelectorAll('.menu-item');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(search)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        // ==================== SET QUERY TYPE ====================
        function setQueryType(type) {
            currentType = type;
            const config = queryTypes[type];
            
            if (!config) return;
            
            // Update UI
            document.getElementById('queryIcon').className = `fas ${config.icon}`;
            document.getElementById('queryTitle').textContent = config.name;
            document.getElementById('queryTypeBadge').innerHTML = `<i class="fas ${config.icon}"></i><span>${config.badge}</span>`;
            document.getElementById('queryParam').placeholder = config.placeholder;
            document.getElementById('queryExample').innerHTML = `<i class="fas fa-info-circle"></i> Örnek: ${config.example}`;
            
            // Update active menu item
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Find and highlight active menu item (simplified)
            const activeItems = Array.from(document.querySelectorAll('.menu-item')).filter(
                item => item.textContent.toLowerCase().includes(config.name.toLowerCase())
            );
            if (activeItems.length > 0) {
                activeItems[0].classList.add('active');
            }
            
            // Close menu on mobile
            if (window.innerWidth <= 768) {
                toggleMenu();
            }
        }
        
        // ==================== EXECUTE QUERY ====================
        async function executeQuery() {
            const param = document.getElementById('queryParam').value.trim();
            
            if (!param) {
                showToast('Lütfen parametre girin!', 'error');
                return;
            }
            
            // Clear previous timeout
            if (timeoutId) clearTimeout(timeoutId);
            
            // Show loader
            document.getElementById('queryLoader').style.display = 'block';
            document.getElementById('resultContainer').style.display = 'none';
            document.getElementById('queryBtn').disabled = true;
            
            // Set timeout (40 seconds)
            timeoutId = setTimeout(() => {
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                showToast('Sorgu zaman aşımına uğradı! (40 saniye)', 'error');
            }, 40000);
            
            try {
                const response = await fetch('api-proxy.php?type=' + currentType + '&param=' + encodeURIComponent(param));
                const data = await response.json();
                
                // Clear timeout
                clearTimeout(timeoutId);
                
                // Hide loader
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                
                if (data.success) {
                    // Show result
                    const resultContent = document.getElementById('resultContent');
                    const resultData = data.data || data;
                    const resultStr = JSON.stringify(resultData, null, 2);
                    
                    resultContent.textContent = resultStr;
                    
                    // Check if result is small (less than 1000 chars) for special styling
                    if (resultStr.length < 1000) {
                        resultContent.classList.add('small-result');
                    } else {
                        resultContent.classList.remove('small-result');
                    }
                    
                    document.getElementById('resultContainer').style.display = 'block';
                    
                    // Add to recent
                    addToRecent(currentType, param, resultData);
                    
                    showToast('Sorgu başarılı!', 'success');
                } else {
                    showToast(data.error || 'Sorgu başarısız!', 'error');
                }
            } catch (error) {
                clearTimeout(timeoutId);
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                showToast('Bağlantı hatası: ' + error.message, 'error');
            }
        }
        
        // ==================== COPY RESULT ====================
        function copyResult() {
            const content = document.getElementById('resultContent').textContent;
            navigator.clipboard.writeText(content).then(() => {
                showToast('Sonuç kopyalandı!', 'success');
            }).catch(() => {
                showToast('Kopyalama başarısız!', 'error');
            });
        }
        
        // ==================== DOWNLOAD RESULT ====================
        function downloadResult() {
            const content = document.getElementById('resultContent').textContent;
            const type = queryTypes[currentType].name;
            const param = document.getElementById('queryParam').value.trim();
            const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
            const filename = `${type}_${param}_${timestamp}.txt`.replace(/[^a-z0-9]/gi, '_').toLowerCase();
            
            const blob = new Blob([content], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            URL.revokeObjectURL(url);
            
            showToast('Dosya indiriliyor...', 'success');
        }
        
        // ==================== RECENT QUERIES ====================
        function addToRecent(type, param, data) {
            const typeNames = {
                'tc': 'TC Sorgu', 'tc2': 'TC Detaylı', 'gsm': 'GSM Sorgu',
                'gsm2': 'GSM Detaylı', 'ad': 'Ad-Soyad', 'aile': 'Aile',
                'sulale': 'Sülale', 'hane': 'Hane', 'isyeri': 'İş Yeri',
                'plaka': 'Plaka', 'vesika': 'Vesika', 'tc-ikametgah': 'İkametgah'
            };
            
            const query = {
                type: typeNames[type] || type,
                param: param,
                time: new Date().toLocaleString('tr-TR'),
                data: data,
                timestamp: Date.now()
            };
            
            recentQueries.unshift(query);
            if (recentQueries.length > 10) recentQueries.pop();
            
            localStorage.setItem('recentQueries', JSON.stringify(recentQueries));
            loadRecentQueries();
        }
        
        function loadRecentQueries() {
            const grid = document.getElementById('recentGrid');
            
            if (recentQueries.length === 0) {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: var(--gray-400); padding: 40px;">Henüz sorgu yapılmadı</p>';
                return;
            }
            
            grid.innerHTML = recentQueries.map(q => `
                <div class="recent-item" onclick='showRecentResult(${JSON.stringify(q.data).replace(/'/g, "\\'")})'>
                    <div class="recent-item-header">
                        <span class="recent-type">${q.type}</span>
                        <span class="recent-time">${q.time}</span>
                    </div>
                    <div class="recent-param">${q.param}</div>
                    <div class="recent-preview">${JSON.stringify(q.data).substring(0, 50)}...</div>
                </div>
            `).join('');
        }
        
        function showRecentResult(data) {
            const resultContent = document.getElementById('resultContent');
            const resultStr = JSON.stringify(data, null, 2);
            
            resultContent.textContent = resultStr;
            
            if (resultStr.length < 1000) {
                resultContent.classList.add('small-result');
            } else {
                resultContent.classList.remove('small-result');
            }
            
            document.getElementById('resultContainer').style.display = 'block';
        }
        
        function clearRecent() {
            if (confirm('Tüm son sorgular silinsin mi?')) {
                recentQueries = [];
                localStorage.removeItem('recentQueries');
                loadRecentQueries();
                showToast('Son sorgular temizlendi', 'success');
            }
        }
        
        // ==================== TOAST ====================
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `
                <div class="toast ${type}">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            setTimeout(() => {
                toast.innerHTML = '';
            }, 3000);
        }
        
        // ==================== INIT ====================
        document.addEventListener('DOMContentLoaded', () => {
            loadRecentQueries();
            setQueryType('tc');
            
            // Close menu on escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                    toggleMenu();
                }
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
