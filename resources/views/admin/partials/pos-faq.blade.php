{{--
  Help & FAQ pop-up for the POS (staff and assistants, including first-timers on the PIN screen).
  Self-contained: it styles itself, so it works on the plain-CSS login page and the Tailwind terminal alike.
  It brings its own floating Help button (bottom right); any other button can still call openFaq().
--}}
@php
  $faqGroups = [
    'Getting started' => [
      ['How do I unlock the POS?', 'Type your own 4-digit PIN on the PIN screen. It unlocks by itself after the fourth digit, or tap Unlock POS. Your PIN tells the system who processed each sale, so keep it private.'],
      ['It says my PIN is incorrect, or I forgot it.', 'Tap Clear and try again. If it still fails, ask an admin: they can see and change your PIN in User Management.'],
      ['How do I switch users at the end of a shift?', 'Tap the red Switch user button at the top right of the POS. The next person then enters their own PIN.'],
    ],
    'Taking an order' => [
      ['How do I add a drink?', 'Tap the drink. If it comes in sizes, pick Small, Medium or Large and it is added to the order. One-size drinks are added straight away. Use the category tabs (Hot Coffee, Iced Coffee and so on) to find a drink faster.'],
      ['Where is the current order?', 'On a phone or tablet, tap the Current Order bar at the bottom of the screen. On a computer, click the Current Order tab on the right edge to slide it out.'],
      ['How do I change a quantity?', 'Use the − and + buttons, or tap the number and type it, then press Enter. You can order up to 99 of one item. Setting it to 0 removes the item.'],
      ['How do I remove an item or start over?', 'Set the item to 0, or press − until it disappears. Clear empties the whole order and asks you to confirm first.'],
    ],
    'Payment' => [
      ['How do I take a cash payment?', 'Choose Cash and type the cash received. The change shows underneath. Charge turns on once the cash covers the total.'],
      ['What about GCash or Card?', 'Choose GCash or Card, then tap Charge. There is no amount to type.'],
      ['How do I use a promo code?', 'Type the code in the Promo code box and tap Apply. The discount appears in the totals. An invalid or expired code shows an error, so ask an admin which promos are running.'],
      ['What happens after I charge?', 'A receipt appears. You can print it, then close it to start the next order. The sale is only recorded once the receipt shows.'],
    ],
    'When something goes wrong' => [
      ['I charged the wrong order. Can I cancel it?', 'Staff cannot cancel a sale. Ask an admin: they void it from Transactions with a reason and their own PIN. Voided sales are not counted in the sales totals.'],
      ['It says there was a network error.', 'The sale was not recorded. Check the internet connection and press Charge again. If a receipt did appear, do not charge it twice.'],
      ['It says my session expired.', 'You are sent back to the PIN screen. Enter your PIN and add the order again.'],
      ['A drink is missing from the menu.', 'An admin may have archived it. Ask them to check Products.'],
      ['Who can see the sales history?', 'Only admins. Assistants can sell, but the Transactions log is for admins.'],
    ],
  ];
