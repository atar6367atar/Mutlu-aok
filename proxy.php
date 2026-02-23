<?php
// =============================================
// DASSY TAG - GÜÇLÜ PROXY (KESİN ÇÖZÜM)
// Tüm API'leri çalıştırır, reklamları temizler
// =============================================

// Hataları göster (debug için)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS izni
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json');

// Zaman aşımı
set_time_limit(45);
ini_set('max_execution_time', 45);

// Log fonksiyonu
function logError($msg) {
    file_put_contents('proxy_log.txt', date('Y-m-d H:i:s') . ' - ' . $msg . PHP_EOL, FILE_APPEND);
}

// API'den veri çek
function fetchAPI($url) {
    logError("İstek: " . $url);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Connection: keep-alive'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        logError("CURL Hatası: " . $error);
        return null;
    }
    
    if ($httpCode != 200) {
        logError("HTTP Hatası: " . $httpCode);
        return null;
    }
    
    return $response;
}

// Parametreler
$type = isset($_GET['type']) ? $_GET['type'] : '';
$param = isset($_GET['param']) ? $_GET['param'] : '';

if (empty($type) || empty($param)) {
    echo json_encode(['success' => false, 'error' => 'Parametre eksik']);
    exit;
}

// API Base URL (HTTP kullan!)
$API_BASE = 'http://punisher.alwaysdata.net/apiservices/';

// URL oluştur
$url = '';
switch ($type) {
    case 'tc':
        $url = $API_BASE . "tc.php?tc=" . urlencode($param);
        break;
    case 'tcpro':
        $url = $API_BASE . "tcpro.php?tc=" . urlencode($param);
        break;
    case 'adsoyad':
        $parts = explode(' ', $param, 2);
        $ad = urlencode($parts[0]);
        $soyad = isset($parts[1]) ? urlencode($parts[1]) : '';
        $url = $API_BASE . "adsoyad.php?ad=$ad&soyad=$soyad";
        break;
    case 'adsoyadpro':
        $parts = explode(' ', $param, 3);
        $ad = urlencode($parts[0]);
        $soyad = isset($parts[1]) ? urlencode($parts[1]) : '';
        $il = isset($parts[2]) ? urlencode($parts[2]) : '';
        $url = $API_BASE . "adsoyadpro.php?ad=$ad&soyad=$soyad&il=$il";
        break;
    case 'adililce':
        $parts = explode(' ', $param, 2);
        $ad = urlencode($parts[0]);
        $il = isset($parts[1]) ? urlencode($parts[1]) : '';
        $url = $API_BASE . "adililce.php?ad=$ad&il=$il";
        break;
    case 'aile':
        $url = $API_BASE . "aile.php?tc=" . urlencode($param);
        break;
    case 'ailepro':
        $url = $API_BASE . "ailepro.php?tc=" . urlencode($param);
        break;
    case 'sulale':
        $url = $API_BASE . "sulale.php?tc=" . urlencode($param);
        break;
    case 'soyagaci':
        $url = $API_BASE . "soyagaci.php?tc=" . urlencode($param);
        break;
    case 'adres':
        $url = $API_BASE . "adres.php?tc=" . urlencode($param);
        break;
    case 'adrespro':
        $url = $API_BASE . "adrespro.php?tc=" . urlencode($param);
        break;
    case 'isyeri':
        $url = $API_BASE . "isyeri.php?tc=" . urlencode($param);
        break;
    case 'isyeriark':
        $url = $API_BASE . "isyeriark.php?tc=" . urlencode($param);
        break;
    case 'gncloperator':
        $url = $API_BASE . "gncloperator.php?numara=" . urlencode($param);
        break;
    case 'tcgsm':
        $url = $API_BASE . "tcgsm.php?tc=" . urlencode($param);
        break;
    case 'gsmtc':
        $url = $API_BASE . "gsmtc.php?gsm=" . urlencode($param);
        break;
    case 'iban':
        $url = $API_BASE . "iban.php?iban=" . urlencode($param);
        break;
    case 'sms':
        $url = $API_BASE . "sms.php?gsm=" . urlencode($param);
        break;
    case 'tg':
        $username = str_replace('@', '', $param);
        $url = $API_BASE . "tg.php?username=" . urlencode($username);
        break;
    case 'okulno':
        $url = $API_BASE . "okulno.php?tc=" . urlencode($param);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Geçersiz tip']);
        exit;
}

// API'den veri çek
$response = fetchAPI($url);

if (!$response) {
    // İkinci deneme - alternatif URL
    $url2 = str_replace('http://', 'https://', $url);
    $response = fetchAPI($url2);
}

if (!$response) {
    echo json_encode([
        'success' => false, 
        'error' => 'API yanıt vermiyor. Lütfen daha sonra tekrar deneyin.',
        'url' => $url
    ]);
    exit;
}

// JSON çöz
$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        'success' => false,
        'error' => 'Geçersiz JSON yanıtı',
        'raw' => substr($response, 0, 200)
    ]);
    exit;
}

// Reklamları temizle
if (is_array($data)) {
    unset($data['geliştirici']);
    unset($data['sürüm']);
    unset($data['reklam']);
    unset($data['auth']);
    unset($data['api_sahibi']);
    unset($data['developer']);
    unset($data['version']);
    unset($data['ads']);
    unset($data['sponsor']);
}

// Başarılı yanıt
echo json_encode([
    'success' => true,
    'data' => $data,
    'type' => $type,
    'param' => $param,
    'time' => date('Y-m-d H:i:s')
]);
