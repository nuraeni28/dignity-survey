<table>
    <thead>
        <tr>
            <th>KABUPATEN</th>
            <th>KECAMATAN</th>
            <th>DESA</th>
            <th>TPS</th>
            <th>KATEGORI</th>
            <th>NAMA</th>
            <th>JUMLAH SUARA</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($quickCounts as $key => $data)
            @php
                $kabupaten = DB::table('indonesia_cities')
                    ->select()
                    ->where('id', $data->indonesia_city_id)
                    ->first();
                $kecamatan = DB::table('indonesia_districts')
                    ->select()
                    ->where('id', $data->indonesia_district_id)
                    ->first();
                $desa = DB::table('indonesia_villages')
                    ->select()
                    ->where('id', $data->indonesia_village_id)
                    ->first();
                $dataCaleg = \App\Models\QuickCount::where('tps', $data->tps)
                    ->where('indonesia_village_id', $data->indonesia_village_id)
                    ->where('partai_id', $data->partai_id)
                    ->get();
                $dataCalegCount = $dataCaleg->count();
                // $dataCaleg = DB::table('quick_count')
                //     ->where('tps', $data->tps)
                //     ->where('partai_id', $data->partai_id)
                //     ->get();

                // dd($data);
            @endphp
            @if ($dataCalegCount > 0)
                <!-- Memeriksa jika ada data -->
                <!-- Menampilkan nama partai di baris pertama -->
                <tr>
                    <td>{{ $kabupaten->name ?? '-' }}</td>
                    <td>{{ $kecamatan->name ?? '-' }}</td>
                    <td>{{ $desa ? $desa->name : '-' }}</td>
                    <td>{{ $data->tps }}</td>
                    <td>Nama Partai</td>
                    <td>{{ $dataCaleg->first()->partai->name }}</td> <!-- Mengambil nama partai dari entri pertama -->
                    <td>{{ $dataCaleg->first()->jumlah_suara_partai }}</td>
                    <!-- Mengambil jumlah suara partai dari entri pertama -->
                </tr>

                <!-- Melanjutkan dengan baris untuk caleg -->
                @foreach ($dataCaleg as $index => $caleg)
                    @if ($data->partai_id == 2)
                        <tr>
                            <td>{{ $kabupaten->name ?? '-' }}</td>
                            <td>{{ $kecamatan->name ?? '-' }}</td>
                            <td>{{ $desa ? $desa->name : '-' }}</td>
                            <td>{{ $data->tps }}</td>
                            <td>Nama Caleg</td>
                            <td>{{ $caleg->caleg->name }}</td>
                            <td>{{ $caleg->jumlah_suara_caleg }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
        @endforeach

    </tbody>
</table>
