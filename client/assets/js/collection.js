const sub   = document.getElementById('collectionSub');
const slots = Array.from(document.querySelectorAll('.trophy-slot'));

async function loadCollection() {
  try {
    const res = await fetch('../server/collection.php');
    const data = await res.json();
    if (!data.success) throw new Error('bad response');

    slots.forEach(slot => {
      const got = !!data.collected[slot.dataset.trophy];
      slot.classList.toggle('collected', got);
    });

    sub.textContent = `${data.points} / ${data.total} trófea megszerezve`;
  } catch (err) {
    sub.textContent = 'Nem sikerült betölteni a gyűjteményt.';
  }
}

// showRewardModal() comes from trophies.js, included before this file.
slots.forEach(slot => {
  slot.addEventListener('click', () => {
    if (!slot.classList.contains('collected')) return;
    showRewardModal(slot.dataset.trophy);
  });
});

loadCollection();
refreshCompletionState();
