
<table style="border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="{{ count($paslon) + 4 }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold; font-size: 16pt;">REKAPITULASI HASIL SUARA KECAMATAN {{ strtoupper($kecamatan->kec_name) }}</th>
        </tr>
        <tr><th>&nbsp;</th></tr>
        <tr>
            <th style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">No Urut Paslon</th>
            <th colspan="{{ count($paslon) + 1 }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Nama Paslon</th>
            <th colspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah Suara</th>
        </tr>
        @php $total = 0; @endphp
        @foreach ($alldata['valid'] as $all)
        <tr>
            <th style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ $all['urut'] }}</th>
            <th colspan="{{ count($paslon) + 1 }}" style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ $all['name'] }}</th>
            <th colspan="2" style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ ($all['total']) }}</th>
        </tr>
        @php $total = $total + intval($all['total']); @endphp
        @endforeach
        <tr>
            <th colspan="{{ count($paslon) + 2 }}" style="border: 1px solid black; border-width: 1px; border-style: solid; background-color: #5ff554;">Suara Sah</th>
            <th colspan="2" style="border: 1px solid black; border-width: 1px; border-style: solid; background-color: #5ff554;">{{ ($total) }}</th>
        </tr>
        <tr>
            <th colspan="{{ count($paslon) + 2 }}" style="border: 1px solid black; border-width: 1px; border-style: solid; background-color: #ff9d9d;">Suara Tidak Sah</th>
            <th colspan="2" style="border: 1px solid black; border-width: 1px; border-style: solid; background-color: #ff9d9d;">{{ ($alldata['invalid']) }}</th>
        </tr>
        <tr>
            <th colspan="{{ count($paslon) + 2 }}" style="border: 1px solid black; border-width: 1px; border-style: solid; font-size: 16pt;">Total Suara</th>
            <th colspan="2" style="border: 1px solid black; border-width: 1px; border-style: solid; font-size: 16pt;">{{ (intval($alldata['invalid']) + $total) }}</th>
        </tr>
        <tr><th>&nbsp;</th></tr>
        <tr><th>&nbsp;</th></tr>
        <tr>
            <th colspan="{{ count($paslon) + 4 }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold; font-size: 16px;">SEBARAN SUARA</th>
        </tr>
        <tr><th>&nbsp;</th></tr>
        <tr>
            <th rowspan="2" style="text-align: center; vertical-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Nama Desa/Kelurahan</th>
            <th colspan="{{ count($paslon) }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Suara Sah</th>
            <th colspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah</th>
            {{-- <th rowspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah Suara Sah</th>
            <th rowspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah Suara Tidak Sah</th> --}}
            <th rowspan="2" style="text-align: center; vertical-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Total Suara</th>
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
                        <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ ($dt['valid'][$vt]['point']) }}</td>
                    @endfor
                    {{-- @foreach ($dt['valid'] as $vl)
                        <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ ($vl['point']) }}</td>
                    @endforeach --}}
                    @endif
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid; background-color: #5ff554;">{{ (intval($dt['total']) - intval($dt['invalid'])) }}</td>
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid; background-color: #ff9d9d;">{{ ($dt['invalid']) }}</td>
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ ($dt['total']) }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<table style="border-collapse: collapse;">
    <thead>
        <tr><th>&nbsp;</th></tr>
        <tr><th>&nbsp;</th></tr>
        <tr>
            <th colspan="{{ count($paslon) + 5 }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold; font-size: 16pt;">DATA TIAP TPS</th>
        </tr>
        <tr><th>&nbsp;</th></tr>
        <tr>
            <th rowspan="2" style="text-align: center; vertical-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Nomor TPS</th>
            <th rowspan="2" style="text-align: center; vertical-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Nama Desa/Kelurahan</th>
            <th colspan="{{ count($paslon) }}" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Suara Sah</th>
            <th colspan="2" style="text-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Jumlah</th>
            <th rowspan="2" style="text-align: center; vertical-align: center; border: 1px solid black; border-width: 1px; border-style: solid; font-weight: bold;">Total Suara</th>
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
        @if(count($tps) > 0)
            @foreach ($tps as $tp)
                <tr>
                    <td style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ $tp['name'] }}</td>
                    <td style="border: 1px solid black; border-width: 1px; border-style: solid;">{{ $tp['desa'] }}</td>
                    @if (count($tp['paslon']) > 0)
                    @for ($v=0; $v < count($tp['paslon']); $v++)
                        <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ ($tp['paslon'][$v]['voting']) }}</td>
                    @endfor
                    @endif
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid; background-color: #5ff554;">{{ ($tp['valid']) }}</td>
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid; background-color: #ff9d9d;">{{ ($tp['invalid']) }}</td>
                    <td style="text-align: right; border: 1px solid black; border-width: 1px; border-style: solid;">{{ ($tp['total']) }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>