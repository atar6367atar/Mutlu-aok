<?php
// =============================================
// NGB SORGU PANELİ - PROXY SİSTEMİ
// TÜM API İSTEKLERİ BU DOSYA ÜZERİNDEN GEÇER
// =============================================

// Hata raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS ayarları - Herkese açık
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// OPTIONS isteğine cevap ver
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// API Base URL
define('API_BASE', 'https://punisherapi.alwaysdata.net/apiservices/');

// API endpoint'leri
$endpoints = [
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

// Parametre isimleri
$paramNames = [
    'tc1' => ['tc'],
    'tc2' => ['tc'],
    'isim' => ['ad', 'soyad'],
    'isim_pro' => ['ad', 'soyad', 'il'],
    'isim_il' => ['ad', 'il', 'ilce'],
    'aile' => ['tc'],
    'aile_pro' => ['tc'],
    'sulale' => ['tc'],
    'adres' => ['tc'],
    'adres_pro' => ['tc'],
    'isyeri' => ['tc'],
    'isyeri_ark' => ['tc'],
    'gncloperator' => ['numara'],
    'tcgsm' => ['tc'],
    'gsmtc' => ['gsm'],
    'iban' => ['iban']
];

// İstek tipini kontrol et
$type = isset($_GET['type']) ? $_GET['type'] : '';
$params = isset($_GET['params']) ? $_GET['params'] : '';

if (empty($type) || !isset($endpoints[$type])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Geçersiz sorgu tipi',
        'available_types' => array_keys($endpoints)
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Parametreleri çözümle
$paramValues = explode('|', $params);
$expectedCount = count($paramNames[$type]);

if (count($paramValues) < $expectedCount) {
    echo json_encode([
        'success' => false, 
        'error' => 'Eksik parametre',
        'expected' => $paramNames[$type],
        'received' => $paramValues
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// URL oluştur
$url = API_BASE . $endpoints[$type] . '?';
$queryParams = [];

for ($i = 0; $i < $expectedCount; $i++) {
    $queryParams[$paramNames[$type][$i]] = $paramValues[$i];
}

$url .= http_build_query($queryParams);

// Log (isteğe bağlı)
error_log("Proxy İstek: " . $url);

// cURL ile API'ye bağlan
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_error($ch)) {
    echo json_encode([
        'success' => false, 
        'error' => 'cURL Hatası: ' . curl_error($ch),
        'url' => $url
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    curl_close($ch);
    exit;
}

curl_close($ch);

if ($http_code == 200 && !empty($response)) {
    // JSON kontrolü
    $data = json_decode($response, true);
    
    if ($data) {
        // Reklamları temizle
        $cleanData = cleanApiData($data);
        echo json_encode([
            'success' => true, 
            'data' => $cleanData,
            'source_url' => $url
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        // JSON değilse direkt gönder
        echo json_encode([
            'success' => true, 
            'data' => $response,
            'source_url' => $url
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'API yanıt vermiyor',
        'http_code' => $http_code,
        'url' => $url
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

// Veri temizleme fonksiyonu
function cleanApiData($data) {
    if (is_array($data)) {
        $cleaned = [];
        foreach ($data as $key => $value) {
            // Reklam içeren anahtarları atla
            $bannedKeys = ['developer', 'geliştirici', 'version', 'sürüm', 'reklam', 'kanal', 'telegram', 'punisher', 'admin', 'destek', 'copyright'];
            
            $skip = false;
            foreach ($bannedKeys as $banned) {
                if (stripos($key, $banned) !== false) {
                    $skip = true;
                    break;
                }
            }
            
            if ($skip) continue;
            
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
                $value = preg_replace('/alwaysdata/i', '', $value);
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
?>
