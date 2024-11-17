
<table style="border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="{{ count($paslon) + 4 }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold; font-size: 16pt;">REKAPITULASI HASIL SUARA KECAMATAN {{ strtoupper($kecamatan->kec_name) }}</th>
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
            <th colspan="{{ count($paslon) + 2 }}" style="border: 1px solid black; border-width: 1px; border-style: solid; background-color: #5ff554;">Suara Sah</th>
            <th colspan="2" style="border: 1px solid black; border-width: 1px; border-style: solid; background-color: #5ff554;">{{ number_format($total) }}</th>
        </tr>
        <tr>
            <th colspan="{{ count($paslon) + 2 }}" style="border: 1px solid black; border-width: 1px; border-style: solid; background-color: #ff9d9d;">Suara Tidak Sah</th>
            <th colspan="2" style="border: 1px solid black; border-width: 1px; border-style: solid; background-color: #ff9d9d;">{{ number_format($alldata['invalid']) }}</th>
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
            <th rowspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Nama Desa/Kelurahan</th>
            <th colspan="{{ count($paslon) }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Suara Sah</th>
            <th colspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah</th>
            {{-- <th rowspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah Suara Sah</th>
            <th rowspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah Suara Tidak Sah</th> --}}
            <th rowspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Total Suara</th>
        </tr>
        <tr>
            @foreach ($paslon as $ps)
                <th style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">{{ $ps->no_urut }} {{ $ps->nama_paslon }}</th>
            @endforeach
            <th style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold; background-color: #5ff554;">Suara Sah</th>
            <th style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold; background-color: #ff9d9d;">Tidak Sah</th>
        </tr>
    </thead>
    <tbody>
        @if(count($data) > 0)
            @foreach ($data as $dt)
                <tr>
                    {{-- {{ dd($dt['valid']) }} --}}
                    <td style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ $dt['name'] }}</td>
                    @if (count($dt['valid']) > 0)
                    @for ($vt=0; $vt < count($dt['valid']); $vt++)
                        <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ number_format($dt['valid'][$vt]['point']) }}</td>
                    @endfor
                    {{-- @foreach ($dt['valid'] as $vl)
                        <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ number_format($vl['point']) }}</td>
                    @endforeach --}}
                    @endif
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid; background-color: #5ff554;">{{ number_format(intval($dt['total']) - intval($dt['invalid'])) }}</td>
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid; background-color: #ff9d9d;">{{ number_format($dt['invalid']) }}</td>
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ number_format($dt['total']) }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>