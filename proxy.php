<?php
header('Content-Type: application/json');

$type = isset($_GET['type']) ? $_GET['type'] : '';
$raw_param = isset($_GET['param']) ? $_GET['param'] : '';

if (empty($type) || empty($raw_param)) {
    echo json_encode(['success' => false, 'error' => 'Eksik parametre']);
    exit;
}

$api_config = [
    'tc' => ['url' => 'https://punisher.alwaysdata.net/apiservices/tc.php', 'params' => ['tc']],
    'tcpro' => ['url' => 'https://punisher.alwaysdata.net/apiservices/tcpro.php', 'params' => ['tc']],
    'adsoyad' => ['url' => 'https://punisher.alwaysdata.net/apiservices/adsoyad.php', 'params' => ['ad', 'soyad']],
    'adsoyadpro' => ['url' => 'https://punisher.alwaysdata.net/apiservices/adsoyadpro.php', 'params' => ['ad', 'soyad', 'il']],
    'adililce' => ['url' => 'https://punisher.alwaysdata.net/apiservices/adililce.php', 'params' => ['ad', 'il']],
    'aile' => ['url' => 'https://punisher.alwaysdata.net/apiservices/aile.php', 'params' => ['tc']],
    'ailepro' => ['url' => 'https://punisher.alwaysdata.net/apiservices/ailepro.php', 'params' => ['tc']],
    'sulale' => ['url' => 'https://punisher.alwaysdata.net/apiservices/sulale.php', 'params' => ['tc']],
    'soyagaci' => ['url' => 'https://punisher.alwaysdata.net/apiservices/soyagaci.php', 'params' => ['tc']],
    'adres' => ['url' => 'https://punisher.alwaysdata.net/apiservices/adres.php', 'params' => ['tc']],
    'adrespro' => ['url' => 'https://punisher.alwaysdata.net/apiservices/adrespro.php', 'params' => ['tc']],
    'isyeri' => ['url' => 'https://punisher.alwaysdata.net/apiservices/isyeri.php', 'params' => ['tc']],
    'isyeriark' => ['url' => 'https://punisher.alwaysdata.net/apiservices/isyeriark.php', 'params' => ['tc']],
    'gncloperator' => ['url' => 'https://punisher.alwaysdata.net/apiservices/gncloperator.php', 'params' => ['numara']],
    'tcgsm' => ['url' => 'https://punisher.alwaysdata.net/apiservices/tcgsm.php', 'params' => ['tc']],
    'gsmtc' => ['url' => 'https://punisher.alwaysdata.net/apiservices/gsmtc.php', 'params' => ['gsm']],
    'iban' => ['url' => 'https://punisher.alwaysdata.net/apiservices/iban.php', 'params' => ['iban']],
    'tg' => ['url' => 'https://punisher.alwaysdata.net/apiservices/tg.php', 'params' => ['username']],
    'okulno' => ['url' => 'https://punisher.alwaysdata.net/apiservices/okulno.php', 'params' => ['tc']],
];

if (!isset($api_config[$type])) {
    echo json_encode(['success' => false, 'error' => 'Geçersiz tip']);
    exit;
}

$config = $api_config[$type];
$param_parts = explode(' ', trim($raw_param));

if (count($param_parts) < count($config['params'])) {
    echo json_encode(['success' => false, 'error' => 'Eksik parametre']);
    exit;
}

$query_params = [];
for ($i = 0; $i < count($config['params']); $i++) {
    $query_params[$config['params'][$i]] = urlencode($param_parts[$i]);
}
$full_url = $config['url'] . '?' . http_build_query($query_params);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $full_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    echo json_encode(['success' => false, 'error' => 'API bağlantı hatası']);
    exit;
}

$api_data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'Geçersiz JSON yanıtı']);
    exit;
}

// Veriyi temizle
$clean_data = $api_data;

if (isset($clean_data['success']) && $clean_data['success'] === true) {
    if (isset($clean_data['results'])) {
        $clean_data = $clean_data['results'];
    } elseif (isset($clean_data['data'])) {
        $clean_data = $clean_data['data'];
    } else {
        unset($clean_data['success']);
        unset($clean_data['geliştirici']);
        unset($clean_data['sürüm']);
        unset($clean_data['timestamp']);
        unset($clean_data['developer']);
        unset($clean_data['version']);
        unset($clean_data['message']);
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
    if (is_array($clean_data)) {
        unset($clean_data['geliştirici']);
        unset($clean_data['sürüm']);
        unset($clean_data['timestamp']);
        unset($clean_data['developer']);
        unset($clean_data['version']);
        unset($clean_data['message']);
        unset($clean_data['status']);
        unset($clean_data['success']);
    }
}

echo json_encode(['success' => true, 'data' => $clean_data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