@endphp
<style>
  .faq-fab { position: fixed; z-index: 35; right: 16px; bottom: calc(16px + env(safe-area-inset-bottom, 0px)); display: inline-flex; align-items: center; gap: 8px; height: 52px; padding: 0 20px 0 16px; border: 0; border-radius: 999px; background: linear-gradient(#2F6690, #24506F); color: #FFFDF9; font-family: 'Baloo 2', system-ui, sans-serif; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 5px 0 #1A3B52, 0 12px 22px rgba(47, 102, 144, .35); transition: right .3s ease, bottom .3s ease, transform .15s ease, box-shadow .15s ease; }
  .faq-fab:hover { transform: translateY(-2px); }
  .faq-fab:active { transform: translateY(4px); box-shadow: 0 1px 0 #1A3B52; }
  .faq-fab:focus-visible { outline: 3px solid #8FBBDD; outline-offset: 3px; }
  @media print { .faq-fab { display: none; } }
  .faq-backdrop { position: fixed; inset: 0; z-index: 2147482000; display: none; align-items: center; justify-content: center; padding: 16px; background: rgba(26, 59, 82, .45); backdrop-filter: blur(3px); font-family: 'Nunito', system-ui, sans-serif; }
  .faq-backdrop.open { display: flex; }
  .faq-panel { width: 100%; max-width: 640px; max-height: min(86vh, 760px); display: flex; flex-direction: column; background: #FFFDF9; color: #1A3B52; border-radius: 28px; box-shadow: 0 24px 60px -12px rgba(26, 59, 82, .45); overflow: hidden; }
  .faq-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 22px 24px 12px; }
  .faq-title { margin: 0; font-family: 'Baloo 2', system-ui, sans-serif; font-weight: 700; font-size: 1.5rem; line-height: 1.1; }
  .faq-sub { margin: 4px 0 0; font-size: .85rem; font-weight: 700; color: #5F7F96; }
  .faq-close { flex: none; width: 40px; height: 40px; display: grid; place-items: center; border: 0; border-radius: 14px; background: #FBF3E4; color: #2F6690; cursor: pointer; }
  .faq-close:hover { background: #F1E6CC; }
  .faq-search { margin: 0 24px 8px; display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 16px; background: #FBF3E4; box-shadow: inset 0 2px 6px rgba(26, 59, 82, .12); }
  .faq-search input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; font: inherit; font-weight: 700; font-size: .9rem; color: #1A3B52; }
  .faq-search input::placeholder { color: #8FBBDD; }
  .faq-body { overflow-y: auto; padding: 4px 24px 24px; }
  .faq-group { margin-top: 18px; }
  .faq-group h3 { margin: 0 0 8px; font-size: .7rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: #8FBBDD; }
  .faq-item { border-radius: 16px; background: #fff; box-shadow: 0 4px 12px -4px rgba(26, 59, 82, .14); margin-bottom: 8px; }
  .faq-item summary { list-style: none; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 16px; font-weight: 800; font-size: .95rem; border-radius: 16px; }
  .faq-item summary::-webkit-details-marker { display: none; }
  .faq-item summary:focus-visible { outline: 3px solid #8FBBDD; }
  .faq-item summary svg { flex: none; transition: transform .2s; color: #2F6690; }
  .faq-item[open] summary svg { transform: rotate(180deg); }
  .faq-item p { margin: 0; padding: 0 16px 16px; font-size: .9rem; font-weight: 600; line-height: 1.55; color: #3D5F78; }
  .faq-empty { display: none; padding: 28px 8px; text-align: center; font-weight: 700; color: #5F7F96; }
  @media (max-width: 480px) { .faq-head { padding: 18px 18px 10px; } .faq-search { margin: 0 18px 8px; } .faq-body { padding: 4px 18px 20px; } }
</style>
<button type="button" id="faqFab" class="faq-fab" onclick="openFaq()" aria-label="Help and FAQ" aria-haspopup="dialog">
  <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 2.5-3 4.5M12 17.5h.01"/></svg>
  FAQ
</button>
<div id="faqModal" class="faq-backdrop" role="dialog" aria-modal="true" aria-labelledby="faqTitle" onclick="if (event.target === this) closeFaq()">
  <div class="faq-panel">
    <div class="faq-head">
      <div>
        <h2 id="faqTitle" class="faq-title">Help &amp; FAQ</h2>
        <p class="faq-sub">Quick answers for new staff. Tap a question to open it.</p>
      </div>
      <button type="button" class="faq-close" onclick="closeFaq()" aria-label="Close help">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <label class="faq-search">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#2F6690" stroke-width="2.4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input id="faqSearch" type="search" placeholder="Search the FAQ…" autocomplete="off" aria-label="Search the FAQ">
    </label>
    <div class="faq-body">
      @foreach ($faqGroups as $groupTitle => $items)
        <section class="faq-group">
          <h3>{{ $groupTitle }}</h3>
          @foreach ($items as [$question, $answer])
            <details class="faq-item" data-text="{{ strtolower($question.' '.$answer) }}">
              <summary>
                <span>{{ $question }}</span>
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
              </summary>
              <p>{{ $answer }}</p>
            </details>
          @endforeach
        </section>
      @endforeach
      <p id="faqEmpty" class="faq-empty">No answers match that. Ask an admin for help.</p>
    </div>
  </div>
</div>
<script>
  (function () {
    var modal = document.getElementById('faqModal');
    var search = document.getElementById('faqSearch');
    var opener = null;

    window.openFaq = function () {
      opener = document.activeElement;
      modal.classList.add('open');
      search.value = '';
      filter();
      document.body.style.overflow = 'hidden';
      modal.querySelector('.faq-close').focus();
    };

    window.closeFaq = function () {
      modal.classList.remove('open');
      document.body.style.overflow = '';
      if (opener && opener.focus) { opener.focus(); }
    };

    function filter() {
      var query = search.value.trim().toLowerCase();
      var shown = 0;
      modal.querySelectorAll('.faq-item').forEach(function (item) {
        var match = !query || item.dataset.text.indexOf(query) !== -1;
        item.style.display = match ? '' : 'none';
        if (query && match) { item.open = true; }
        if (!query) { item.open = false; }
        if (match) { shown++; }
      });
      modal.querySelectorAll('.faq-group').forEach(function (group) {
        var any = Array.prototype.some.call(group.querySelectorAll('.faq-item'), function (item) { return item.style.display !== 'none'; });
        group.style.display = any ? '' : 'none';
      });
      document.getElementById('faqEmpty').style.display = shown ? 'none' : 'block';
    }

    search.addEventListener('input', filter);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.classList.contains('open')) { closeFaq(); }
    });
  })();
</script>
