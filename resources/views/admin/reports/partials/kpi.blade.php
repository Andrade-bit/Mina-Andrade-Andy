{{--
  One clickable KPI card. Pass: href, label, value, sub, and optionally tone (default | primary | danger),
  valueClass and class. The whole card is the link.
--}}
@php
  $tone = $tone ?? 'default';
  $isFilled = in_array($tone, ['primary', 'danger'], true);
  $surface = match ($tone) {
    'primary' => 'bg-stamp-500 text-cream-50',
    'danger' => 'bg-coral-600 text-cream-50',
    default => 'bg-white',
  };
@endphp
<a href="{{ $href }}" class="block rounded-3xl shadow-soft p-5 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300 {{ $surface }} {{ $class ?? '' }}">
  <p class="text-[11px] font-extrabold uppercase tracking-wide {{ $isFilled ? 'text-cream-50/70' : 'text-stamp-300' }}">{{ $label }}</p>
  <p class="font-display font-bold text-xl md:text-2xl mt-1 {{ $isFilled ? '' : ($valueClass ?? 'text-stamp-700') }}">{{ $value }}</p>
  <p class="text-xs font-semibold {{ $isFilled ? 'text-cream-50/80' : 'text-stamp-300' }}">{{ $sub }}</p>
</a>
