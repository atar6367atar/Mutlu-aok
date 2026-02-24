<?php
// =============================================
// NGB SORGU PANELİM.IO - PROXY (API TEMİZLEYİCİ)
// Tarih: 24.02.2026
// =============================================

header('Content-Type: application/json');

// Gelen parametreleri al
$type = isset($_GET['type']) ? $_GET['type'] : '';
$raw_param = isset($_GET['param']) ? $_GET['param'] : '';

if (empty($type) || empty($raw_param)) {
    echo json_encode(['success' => false, 'error' => 'Eksik parametre (type veya param)']);
    exit;
}

// API URL'lerini ve parametre dönüşümlerini tanımla
$api_config = [
    // TC
    'tc' => ['url' => 'https://punisher.alwaysdata.net/apiservices/tc.php', 'params' => ['tc']],
    'tcpro' => ['url' => 'https://punisher.alwaysdata.net/apiservices/tcpro.php', 'params' => ['tc']],

    // İSİM
    'adsoyad' => ['url' => 'https://punisher.alwaysdata.net/apiservices/adsoyad.php', 'params' => ['ad', 'soyad']],
    'adsoyadpro' => ['url' => 'https://punisher.alwaysdata.net/apiservices/adsoyadpro.php', 'params' => ['ad', 'soyad', 'il']],
    'adililce' => ['url' => 'https://punisher.alwaysdata.net/apiservices/adililce.php', 'params' => ['ad', 'il']],

    // AİLE
    'aile' => ['url' => 'https://punisher.alwaysdata.net/apiservices/aile.php', 'params' => ['tc']],
    'ailepro' => ['url' => 'https://punisher.alwaysdata.net/apiservices/ailepro.php', 'params' => ['tc']],
    'sulale' => ['url' => 'https://punisher.alwaysdata.net/apiservices/sulale.php', 'params' => ['tc']],
    'soyagaci' => ['url' => 'https://punisher.alwaysdata.net/apiservices/soyagaci.php', 'params' => ['tc']],

    // ADRES
    'adres' => ['url' => 'https://punisher.alwaysdata.net/apiservices/adres.php', 'params' => ['tc']],
    'adrespro' => ['url' => 'https://punisher.alwaysdata.net/apiservices/adrespro.php', 'params' => ['tc']],

    // İŞ
    'isyeri' => ['url' => 'https://punisher.alwaysdata.net/apiservices/isyeri.php', 'params' => ['tc']],
    'isyeriark' => ['url' => 'https://punisher.alwaysdata.net/apiservices/isyeriark.php', 'params' => ['tc']],

    // GSM
    'gncloperator' => ['url' => 'https://punisher.alwaysdata.net/apiservices/gncloperator.php', 'params' => ['numara']],
    'tcgsm' => ['url' => 'https://punisher.alwaysdata.net/apiservices/tcgsm.php', 'params' => ['tc']],
    'gsmtc' => ['url' => 'https://punisher.alwaysdata.net/apiservices/gsmtc.php', 'params' => ['gsm']],

    // FİNANS
    'iban' => ['url' => 'https://punisher.alwaysdata.net/apiservices/iban.php', 'params' => ['iban']],

    // SOSYAL
    'tg' => ['url' => 'https://punisher.alwaysdata.net/apiservices/tg.php', 'params' => ['username']],

    // EĞİTİM
    'okulno' => ['url' => 'https://punisher.alwaysdata.net/apiservices/okulno.php', 'params' => ['tc']],
];

if (!isset($api_config[$type])) {
    echo json_encode(['success' => false, 'error' => 'Geçersiz sorgu tipi']);
    exit;
}

$config = $api_config[$type];
$api_url = $config['url'];
$param_names = $config['params'];

// Gelen ham parametreyi parçala (örn: "roket atar bursa" -> ["roket", "atar", "bursa"])
$param_parts = explode(' ', trim($raw_param));

// Eğer beklenen parametre sayısı kadar parça yoksa hata ver
if (count($param_parts) < count($param_names)) {
    echo json_encode(['success' => false, 'error' => 'Eksik parametre. Örnek kullanım: ' . implode(' ', $param_names)]);
    exit;
}

// API için query string oluştur
$query_params = [];
for ($i = 0; $i < count($param_names); $i++) {
    $query_params[$param_names[$i]] = urlencode($param_parts[$i]);
}
$full_url = $api_url . '?' . http_build_query($query_params, '', '&', PHP_QUERY_RFC3986);

// cURL ile API'yi çağır
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $full_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // API'ler 10 saniye bekletebiliyor
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Yerel testler için
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'NGB-Panel-Proxy/1.0');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo json_encode(['success' => false, 'error' => 'API bağlantı hatası: ' . $curl_error]);
    exit;
}

if ($http_code !== 200) {
    echo json_encode(['success' => false, 'error' => 'API HTTP Hata Kodu: ' . $http_code]);
    exit;
}

// Gelen JSON'ı parse et
$api_data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'API geçersiz JSON yanıtı verdi: ' . json_last_error_msg()]);
    exit;
}

// --- VERİYİ TEMİZLE (Geliştirici, timestamp vb. alanları kaldır) ---
$clean_data = $api_data;

// Eğer success/status varsa ve true ise, içindeki veriyi çekmeye çalış
if (isset($clean_data['success']) && $clean_data['success'] === true) {
    if (isset($clean_data['results'])) {
        $clean_data = $clean_data['results'];
    } elseif (isset($clean_data['data'])) {
        $clean_data = $clean_data['data'];
    } else {
        // success true ama results/data yoksa, success dışındaki alanları temizle
        unset($clean_data['success']);
        unset($clean_data['geliştirici']);
        unset($clean_data['sürüm']);
        unset($clean_data['timestamp']);
        unset($clean_data['developer']);
        unset($clean_data['version']);
        unset($clean_data['api_ismi']);
        unset($clean_data['message']);
        unset($clean_data['status']);
    }
} elseif (isset($clean_data['status']) && $clean_data['status'] === true) {
     if (isset($clean_data['data'])) {
        $clean_data = $clean_data['data'];
     } else {
        unset($clean_data['status']);
        unset($clean_data['developer']);
        unset($clean_data['version']);
        unset($clean_data['timestamp']);
        unset($clean_data['message']);
     }
} else {
    // Eğer success/status yapısı yoksa (gsmtc, tc gibi direkt dizi dönenler) veya false ise,
    // sadece meta alanları temizle
    if (is_array($clean_data)) {
        unset($clean_data['geliştirici']);
        unset($clean_data['sürüm']);
        unset($clean_data['timestamp']);
        unset($clean_data['developer']);
        unset($clean_data['version']);
        unset($clean_data['api_ismi']);
        unset($clean_data['message']);
        unset($clean_data['status']);
        unset($clean_data['success']);
    }
}

// Başarılı yanıtı ve temizlenmiş veriyi panele gönder
echo json_encode(['success' => true, 'data' => $clean_data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>
