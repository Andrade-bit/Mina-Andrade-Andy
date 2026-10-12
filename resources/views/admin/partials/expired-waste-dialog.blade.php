<style>
  #expiredWasteDialog { width: calc(100% - 2rem); max-width: 420px !important; max-height: 90dvh; border: 0; padding: 24px; }
  #expiredWasteDialog::backdrop { background: rgba(26,59,82,.45); backdrop-filter: blur(3px); }
</style>
<dialog id="expiredWasteDialog" aria-labelledby="expiredWasteTitle" aria-describedby="expiredWasteDescription" class="bg-cream-50 rounded-[2rem] shadow-soft text-stamp-700">
  <h2 id="expiredWasteTitle" class="font-display font-bold text-xl">Record expired stock as waste?</h2>
  <p id="expiredWasteDescription" class="text-sm text-stamp-500 mt-2">Confirm only after removing this expired batch from physical stock.</p>
  <div class="rounded-2xl bg-coral-50 p-4 mt-4">
    <p id="expiredWasteName" class="font-bold text-stamp-700 break-words"></p>
    <p id="expiredWasteBatch" class="text-xs font-semibold text-coral-600 mt-1"></p>
    <p id="expiredWasteQuantity" class="text-sm font-bold text-coral-600 mt-2"></p>
  </div>
  <p class="text-xs text-stamp-500 mt-3">The entire remaining quantity will be deducted from inventory and logged as waste.</p>
  <div class="flex gap-3 mt-5">
    <button type="button" autofocus onclick="document.getElementById('expiredWasteDialog').close()" class="flex-1 rounded-2xl bg-cream-100 text-stamp-600 py-3 font-bold hover:bg-cream-200">Cancel</button>
    <button type="button" onclick="submitExpiredWaste()" class="flex-1 rounded-2xl bg-coral-600 text-white py-3 font-bold hover:bg-coral-500">Confirm Waste</button>
  </div>
</dialog>
<script>
  (() => {
    const dialog = document.getElementById('expiredWasteDialog');
    let pendingForm = null;
    window.reviewExpiredWaste = function(event, form) {
      if (form.dataset.wasteConfirmed === '1') {
        delete form.dataset.wasteConfirmed;
        return true;
      }
      event.preventDefault();
      if (form.dataset.busy) return false;
      pendingForm = form;
      document.getElementById('expiredWasteName').textContent = form.dataset.wasteName;
      document.getElementById('expiredWasteBatch').textContent = 'Batch #' + form.dataset.wasteBatch;
      document.getElementById('expiredWasteQuantity').textContent = 'Quantity: ' + form.dataset.wasteQuantity;
      dialog.showModal();
      return false;
    };
    window.submitExpiredWaste = function() {
      const form = pendingForm;
      if (!form || form.dataset.busy) return;
      pendingForm = null;
      dialog.close();
      form.dataset.wasteConfirmed = '1';
      form.requestSubmit();
    };
    dialog.addEventListener('close', () => { pendingForm = null; });
    dialog.addEventListener('click', event => {
      const bounds = dialog.getBoundingClientRect();
      if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) dialog.close();
    });
  })();
</script>
