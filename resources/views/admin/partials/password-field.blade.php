{{--
  Masked password / PIN box with a show/hide eye button.
  Variables: name; optional id, value, placeholder, required, autocomplete (default new-password, so browsers
  do not fill the saved admin password into a staff PIN); pin = true for a 4-digit PIN (numeric keypad).
  The eye button is wired by the [data-eye] handler in the page script.
--}}
<div class="bg-cream-100 rounded-2xl shadow-soft-inset flex items-center gap-2 px-3.5 py-2.5">
  <svg class="w-4 h-4 text-stamp-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
  <input type="password" name="{{ $name }}" @isset($id) id="{{ $id }}" @endisset @isset($value) value="{{ $value }}" @endisset
         @if ($pin ?? false) inputmode="numeric" pattern="[0-9]{4}" maxlength="4" @endif
         placeholder="{{ $placeholder ?? '' }}" autocomplete="{{ $autocomplete ?? 'new-password' }}" @required($required ?? false)
         class="w-full min-w-0 bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
  <button type="button" data-eye aria-label="Show password" aria-pressed="false" class="shrink-0 text-stamp-300 hover:text-stamp-600 transition-colors">
    <svg class="eye-show w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
    <svg class="eye-hide w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
  </button>
</div>
