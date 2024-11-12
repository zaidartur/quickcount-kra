<table style="border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="{{ count($paslon) + 4 }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold; font-size: 16pt;">REKAPITULASI HASIL SUARA</th>
        </tr>
        <tr><th>&nbsp;</th></tr>
        <tr>
            <th style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">No Urut</th>
            <th colspan="{{ count($paslon) + 1 }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Nama Paslon</th>
            <th colspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah Suara</th>
        </tr>
        @php $total = 0; @endphp
        @foreach ($alldata['valid'] as $all)
        <tr>
            <th style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ $all['urut'] }}</th>
            <th colspan="{{ count($paslon) + 1 }}" style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ $all['name'] }}</th>
            <th colspan="2" style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ number_format($all['total']) }}</th>
        </tr>
        @php $total = $total + intval($all['total']); @endphp
        @endforeach
        <tr>
            <th colspan="{{ count($paslon) + 2 }}" style="border: 1px solid black; border-width: 1px; border-style: solid;">Suara Tidak Sah</th>
            <th colspan="2" style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ number_format($alldata['invalid']) }}</th>
        </tr>
        <tr>
            <th colspan="{{ count($paslon) + 2 }}" style="border: 1px solid black; border-width: 1px; border-style: solid; font-size: 16pt;">Total Suara</th>
            <th colspan="2" style="border: 1px solid black; border-width: 1px; border-style: solid; font-size: 16pt;">{{ number_format(intval($alldata['invalid']) + $total) }}</th>
        </tr>
        <tr><th>&nbsp;</th></tr>
        <tr><th>&nbsp;</th></tr>
        <tr>
            <th colspan="{{ count($paslon) + 4 }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold; font-size: 16px;">SEBARAN SUARA</th>
        </tr>
        <tr><th>&nbsp;</th></tr>
        <tr>
            <th rowspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Nama Kecamatan</th>
            <th colspan="{{ count($paslon) }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Suara Sah</th>
            <th rowspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah Suara Sah</th>
            <th rowspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah Suara Tidak Sah</th>
            <th rowspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Total Suara</th>
        </tr>
        <tr>
            @foreach ($paslon as $ps)
                <th style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">{{ $ps->no_urut }} {{ $ps->nama_paslon }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @if(count($data) > 0)
            @foreach ($data as $dt)
                <tr>
                    <td style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ $dt['kec_name'] }}</td>
                    @foreach ($dt['paslons'] as $item)
                        <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ $item['voting'] }}</td>
                    @endforeach
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ number_format(intval($dt['total']) - intval($dt['invalid'])) }}</td>
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ number_format($dt['invalid']) }}</td>
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ $dt['total'] }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>