@props(['slots', 'position'])

@if (isset($slots[$position]) && $slots[$position]->isNotEmpty())
  @foreach ($slots[$position] as $slot)
    @if ($slot->is_active && !empty($slot->ad_code))
      <div class="ad-slot-wrapper ad-slot-{{ $position }}">
        {!! $slot->ad_code !!}
      </div>
    @endif
  @endforeach
@endif

