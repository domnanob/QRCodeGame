<!DOCTYPE html>
<html lang="hu">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
  <title>QR Kereső</title>
  <?php
    $showRedirectModal = isset($_GET['redirect']);
    function init()
  {
    $rid = unique_id(5);
    setcookie(hash("sha256", "token"), $rid, time()+60*60*24*365, "/");
  }
  function unique_id($l = 8)
  {
    return substr(md5(uniqid(mt_rand(), true)), 0, $l);
  }
  if (!isset($_COOKIE[hash("sha256", "token")])) {
    init();
  }
  ?>
</head>

<body>

  <div class="watermark" aria-hidden="true"></div>

  <a href="./collection.php" class="trophy-btn" id="trophyBtn" aria-label="Gyűjtemény megnyitása">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
      stroke-linejoin="round">
      <path d="M8 4h8v4a4 4 0 0 1-8 0V4Z" />
      <path d="M8 5H5a2 2 0 0 0 0 4c.6 1.2 1.6 2 2.7 2.4" />
      <path d="M16 5h3a2 2 0 0 1 0 4c-.6 1.2-1.6 2-2.7 2.4" />
      <path d="M12 12v3" />
      <path d="M9 19h6" />
      <path d="M10 15h4l.6 4H9.4l.6-4Z" />
    </svg>
    <span class="trophy-badge" id="trophyBadge"><span id="pointsSpan">0</span>/8</span>
  </a>

  <div class="content">

    <h1>Szkenneld be a kódot</h1>
    <p class="intro-text">Gyűjtsd össze az elrejtett QR kódokat, és szerezd meg a jutalmad! 🏆</p>

    <button type="button" class="scan-btn" id="scanBtn" aria-label="QR kód beolvasása">
      <span class="ring"></span>
      <span class="ring"></span>
      <span class="spinner"></span>
      <svg id="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
        stroke-linejoin="round">
        <path
          d="M4 8a2 2 0 0 1 2-2h1.2a1 1 0 0 0 .89-.55l.5-1A1 1 0 0 1 9.48 4h5.04a1 1 0 0 1 .9.55l.5 1a1 1 0 0 0 .88.55H18a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z" />
        <circle cx="12" cy="13" r="3.5" />
      </svg>
    </button>

    <p id="status">Koppints az ikonra, és fényképezd le a kódot</p>
    <button type="button" id="retry">Új szkennelés</button>

    <input type="file" id="qrImage" accept="image/*" capture="environment">

    <p id="completeBanner" class="complete-banner" role="button" tabindex="-1" aria-live="polite" hidden>
      <span id="completeBannerText">🎉 Összegyűjtötted az összes trófeát! Gyere a Nádasdy Standjához a jutalmadért!</span>
      <span id="redemptionProgress" class="redemption-progress" hidden></span>
    </p>

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

  <?php if ($showRedirectModal): ?>
    <div class="reward-overlay" id="redirectOverlay" hidden>
      <div class="reward-card" role="dialog" aria-modal="true" aria-labelledby="redirectTitle">
        <button type="button" class="reward-close" id="redirectClose" aria-label="Bezárás">✕</button>
        <img class="reward-img" src="./assets/img/stickers/traveler.png" alt="Mosolygó utazó">
        <h2 id="redirectTitle">Szia, QR-vadász! 👋</h2>
        <p>A matricagyűjtéshez az ezen az oldalon található olvasóval olvasd be a QR-kódokat! A trófeákat csakis így szerezheted meg! ✨</p>
        <button type="button" class="reward-ok" id="redirectOk">Értem</button>
      </div>
    </div>
    <script>
      const redirectOverlay = document.getElementById('redirectOverlay');
      const closeRedirectModal = () => {
        redirectOverlay.classList.remove('open');
        setTimeout(() => { redirectOverlay.hidden = true; }, 250);
      };

      redirectOverlay.hidden = false;
      requestAnimationFrame(() => redirectOverlay.classList.add('open'));
      document.getElementById('redirectClose').addEventListener('click', closeRedirectModal);
      document.getElementById('redirectOk').addEventListener('click', closeRedirectModal);
      redirectOverlay.addEventListener('click', (event) => {
        if (event.target === redirectOverlay) closeRedirectModal();
      });
    </script>
  <?php endif; ?>

  <script src="./assets/js/trophies.js"></script>
  <script src="./assets/js/script.js"></script>

</body>

</html>
      <path d="M9 19h6" />
      <path d="M10 15h4l.6 4H9.4l.6-4Z" />
    </svg>
    <span class="trophy-badge" id="trophyBadge"><span id="pointsSpan">0</span>/8</span>
  </a>

  <div class="content">

    <h1>Szkenneld be a kódot</h1>
    <p class="intro-text">Gyűjtsd össze az elrejtett QR kódokat, és szerezd meg a jutalmad! 🏆</p>

    <button type="button" class="scan-btn" id="scanBtn" aria-label="QR kód beolvasása">
      <span class="ring"></span>
      <span class="ring"></span>
      <span class="spinner"></span>
      <svg id="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
        stroke-linejoin="round">
        <path
          d="M4 8a2 2 0 0 1 2-2h1.2a1 1 0 0 0 .89-.55l.5-1A1 1 0 0 1 9.48 4h5.04a1 1 0 0 1 .9.55l.5 1a1 1 0 0 0 .88.55H18a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z" />
        <circle cx="12" cy="13" r="3.5" />
      </svg>
    </button>

    <p id="status">Koppints az ikonra, és fényképezd le a kódot</p>
    <button type="button" id="retry">Új szkennelés</button>

    <input type="file" id="qrImage" accept="image/*" capture="environment">

    <p id="completeBanner" class="complete-banner" role="button" tabindex="-1" aria-live="polite" hidden>
      <span id="completeBannerText">🎉 Összegyűjtötted az összes trófeát! Gyere el hozzánk a jutalmadért!</span>
      <span id="redemptionProgress" class="redemption-progress" hidden></span>
    </p>

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

  <?php if ($showRedirectModal): ?>
    <div class="reward-overlay" id="redirectOverlay" hidden>
      <div class="reward-card" role="dialog" aria-modal="true" aria-labelledby="redirectTitle">
        <button type="button" class="reward-close" id="redirectClose" aria-label="Bezárás">✕</button>
        <img class="reward-img" src="./assets/img/stickers/traveler.png" alt="Mosolygó utazó">
        <h2 id="redirectTitle">Szia, QR-vadász! 👋</h2>
        <p>A matricagyűjtéshez az ezen az oldalon található olvasóval olvasd be a QR-kódokat! A trófeákat csakis így szerezheted meg! ✨</p>
        <button type="button" class="reward-ok" id="redirectOk">Értem</button>
      </div>
    </div>
    <script>
      const redirectOverlay = document.getElementById('redirectOverlay');
      const closeRedirectModal = () => {
        redirectOverlay.classList.remove('open');
        setTimeout(() => { redirectOverlay.hidden = true; }, 250);
      };

      redirectOverlay.hidden = false;
      requestAnimationFrame(() => redirectOverlay.classList.add('open'));
      document.getElementById('redirectClose').addEventListener('click', closeRedirectModal);
      document.getElementById('redirectOk').addEventListener('click', closeRedirectModal);
      redirectOverlay.addEventListener('click', (event) => {
        if (event.target === redirectOverlay) closeRedirectModal();
      });
    </script>
  <?php endif; ?>

  <script src="./assets/js/trophies.js"></script>
  <script src="./assets/js/script.js"></script>

</body>

</html>
