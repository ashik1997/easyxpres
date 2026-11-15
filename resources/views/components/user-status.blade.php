@props(['status'])

@php
    $normalizedStatus = strtolower((string) $status);
    $label = match ($normalizedStatus) {
        'approved', 'successful' => 'Success',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
        default => ucfirst($normalizedStatus ?: 'Unknown'),
    };
@endphp

<span {{ $attributes->class(['ux-status', 'ux-status--' . $normalizedStatus]) }}>{{ $label }}</span>
