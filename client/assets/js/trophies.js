/**
 * trophies.js
 * Shared between index.php (scan page) and collection.php (collection page):
 * sticker artwork, dummy descriptions, and the reward modal that shows a
 * trophy's sticker + description. Both pages must include a
 * #rewardOverlay / #rewardImg / #rewardTitle / #rewardDesc / #rewardClose /
 * #rewardOk block in their markup for this to work.
 */

// Sticker artwork per trophy — swap filenames here if the mapping changes.
const TROPHY_STICKERS = {
  qr_1: './assets/img/stickers/audit.png',
  qr_2: './assets/img/stickers/birthday-cake.png',
  qr_3: './assets/img/stickers/chef.png',
  qr_4: './assets/img/stickers/circular-saw.png',
  qr_5: './assets/img/stickers/laptop.png',
  qr_6: './assets/img/stickers/robot-arm.png',
  qr_7: './assets/img/stickers/traveler.png',
  qr_8: './assets/img/stickers/waiter.png',
};

// Each trophy corresponds to a profession taught at our school. Dummy
// placeholder descriptions for now — swap in the real copy later.
const TROPHY_INFO = {
  qr_1: {
    title: 'Pénzügyi-számviteli ügyintéző',
    desc: 'Iskolánkban megtanulhatsz pénzügyi-számviteli ügyintézővé válni, aki cégek könyvelését vezeti, bizonylatokat dolgoz fel, és pontos pénzügyi nyilvántartást készít.',
  },
  qr_2: {
    title: 'Cukrász',
    desc: 'Iskolánkban megtanulhatsz cukrásszá válni, aki tortákat, süteményeket és desszerteket készít, és a vendégeket finom édességekkel örvendezteti meg.',
  },
  qr_3: {
    title: 'Szakács',
    desc: 'Iskolánkban megtanulhatsz szakáccsá válni, aki ízletes ételeket készít, és egy konyha teljes működését irányítja.',
  },
  qr_4: {
    title: 'CNC-programozó',
    desc: 'Iskolánkban megtanulhatsz CNC-programozóvá válni, aki számítógépes vezérlésű gépeket programoz, és precíz alkatrészeket gyárt.',
  },
  qr_5: {
    title: 'Szoftverfejlesztő és -tesztelő',
    desc: 'Iskolánkban megtanulhatsz szoftverfejlesztővé és -tesztelővé válni, aki programokat ír, hibákat keres, és modern alkalmazásokat épít.',
  },
  qr_6: {
    title: 'Mechatronikai technikus',
    desc: 'Iskolánkban megtanulhatsz mechatronikai technikussá válni, aki gépészeti, elektronikai és informatikai tudást ötvözve automatizált rendszereket tervez és üzemeltet.',
  },
  qr_7: {
    title: 'Turisztikai technikus',
    desc: 'Iskolánkban megtanulhatsz turisztikai technikussá válni, aki utazásokat szervez, programokat állít össze, és vendégeket kalauzol.',
  },
  qr_8: {
    title: 'Pincér - vendégtéri szakember',
    desc: 'Iskolánkban megtanulhatsz vendégtéri szakemberré válni, aki éttermekben és rendezvényeken gondoskodik a vendégek kényelméről és kiszolgálásáról.',
  },
};

const rewardOverlay = document.getElementById('rewardOverlay');
const rewardImg = document.getElementById('rewardImg');
const rewardTitle = document.getElementById('rewardTitle');
const rewardDesc = document.getElementById('rewardDesc');
const rewardClose = document.getElementById('rewardClose');
const rewardOk = document.getElementById('rewardOk');

function showRewardModal(trophyKey) {
  const info = TROPHY_INFO[trophyKey] || { title: 'Trófea', desc: 'Gratulálunk, ez a trófea a gyűjteményedben van!' };
  rewardImg.src = TROPHY_STICKERS[trophyKey] || './assets/img/logo-watermark.png';
  rewardImg.alt = info.title;
  rewardTitle.textContent = info.title;
  rewardDesc.textContent = info.desc;

  rewardOverlay.hidden = false;
  requestAnimationFrame(() => rewardOverlay.classList.add('open'));
}

function closeRewardModal() {
  rewardOverlay.classList.remove('open');
  setTimeout(() => {
    rewardOverlay.hidden = true;
    rewardOverlay.dispatchEvent(new CustomEvent('rewardclosed'));
  }, 250);
}

rewardClose.addEventListener('click', closeRewardModal);
rewardOk.addEventListener('click', closeRewardModal);
rewardOverlay.addEventListener('click', (e) => {
  if (e.target === rewardOverlay) closeRewardModal();
});

/* ---------------- "All trophies collected" modal + banner ---------------- */

const completeOverlay = document.getElementById('completeOverlay');
const completeDesc = document.getElementById('completeDesc');
const completeClose = document.getElementById('completeClose');
const completeOk = document.getElementById('completeOk');
const completeBanner = document.getElementById('completeBanner');

function showCompleteModal(total) {
  if (!completeOverlay) return;
  if (completeDesc) {
    completeDesc.textContent =
      `Megszerezted mind a ${total} trófeát! Gyere oda hozzánk a standunkhoz, és vedd át a jutalmad!`;
  }
  completeOverlay.hidden = false;
  requestAnimationFrame(() => completeOverlay.classList.add('open'));
}

function closeCompleteModal() {
  if (!completeOverlay) return;
  completeOverlay.classList.remove('open');
  setTimeout(() => { completeOverlay.hidden = true; }, 250);
}

if (completeClose) completeClose.addEventListener('click', closeCompleteModal);
if (completeOk) completeOk.addEventListener('click', closeCompleteModal);
if (completeOverlay) {
  completeOverlay.addEventListener('click', (e) => {
    if (e.target === completeOverlay) closeCompleteModal();
  });
}

// Reads the additive server/collection.php endpoint (doesn't touch
// check.php/getpoints.php) to see whether every trophy is collected.
async function checkCollectionComplete() {
  try {
    const res = await fetch('../server/collection.php');
    const data = await res.json();
    if (!data.success) return null;
    return { points: data.points, total: data.total, complete: data.points >= data.total };
  } catch (err) {
    return null;
  }
}

// Shows the persistent "you're done" banner whenever all trophies are in,
// and pops the modal once per browser (via localStorage) the first time
// completion is detected.
async function refreshCompletionState({ allowModal = false } = {}) {
  const status = await checkCollectionComplete();
  if (!status || !status.complete) return status;

  if (completeBanner) completeBanner.hidden = false;

  if (allowModal && !localStorage.getItem('allTrophiesCompleteShown')) {
    localStorage.setItem('allTrophiesCompleteShown', '1');
    showCompleteModal(status.total);
  }
  return status;
}
