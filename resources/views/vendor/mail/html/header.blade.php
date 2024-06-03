<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Kampanye INSIDE')
                <img src="{{ asset('public/assets/logo/logo_apk.png') }}" class="logo" alt="Logo"
                    style="height: 200px">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
