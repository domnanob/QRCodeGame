const btn = document.getElementById('scanBtn');
const icon = document.getElementById('icon');
const input = document.getElementById('qrImage');
const status = document.getElementById('status');
const retry = document.getElementById('retry');
const trophyBtn = document.getElementById('trophyBtn');

const ICON_CAMERA = icon.innerHTML;
const ICON_SUCCESS = '<path d="M5 13l4 4L19 7"/>';
const ICON_ERROR = '<path d="M6 6l12 12M18 6L6 18"/>';

const IDLE_HINT = 'Koppints az ikonra, és fényképezd le a kódot';

// sha256("qr_N") for each trophy, precomputed, so the client can tell
// which trophy a scanned code belongs to without needing crypto.subtle
// (which isn't available on plain-http LAN addresses on some phones).
const TROPHY_HASHES = {
  qr_1: '12716218d0b2862b72e1e8e41f2ac6726f45ff5f7fd081424266346482c9b013',
  qr_2: 'ab09a8c1a56ccc324a5475bb7989a77b22dec011b65e86d3d5ad1277724a4901',
  qr_3: 'a99f952cdd610b68f42189ce4abbd5a2bec03e0859cb3fce771a34bb1498aacc',
  qr_4: 'f04a032fb1da28d748974506b1e2077e0743722aaa8ae4e0a01e9266d18536e9',
  qr_5: '64fffd6718a85875e5647326bbc5d95e384d13bdb3450e67670e7cde4003a1de',
  qr_6: 'ba0b483a31625b0df902d1d6c0c9163738c9082c9910511c1c0572afa2ca7e07',
  qr_7: '9ca740f5f7155817d4d9f0f3a29e84fa53ee82aeebd41e6289a2d0b6555c5248',
  qr_8: '40e11eeb55836f45877356f83b2962c880ece80ff4e6b5dd95c1a591dfe50780',
};

// TROPHY_STICKERS, TROPHY_INFO and showRewardModal()/closeRewardModal()
// come from trophies.js, which must be included before this file.

function trophyKeyFromCode(code) {
  return Object.keys(TROPHY_HASHES).find(key => TROPHY_HASHES[key] === code) || null;
}

async function getPoints() {
  const response = await fetch('../server/getpoints.php', { method: 'GET' });
  const data = await response.json();
  document.getElementById("pointsSpan").innerHTML = data.points;
}
getPoints();

// Shows the "you're done" banner on load if already complete (doesn't pop
// the modal here — that's reserved for the moment a scan completes the set).
refreshCompletionState();

function setState(state, message) {
  btn.classList.remove('scanning', 'success', 'error');
  if (state) btn.classList.add(state);

  icon.innerHTML =
    state === 'success' ? ICON_SUCCESS :
      state === 'error' ? ICON_ERROR :
        ICON_CAMERA;

  status.textContent = message;
  retry.style.display = (state === 'success' || state === 'error') ? 'inline-block' : 'none';
  btn.style.pointerEvents = state === 'scanning' ? 'none' : 'auto';
}

btn.addEventListener('click', () => {
  if (btn.classList.contains('scanning')) return;
  input.value = '';
  input.click();
});

retry.addEventListener('click', () => setState(null, IDLE_HINT));

input.addEventListener('change', async () => {
  if (!input.files[0]) return;

  setState('scanning', 'Beolvasás…');

  const formData = new FormData();
  formData.append('qrImage', input.files[0]);

  try {
    const response = await fetch('../server/decode.php', { method: 'POST', body: formData });
    const data = await response.json();

    if (!data.success) {
      setState('error', data.error);
      return;
    }

    const code = data.text.split("?")[1];
    const response2 = await fetch(data.text.split("?")[0], {
      method: 'POST',
      body: JSON.stringify({ code }),
    });
    const data2 = await response2.json();

    if (data2.success) {
      setState('success', 'Trófea megszerezve!');
      const trophyKey = trophyKeyFromCode(code);
      flySticker(trophyKey);
      getPoints();

      // Once the reward modal for this trophy is closed, check whether the
      // whole set is now complete — if so, show the completion modal too.
      rewardOverlay.addEventListener('rewardclosed', () => {
        refreshCompletionState({ allowModal: true });
      }, { once: true });
    } else {
      setState('error', 'Ez a kód nem érvényes.');
    }
  } catch (err) {
    setState('error', 'A kérés nem sikerült: ' + err.message);
  }
});

/* ---------------- Sticker pop + fly to the trophy badge ---------------- */

function flySticker(trophyKey) {
  const startRect = btn.getBoundingClientRect();
  const endRect = trophyBtn.getBoundingClientRect();

  const sticker = document.createElement('img');
  sticker.src = TROPHY_STICKERS[trophyKey] || './assets/img/logo-watermark.png';
  sticker.className = 'flying-sticker';
  sticker.alt = '';

  const size = 96;
  const startX = startRect.left + startRect.width / 2 - size / 2;
  const startY = startRect.top + startRect.height / 2 - size / 2;
  sticker.style.width = size + 'px';
  sticker.style.height = size + 'px';
  sticker.style.left = startX + 'px';
  sticker.style.top = startY + 'px';

  document.body.appendChild(sticker);

  const dx = (endRect.left + endRect.width / 2) - (startX + size / 2);
  const dy = (endRect.top + endRect.height / 2) - (startY + size / 2);

  const anim = sticker.animate([
    { transform: 'translate(0px, 0px) scale(0) rotate(0deg)', opacity: 0, offset: 0 },
    { transform: 'translate(0px, -18px) scale(1.3) rotate(-8deg)', opacity: 1, offset: 0.22 },
    { transform: 'translate(0px, 0px) scale(1) rotate(0deg)', opacity: 1, offset: 0.4 },
    { transform: 'translate(0px, 0px) scale(1) rotate(0deg)', opacity: 1, offset: 0.55 },
    { transform: `translate(${dx}px, ${dy}px) scale(0.3) rotate(18deg)`, opacity: 0.9, offset: 0.92 },
    { transform: `translate(${dx}px, ${dy}px) scale(0.1) rotate(18deg)`, opacity: 0, offset: 1 },
  ], {
    duration: 1500,
    easing: 'cubic-bezier(.22,.9,.3,1)',
  });

  anim.onfinish = () => {
    sticker.remove();
    trophyBtn.classList.add('bump');
    setTimeout(() => trophyBtn.classList.remove('bump'), 400);
    showRewardModal(trophyKey);
  };
}
