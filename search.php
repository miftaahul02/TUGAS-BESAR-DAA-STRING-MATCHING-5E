<?php
header('Content-Type: application/json');
include 'dataset_judul.php';

function computeLPS_API($pattern) {
    $m = strlen($pattern);
    $lps = array_fill(0, $m, 0);
    $len = 0; $i = 1;
    while ($i < $m) {
        if ($pattern[$i] === $pattern[$len]) {
            $lps[$i++] = ++$len;
        } else {
            if ($len != 0) $len = $lps[$len - 1];
            else $lps[$i++] = 0;
        }
    }
    return $lps;
}

function kmpSearch_API($text, $pattern, $lps) {
    $n = strlen($text);
    $m = strlen($pattern);
    $i = 0; $j = 0;
    while ($i < $n) {
        if ($text[$i] === $pattern[$j]) { $i++; $j++; }
        if ($j === $m) return true;
        elseif ($i < $n && $text[$i] !== $pattern[$j]) {
            $j ? $j = $lps[$j - 1] : $i++;
        }
    }
    return false;
}

$q = $_GET['q'] ?? '';
$hasil = [];

if ($q !== "") {
    $patternLower = strtolower($q);
    $lps = computeLPS_API($patternLower);

    foreach ($dataset_judul as $judul) {
        if (kmpSearch_API(strtolower($judul), $patternLower, $lps)) {
            $hasil[] = $judul;
        }
    }
}

echo json_encode($hasil);
?>