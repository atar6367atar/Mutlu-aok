<?php
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$param = $_GET['param'] ?? '';

if (!$type || !$param) {
    echo json_encode(['success' => false, 'error' => 'Eksik parametre']);
    exit;
}

// API URL'leri
$apis = [
    'tc' => 'https://punisher.alwaysdata.net/apiservices/tc.php?tc=',
    'tcpro' => 'https://punisher.alwaysdata.net/apiservices/tcpro.php?tc=',
    'adsoyad' => 'https://punisher.alwaysdata.net/apiservices/adsoyad.php?',
    'adsoyadpro' => 'https://punisher.alwaysdata.net/apiservices/adsoyadpro.php?',
    'adililce' => 'https://punisher.alwaysdata.net/apiservices/adililce.php?',
    'aile' => 'https://punisher.alwaysdata.net/apiservices/aile.php?tc=',
    'ailepro' => 'https://punisher.alwaysdata.net/apiservices/ailepro.php?tc=',
    'sulale' => 'https://punisher.alwaysdata.net/apiservices/sulale.php?tc=',
    'soyagaci' => 'https://punisher.alwaysdata.net/apiservices/soyagaci.php?tc=',
    'adres' => 'https://punisher.alwaysdata.net/apiservices/adres.php?tc=',
    'adrespro' => 'https://punisher.alwaysdata.net/apiservices/adrespro.php?tc=',
    'isyeri' => 'https://punisher.alwaysdata.net/apiservices/isyeri.php?tc=',
    'isyeriark' => 'https://punisher.alwaysdata.net/apiservices/isyeriark.php?tc=',
    'gncloperator' => 'https://punisher.alwaysdata.net/apiservices/gncloperator.php?numara=',
    'tcgsm' => 'https://punisher.alwaysdata.net/apiservices/tcgsm.php?tc=',
    'gsmtc' => 'https://punisher.alwaysdata.net/apiservices/gsmtc.php?gsm=',
    'iban' => 'https://punisher.alwaysdata.net/apiservices/iban.php?iban=',
    'tg' => 'https://punisher.alwaysdata.net/apiservices/tg.php?username=',
    'okulno' => 'https://punisher.alwaysdata.net/apiservices/okulno.php?tc='
];

if (!isset($apis[$type])) {
    echo json_encode(['success' => false, 'error' => 'Geçersiz tip']);
    exit;
}

// Parametreleri hazırla
$parts = explode(' ', trim($param));

// URL oluştur
switch($type) {
    case 'adsoyad':
        if (count($parts) < 2) {
            echo json_encode(['success' => false, 'error' => 'Ad ve soyad girin']);
            exit;
        }
        $url = $apis[$type] . 'ad=' . urlencode($parts[0]) . '&soyad=' . urlencode($parts[1]);
        break;
        
    case 'adsoyadpro':
        if (count($parts) < 3) {
            echo json_encode(['success' => false, 'error' => 'Ad, soyad ve il girin']);
            exit;
        }
        $url = $apis[$type] . 'ad=' . urlencode($parts[0]) . '&soyad=' . urlencode($parts[1]) . '&il=' . urlencode($parts[2]);
        break;
        
    case 'adililce':
        if (count($parts) < 2) {
            echo json_encode(['success' => false, 'error' => 'Ad ve il girin']);
            exit;
        }
        $url = $apis[$type] . 'ad=' . urlencode($parts[0]) . '&il=' . urlencode($parts[1]);
        break;
        
    default:
        $url = $apis[$type] . urlencode($param);
        break;
}

// API'yi çağır
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    echo json_encode(['success' => false, 'error' => 'API bağlantı hatası: ' . $http_code]);
    exit;
}

// JSON'ı dene, olmazsa ham response'u kullan
$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    // JSON değilse, direkt response'u döndür
    echo json_encode(['success' => true, 'data' => $response], JSON_UNESCAPED_UNICODE);
    exit;
}

// Gereksiz alanları temizle
if (isset($data['success']) || isset($data['status'])) {
    if (isset($data['data'])) {
        $clean_data = $data['data'];
    } elseif (isset($data['results'])) {
        $clean_data = $data['results'];
    } else {
        $clean_data = $data;
        unset($clean_data['geliştirici'], $clean_data['sürüm'], $clean_data['timestamp'],
              $clean_data['developer'], $clean_data['version'], $clean_data['message'],
              $clean_data['status'], $clean_data['success']);
    }
} else {
    $clean_data = $data;
}

echo json_encode(['success' => true, 'data' => $clean_data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
?>
