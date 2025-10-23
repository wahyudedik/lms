<x-mail::message>
    # {{ ucfirst($reportType) }} Analytics Report

    Period: **{{ $period }}**

    ---

    ## 📊 Summary Statistics

    @if (isset($reportData['stats']))
        <x-mail::table>
            | Metric | Value |
            |:-------|------:|
            @foreach ($reportData['stats'] as $key => $value)
                | {{ ucwords(str_replace('_', ' ', $key)) }} |
                {{ is_numeric($value) ? number_format($value, 2) : $value }} |
            @endforeach
        </x-mail::table>
    @endif

    ---

    @if (isset($reportData['highlights']))
        ## 🎯 Highlights

        @foreach ($reportData['highlights'] as $highlight)
            - {{ $highlight }}
        @endforeach
    @endif

    ---

    @if (isset($reportData['trends']))
        ## 📈 Key Trends

        @foreach ($reportData['trends'] as $trend => $description)
            **{{ $trend }}:** {{ $description }}
        @endforeach
    @endif

    ---

    @if (isset($reportData['recommendations']))
        ## 💡 Recommendations

        @foreach ($reportData['recommendations'] as $recommendation)
            - {{ $recommendation }}
        @endforeach
    @endif

    ---

    <x-mail::button :url="$reportData['dashboard_url'] ?? url('/admin/analytics')">
        View Full Analytics Dashboard
    </x-mail::button>

    <small>This is an automated report generated on {{ now()->format('F d, Y \a\t h:i A') }}</small>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
