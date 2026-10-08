<!DOCTYPE html>
<html lang="hu">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
  <title>Gyűjtemény · QR Kereső</title>
</head>

<body class="collection-page">

  <div class="watermark" aria-hidden="true"></div>

  <div class="collection-wrap">

    <div class="collection-topbar">
      <a href="./index.php" class="back-btn" aria-label="Vissza">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15 18l-6-6 6-6" />
        </svg>
        <span>Vissza</span>
      </a>
      <h1>Trófeagyűjtemény</h1>
      <p class="collection-sub" id="collectionSub">Betöltés…</p>
      <p id="completeBanner" class="complete-banner" role="button" tabindex="-1" aria-live="polite" hidden>
        <span id="completeBannerText">🎉 Összegyűjtötted az összes trófeát! Gyere el hozzánk a jutalmadért!</span>
        <span id="redemptionProgress" class="redemption-progress" hidden></span>
      </p>
    </div>

    <div class="collection-grid" id="collectionGrid">
      <div class="trophy-slot" data-trophy="qr_1">
        <img src="./assets/img/stickers/audit.png" alt="Trófea 1">
        <span>Pénzügyi-számviteli ügyintéző</span>
      </div>
      <div class="trophy-slot" data-trophy="qr_2">
        <img src="./assets/img/stickers/birthday-cake.png" alt="Trófea 2">
        <span>Cukrász</span>
      </div>
      <div class="trophy-slot" data-trophy="qr_3">
        <img src="./assets/img/stickers/chef.png" alt="Trófea 3">
        <span>Szakács</span>
      </div>
      <div class="trophy-slot" data-trophy="qr_4">
        <img src="./assets/img/stickers/circular-saw.png" alt="Trófea 4">
        <span>CNC-programozó</span>
      </div>
      <div class="trophy-slot" data-trophy="qr_5">
        <img src="./assets/img/stickers/laptop.png" alt="Trófea 5">
        <span>Szoftverfejlesztő és -tesztelő</span>
      </div>
      <div class="trophy-slot" data-trophy="qr_6">
        <img src="./assets/img/stickers/robot-arm.png" alt="Trófea 6">
        <span>Mechatronikai technikus</span>
      </div>
      <div class="trophy-slot" data-trophy="qr_7">
        <img src="./assets/img/stickers/traveler.png" alt="Trófea 7">
        <span>Turisztikai technikus</span>
      </div>
      <div class="trophy-slot" data-trophy="qr_8">
        <img src="./assets/img/stickers/waiter.png" alt="Trófea 8">
        <span>Pincér - vendégtéri szakember</span>
      </div>
    </div>

  </div>

  <div class="reward-overlay" id="rewardOverlay" hidden>
    <div class="reward-card" role="dialog" aria-modal="true" aria-labelledby="rewardTitle">
      <button type="button" class="reward-close" id="rewardClose" aria-label="Bezárás">✕</button>
      <img class="reward-img" id="rewardImg" src="" alt="">
      <h2 id="rewardTitle">Új trófea!</h2>
      <p id="rewardDesc"></p>
      <button type="button" class="reward-ok" id="rewardOk">Szuper!</button>
    </div>
  </div>

  <div class="reward-overlay" id="completeOverlay" hidden>
    <div class="reward-card complete-card" role="dialog" aria-modal="true" aria-labelledby="completeTitle">
      <button type="button" class="reward-close" id="completeClose" aria-label="Bezárás">✕</button>
      <div class="complete-icon">🏆</div>
      <h2 id="completeTitle">Gratulálunk!</h2>
      <p id="completeDesc"></p>
      <button type="button" class="reward-ok" id="completeOk">Szuper!</button>
    </div>
  </div>

  <script src="./assets/js/trophies.js"></script>
  <script src="./assets/js/collection.js"></script>

</body>

</html>
