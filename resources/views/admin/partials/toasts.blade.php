{{--
  Floating toasts + fetch-based form submits, shared by every admin and POS page.
  - Flashed status / error / validation messages appear as toasts on top of everything, including open modals.
  - POST forms submit with fetch, so a failed submit keeps the page (and any open modal) as it is and never
    adds a browser history entry; a successful submit swaps the page in with location.replace.
  - Opt a form out with data-native. Other scripts can call window.toast(message, 'error' | 'success' | 'info').
--}}
@php
  $toastQueue = [];

  if (session('status')) {
    $toastQueue[] = ['type' => 'success', 'message' => session('status')];
  }

  if (session('error')) {
    $toastQueue[] = ['type' => 'error', 'message' => session('error')];
  }

  if (isset($errors) && $errors->any()) {
    foreach (array_slice(array_values(array_unique($errors->all())), 0, 3) as $message) {
      $toastQueue[] = ['type' => 'error', 'message' => $message];
    }
  }
@endphp
<style>
  .cbt-wrap { position: fixed; z-index: 2147483000; top: max(16px, env(safe-area-inset-top, 0px)); right: 16px; width: min(380px, calc(100vw - 32px)); display: flex; flex-direction: column; gap: 10px; pointer-events: none; font-family: 'Nunito', system-ui, sans-serif; }
  .cbt { pointer-events: auto; position: relative; overflow: hidden; display: flex; align-items: flex-start; gap: 10px; padding: 12px 12px 14px 14px; border-radius: 18px; border: 1.5px solid; background: #fff; box-shadow: 0 16px 32px -10px rgba(26, 59, 82, .28), 0 4px 10px rgba(26, 59, 82, .1); font-size: 14px; font-weight: 700; line-height: 1.35; animation: cbt-in .22s ease-out both; }
  .cbt.cbt-out { animation: cbt-out .18s ease-in both; }
  .cbt.cbt-pulse { animation: cbt-pulse .3s ease-out; }
  .cbt-success { background: #EAF5EF; color: #2F5F46; border-color: #6FAE8B; }
  .cbt-error { background: #FBEDEB; color: #9C4A41; border-color: #E0776B; }
  .cbt-info { background: #EAF3FA; color: #24506F; border-color: #8FBBDD; }
  .cbt-icon { flex: none; width: 20px; height: 20px; margin-top: 1px; }
  .cbt-msg { flex: 1; min-width: 0; overflow-wrap: anywhere; }
  .cbt-close { flex: none; width: 22px; height: 22px; display: grid; place-items: center; border: 0; border-radius: 8px; background: transparent; color: inherit; opacity: .55; cursor: pointer; }
  .cbt-close:hover { opacity: 1; background: rgba(0, 0, 0, .06); }
  .cbt-bar { position: absolute; left: 0; bottom: 0; height: 3px; width: 100%; background: currentColor; opacity: .35; transform-origin: left; animation: cbt-life linear both; }
  .cbt:hover .cbt-bar { animation-play-state: paused; }
  .cbt-invalid { outline: 2px solid #E0776B !important; outline-offset: 2px; }
  @keyframes cbt-in { from { opacity: 0; transform: translateY(-10px) scale(.97); } to { opacity: 1; transform: none; } }
  @keyframes cbt-out { to { opacity: 0; transform: translateY(-8px) scale(.97); } }
  @keyframes cbt-pulse { 50% { transform: scale(1.03); } }
  @keyframes cbt-life { from { transform: scaleX(1); } to { transform: scaleX(0); } }
  @media (max-width: 639px) { .cbt-wrap { right: 12px; left: 12px; width: auto; } }
  @media (prefers-reduced-motion: reduce) { .cbt, .cbt.cbt-out, .cbt.cbt-pulse { animation-duration: .01s; } }
</style>
<div id="cbToasts" class="cbt-wrap" role="region" aria-label="Notifications" aria-live="polite"></div>
<script>
  (function () {
    var wrap = document.getElementById('cbToasts');
    var ICONS = {
      success: '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>',
      error: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.01M10.3 4.2L2.7 17.4A2 2 0 004.4 20.4h15.2a2 2 0 001.7-3L13.7 4.2a2 2 0 00-3.4 0z"/>',
      info: '<path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    };
    var DURATION = { success: 4500, info: 5000, error: 7000 };

    function dismiss(el) {
      if (!el || el.dataset.gone) { return; }
      el.dataset.gone = '1';
      clearTimeout(el._timer);
      el.classList.add('cbt-out');
      setTimeout(function () { el.remove(); }, 200);
    }

    function arm(el) {
      clearTimeout(el._timer);
      var bar = el.querySelector('.cbt-bar');
      var ms = DURATION[el.dataset.type] || 5000;
      el._left = ms;
      el._started = Date.now();
      bar.style.animationDuration = ms + 'ms';
      el._timer = setTimeout(function () { dismiss(el); }, ms);
    }

    window.toast = function (message, type) {
      type = ICONS[type] ? type : 'info';
      message = String(message == null ? '' : message).trim();
      if (!message) { return; }

      var existing = Array.prototype.find.call(wrap.children, function (el) {
        return !el.dataset.gone && el.dataset.type === type && el.querySelector('.cbt-msg').textContent === message;
      });
      if (existing) {
        existing.classList.remove('cbt-pulse');
        void existing.offsetWidth;
        existing.classList.add('cbt-pulse');
        var oldBar = existing.querySelector('.cbt-bar');
        var fresh = oldBar.cloneNode();
        oldBar.replaceWith(fresh);
        arm(existing);
        return existing;
      }

      var el = document.createElement('div');
      el.className = 'cbt cbt-' + type;
      el.dataset.type = type;
      el.setAttribute('role', type === 'error' ? 'alert' : 'status');
      el.innerHTML =
        '<svg class="cbt-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4" aria-hidden="true">' + ICONS[type] + '</svg>' +
        '<div class="cbt-msg"></div>' +
        '<button type="button" class="cbt-close" aria-label="Dismiss"><svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>' +
        '<span class="cbt-bar"></span>';
      el.querySelector('.cbt-msg').textContent = message;
      el.querySelector('.cbt-close').addEventListener('click', function () { dismiss(el); });
      el.addEventListener('mouseenter', function () {
        clearTimeout(el._timer);
        el._left = Math.max(1200, el._left - (Date.now() - el._started));
      });
      el.addEventListener('mouseleave', function () {
        el._started = Date.now();
        el._timer = setTimeout(function () { dismiss(el); }, el._left);
      });
      wrap.appendChild(el);
      arm(el);

      var live = Array.prototype.filter.call(wrap.children, function (n) { return !n.dataset.gone; });
      while (live.length > 4) { dismiss(live.shift()); }
      return el;
    };
    window.showToast = window.toast;

    var flashed = @json($toastQueue);
    flashed.forEach(function (t) { window.toast(t.message, t.type); });

    /* ---- fetch-based form submits ---- */
    function fieldFor(form, key) {
      var parts = key.split('.');
      var name = parts[0] + parts.slice(1).map(function (p) { return '[' + p + ']'; }).join('');
      return form.querySelector('[name="' + name + '"]') || form.querySelector('[name="' + name + '[]"]');
    }

    function markInvalid(form, errors) {
      var first = null;
      Object.keys(errors || {}).forEach(function (key) {
        var field = fieldFor(form, key);
        if (!field || field.type === 'hidden') { return; }
        var box = field.closest('.shadow-soft-inset') || field;
        box.classList.add('cbt-invalid');
        var clear = function () { box.classList.remove('cbt-invalid'); };
        field.addEventListener('input', clear, { once: true });
        field.addEventListener('change', clear, { once: true });
        first = first || field;
      });
      if (first) { try { first.focus(); } catch (e) {} }
    }

    function clearInvalid(form) {
      form.querySelectorAll('.cbt-invalid').forEach(function (el) { el.classList.remove('cbt-invalid'); });
    }

    async function send(form, submitter) {
      var body;
      try { body = submitter ? new FormData(form, submitter) : new FormData(form); } catch (e) { body = new FormData(form); }

      form.dataset.busy = '1';
      form.setAttribute('aria-busy', 'true');
      var locked = Array.prototype.filter.call(form.querySelectorAll('button, input[type=submit]'), function (b) {
        return !b.disabled && (b.type === 'submit' || !b.getAttribute('type'));
      });
      locked.forEach(function (b) { b.disabled = true; });
      clearInvalid(form);

      var leaving = false;
      try {
        var res = await fetch(form.action, {
          method: 'POST',
          body: body,
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        var data = null;
        try { data = await res.json(); } catch (e) {}

        if (res.ok && data && data.redirect) {
          leaving = true;
          location.replace(data.redirect);
          return;
        }
        if (res.ok) {
          window.toast((data && data.message) || 'Saved.', 'success');
          return;
        }
        if (res.status === 422 && data) {
          var seen = {};
          var messages = [];
          Object.keys(data.errors || {}).forEach(function (key) {
            data.errors[key].forEach(function (m) { if (!seen[m]) { seen[m] = 1; messages.push(m); } });
          });
          if (!messages.length && data.message) { messages.push(data.message); }
          messages.slice(0, 3).forEach(function (m) { window.toast(m, 'error'); });
          markInvalid(form, data.errors);
          form.dispatchEvent(new CustomEvent('form:invalid', { bubbles: true, detail: data }));
          return;
        }
        if (res.status === 401 || res.status === 419) {
          leaving = true;
          window.toast('Your session expired. Reloading…', 'error');
          setTimeout(function () { location.reload(); }, 1400);
          return;
        }
        window.toast((data && data.message) || 'Something went wrong. Please try again.', 'error');
      } catch (e) {
        window.toast('Network error. Check your connection and try again.', 'error');
      } finally {
        if (!leaving) {
          locked.forEach(function (b) { b.disabled = false; });
          form.removeAttribute('aria-busy');
          delete form.dataset.busy;
        }
      }
    }

    document.addEventListener('submit', function (e) {
      var form = e.target;
      if (e.defaultPrevented || !(form instanceof HTMLFormElement) || !window.fetch) { return; }
      if ((form.getAttribute('method') || 'get').toLowerCase() !== 'post') { return; }
      if (form.hasAttribute('data-native') || (form.target && form.target !== '_self')) { return; }
      e.preventDefault();
      if (form.dataset.busy) { return; }
      send(form, e.submitter);
    });
  })();
</script>
