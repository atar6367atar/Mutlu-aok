<?php
session_start();

// Şifre
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
    <title>DASSY TAG | Sorgu Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Login */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-box {
            background: white;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            padding: 50px;
            width: 100%;
            max-width: 400px;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo i {
            font-size: 60px;
            color: #667eea;
        }

        .logo h1 {
            font-size: 32px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 15px 0 5px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            font-size: 16px;
        }

        .input-group input:focus {
            border-color: #667eea;
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 15px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102,126,234,0.4);
        }

        .error {
            background: #fee;
            color: #e74c3c;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Dashboard */
        .dashboard {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .header {
            background: white;
            border-radius: 20px;
            padding: 20px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 24px;
            color: #2c3e50;
        }

        .header h1 i {
            color: #667eea;
            margin-right: 10px;
        }

        .logout-btn {
            background: #fee;
            color: #e74c3c;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .quick-btn {
            background: white;
            border: none;
            border-radius: 15px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .quick-btn i {
            font-size: 28px;
            color: #667eea;
        }

        .quick-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(102,126,234,0.3);
        }

        .query-box {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .query-type {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .query-type i {
            font-size: 32px;
            color: #667eea;
            background: rgba(102,126,234,0.1);
            padding: 15px;
            border-radius: 15px;
        }

        .query-type h2 {
            font-size: 24px;
            color: #2c3e50;
        }

        .query-type span {
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 14px;
            margin-left: auto;
        }

        .input-area {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .input-area input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            font-size: 16px;
        }

        .input-area input:focus {
            border-color: #667eea;
            outline: none;
        }

        .input-area button {
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 15px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .input-area button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102,126,234,0.4);
        }

        .input-area button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .example {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .loader {
            display: none;
            text-align: center;
            padding: 30px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .result {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            display: none;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .result-header h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #27ae60;
        }

        .result-actions {
            display: flex;
            gap: 10px;
        }

        .result-actions button {
            padding: 8px 15px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .result-actions button:hover {
            border-color: #667eea;
            color: #667eea;
        }

        .result-content {
            background: white;
            border-radius: 10px;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.6;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .result-content.small {
            background: #f0f9ff;
            border-left: 5px solid #667eea;
        }

        .recent {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .recent h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .recent-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .recent-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .recent-item:hover {
            background: #edf2f7;
            transform: translateX(5px);
        }

        .recent-type {
            background: #667eea;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            display: inline-block;
            margin-bottom: 8px;
        }

        .recent-param {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .recent-time {
            font-size: 11px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <?php if (!$giris_yapildi): ?>
    <!-- LOGIN -->
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <i class="fas fa-shield-hal"></i>
                <h1>DASSY TAG</h1>
                <p>Profesyonel Sorgu Paneli</p>
            </div>
            
            <?php if (isset($hata)): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i> <?= $hata ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="input-group">
                    <input type="password" name="password" placeholder="Şifre" required>
                </div>
                <button type="submit" name="login" class="login-btn">GİRİŞ YAP</button>
            </form>
        </div>
    </div>
    
    <?php else: ?>
    <!-- DASHBOARD -->
    <div class="dashboard">
        <div class="header">
            <h1>
                <i class="fas fa-shield-hal"></i>
                DASSY TAG Sorgu Paneli
            </h1>
            <a href="?logout=1" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Çıkış
            </a>
        </div>
        
        <!-- Hızlı Butonlar -->
        <div class="quick-actions">
            <button class="quick-btn" onclick="setType('tc')">
                <i class="fas fa-id-card"></i>
                <span>TC</span>
            </button>
            <button class="quick-btn" onclick="setType('gsm')">
                <i class="fas fa-mobile-alt"></i>
                <span>GSM</span>
            </button>
            <button class="quick-btn" onclick="setType('ad')">
                <i class="fas fa-user"></i>
                <span>Ad-Soyad</span>
            </button>
            <button class="quick-btn" onclick="setType('aile')">
                <i class="fas fa-users"></i>
                <span>Aile</span>
            </button>
            <button class="quick-btn" onclick="setType('plaka')">
                <i class="fas fa-car"></i>
                <span>Plaka</span>
            </button>
        </div>
        
        <!-- Sorgu Kutusu -->
        <div class="query-box">
            <div class="query-type">
                <i class="fas fa-id-card" id="typeIcon"></i>
                <h2 id="typeTitle">TC Sorgulama</h2>
                <span id="typeBadge">tc</span>
            </div>
            
            <div class="input-area">
                <input type="text" id="queryParam" placeholder="Parametre girin..." onkeypress="if(event.key==='Enter') executeQuery()">
                <button onclick="executeQuery()" id="queryBtn">
                    <i class="fas fa-search"></i>
                    Sorgula
                </button>
            </div>
            
            <div class="example" id="queryExample">
                <i class="fas fa-info-circle"></i> Örnek: 12345678901
            </div>
            
            <!-- Loader -->
            <div class="loader" id="queryLoader">
                <div class="spinner"></div>
                <p>Sorgulanıyor... (max 30 saniye)</p>
            </div>
            
            <!-- Sonuç -->
            <div class="result" id="resultContainer">
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
        
        <!-- Son Sorgular -->
        <div class="recent">
            <h2>
                <i class="fas fa-history"></i>
                Son Sorgular
            </h2>
            <div class="recent-grid" id="recentGrid">
                <!-- JS ile doldurulacak -->
            </div>
        </div>
    </div>
    
    <script>
        // API Base URL
        const API_BASE = 'https://botapi-jjwj.onrender.com';
        
        // State
        let currentType = 'tc';
        let recentQueries = JSON.parse(localStorage.getItem('recentQueries')) || [];
        
        // Query types
        const types = {
            'tc': {
                name: 'TC Sorgulama',
                icon: 'fa-id-card',
                example: '12345678901',
                badge: 'tc',
                url: (p) => `${API_BASE}/tc?tc=${p}&format=json`
            },
            'tc2': {
                name: 'TC Detaylı',
                icon: 'fa-id-card',
                example: '12345678901',
                badge: 'tc2',
                url: (p) => `${API_BASE}/tc2?tc=${p}&format=json`
            },
            'gsm': {
                name: 'GSM Sorgulama',
                icon: 'fa-mobile-alt',
                example: '5346149118',
                badge: 'gsm',
                url: (p) => `${API_BASE}/gsm?gsm=${p}&format=json`
            },
            'gsm2': {
                name: 'GSM Detaylı',
                icon: 'fa-mobile-alt',
                example: '5346149118',
                badge: 'gsm2',
                url: (p) => `${API_BASE}/gsm2?gsm=${p}&format=json`
            },
            'ad': {
                name: 'Ad-Soyad',
                icon: 'fa-user',
                example: 'EYMEN YAVUZ',
                badge: 'ad',
                url: (p) => {
                    const parts = p.split(' ');
                    return `${API_BASE}/ad?name=${parts[0]}&surname=${parts[1] || ''}&format=json`;
                }
            },
            'aile': {
                name: 'Aile Sorgulama',
                icon: 'fa-users',
                example: '12345678901',
                badge: 'aile',
                url: (p) => `${API_BASE}/aile?tc=${p}&format=json`
            },
            'sulale': {
                name: 'Sülale Sorgulama',
                icon: 'fa-tree',
                example: '12345678901',
                badge: 'sulale',
                url: (p) => `${API_BASE}/sulale?tc=${p}&format=json`
            },
            'hane': {
                name: 'Hane Sorgulama',
                icon: 'fa-home',
                example: '12345678901',
                badge: 'hane',
                url: (p) => `${API_BASE}/hane?tc=${p}&format=json`
            },
            'isyeri': {
                name: 'İş Yeri Sorgulama',
                icon: 'fa-briefcase',
                example: '12345678901',
                badge: 'isyeri',
                url: (p) => `${API_BASE}/isyeri?tc=${p}&format=json`
            },
            'plaka': {
                name: 'Plaka Sorgulama',
                icon: 'fa-car',
                example: '34AKP34',
                badge: 'plaka',
                url: (p) => `${API_BASE}/plaka?plaka=${p}&format=json`
            },
            'vesika': {
                name: 'Vesika Sorgulama',
                icon: 'fa-id-card',
                example: '12345678901',
                badge: 'vesika',
                url: (p) => `${API_BASE}/vesika?tc=${p}&format=json`
            },
            'tc-ikametgah': {
                name: 'İkametgah Sorgulama',
                icon: 'fa-map-marker-alt',
                example: '12345678901',
                badge: 'ikametgah',
                url: (p) => `${API_BASE}/tc-ikametgah?tc=${p}&format=json`
            }
        };
        
        // Set query type
        function setType(type) {
            currentType = type;
            const t = types[type];
            
            document.getElementById('typeIcon').className = `fas ${t.icon}`;
            document.getElementById('typeTitle').textContent = t.name;
            document.getElementById('typeBadge').textContent = t.badge;
            document.getElementById('queryExample').innerHTML = `<i class="fas fa-info-circle"></i> Örnek: ${t.example}`;
        }
        
        // Execute query
        async function executeQuery() {
            const param = document.getElementById('queryParam').value.trim();
            
            if (!param) {
                alert('Parametre girin!');
                return;
            }
            
            const t = types[currentType];
            const url = t.url(encodeURIComponent(param));
            
            // Show loader
            document.getElementById('queryLoader').style.display = 'block';
            document.getElementById('resultContainer').style.display = 'none';
            document.getElementById('queryBtn').disabled = true;
            
            // Timeout 30 saniye
            const timeout = setTimeout(() => {
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                alert('Zaman aşımı! (30 saniye)');
            }, 30000);
            
            try {
                // API'ye direkt istek
                const response = await fetch(url);
                clearTimeout(timeout);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const data = await response.json();
                
                // Hide loader
                document.getElementById('queryLoader').style.display = 'none';
                document.getElementById('queryBtn').disabled = false;
                
                // Show result
                const resultContent = document.getElementById('resultContent');
                const resultStr = JSON.stringify(data, null, 2);
                
                resultContent.textContent = resultStr;
                
                // Küçük sonuçsa özel stil
                if (resultStr.length < 1000) {
                    resultContent.classList.add('small');
                } else {
                    resultContent.classList.remove('small');
                }
                
                document.getElementById('resultContainer').style.display = 'block';
                
                // Son sorgulara ekle
                addToRecent(currentType, param, data);
                
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
                alert('Kopyalandı!');
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
                type: types[type].name,
                param: param,
                time: new Date().toLocaleString('tr-TR'),
                data: data
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
                grid.innerHTML = '<p style="text-align: center; color: #999;">Henüz sorgu yok</p>';
                return;
            }
            
            grid.innerHTML = recentQueries.map(q => `
                <div class="recent-item" onclick='showRecent(${JSON.stringify(q.data)})'>
                    <span class="recent-type">${q.type}</span>
                    <div class="recent-param">${q.param}</div>
                    <div class="recent-time">${q.time}</div>
                </div>
            `).join('');
        }
        
        // Show recent
        function showRecent(data) {
            const resultContent = document.getElementById('resultContent');
            resultContent.textContent = JSON.stringify(data, null, 2);
            document.getElementById('resultContainer').style.display = 'block';
        }
        
        // Clear recent
        function clearRecent() {
            if (confirm('Son sorgular silinsin mi?')) {
                recentQueries = [];
                localStorage.removeItem('recentQueries');
                loadRecent();
            }
        }
        
        // Load on start
        document.addEventListener('DOMContentLoaded', () => {
            setType('tc');
            loadRecent();
        });
    </script>
    <?php endif; ?>
</body>
</html>
