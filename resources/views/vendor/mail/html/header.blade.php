<tr>
  <td class="header">
    <a href="{{ $url }}" style="display: inline-block;">
      @if (trim($slot) === 'Laravel')
        <img src="{{ asset('logo.png') }}" class="logo" alt="Wall Street">
      @else
        {{ $slot }}
      @endif
    </a>
  </td>
</tr>
