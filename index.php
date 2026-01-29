<?php
include 'dataset_judul.php';

function computeLPS($pattern) {
    $m = strlen($pattern);
    $lps = array_fill(0, $m, 0);
    $len = 0; 
    $i = 1;
    while ($i < $m) {
        if ($pattern[$i] === $pattern[$len]) {
            $lps[$i++] = ++$len;
        } else {
            if ($len != 0) {
                $len = $lps[$len - 1];
            } else {
                $lps[$i++] = 0;
            }
        }
    }
    return $lps;
}

function kmpSearch($text, $pattern, $lps) {
    $n = strlen($text);
    $m = strlen($pattern);
    $i = 0; $j = 0;
    while ($i < $n) {
        if ($text[$i] === $pattern[$j]) {
            $i++; $j++;
        }
        if ($j === $m) return true;
        elseif ($i < $n && $text[$i] !== $pattern[$j]) {
            $j ? $j = $lps[$j - 1] : $i++;
        }
    }
    return false;
}

function highlight($text, $key) {
    $safeKey = preg_quote($key, "/");
    return preg_replace(
        "/($safeKey)/i",
        "<span class='highlight'>$1</span>",
        htmlspecialchars($text)
    );
}

$q = $_GET['judul'] ?? "";
$hasil = [];

$layoutClass = ($q === "") ? "home-mode" : "result-mode";

if ($q !== "") {
    $patternLower = strtolower($q);
    $lps = computeLPS($patternLower);

    foreach ($dataset_judul as $judul) {
        if (kmpSearch(strtolower($judul), $patternLower, $lps)) {
            $hasil[] = $judul;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Skripsi Library</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container <?= $layoutClass ?>">
    
    <div class="left">
        <div class="header-brand">
            <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" width="50" alt="Logo">
            <h2>Digital Skripsi Library</h2>
        </div>

        <form method="get" class="search-box">
            
            <div class="input-group">
                <input type="text" id="search" name="judul" 
                       placeholder="Cari judul skripsi..." 
                       value="<?= htmlspecialchars($q) ?>"
                       autocomplete="off">
                
                <ul id="suggestion"></ul>
            </div>

            <button type="submit">🔍</button>
        </form>
    </div>

    <?php if ($layoutClass === "result-mode"): ?>
    <div class="right">
        <h3>Hasil Pencarian</h3>

        <?php if (empty($hasil)): ?>
            <div class="not-found">
                <p>Tidak ditemukan judul dengan kata <b>"<?= htmlspecialchars($q) ?>"</b></p>
                <p style="font-size: 14px; color: #999;">Coba gunakan kata kunci lain.</p>
            </div>
        <?php else: ?>
            <p class="stats">Ditemukan <?= count($hasil) ?> judul.</p>
            <?php foreach ($hasil as $judul): ?>
                <div class="item">
                    <?= highlight($judul, $q) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<script src="script.js"></script>
</body>
</html>