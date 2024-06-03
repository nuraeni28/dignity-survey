<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIK</th>
            <th>Nomor Handphone</th>
            <th>Kabupaten</th>
            <th>Kecamatan</th>
            <th>Desa</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($supporters as $key => $data)
            <tr>
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
                    $nik = "'" . $data->nik;

                @endphp
                <td>{{ $data->name ? $data->name : '-' }}</td>
                <td>{{ $nik ? $nik : '-' }}</td>
                <td>{{ $data->phone ? $data->phone : '-' }}</td>
                <td>{{ $kabupaten->name ? $kabupaten->name : '-' }}</td>
                <td>{{ $kecamatan->name ? $kecamatan->name : '-' }}</td>
                <td>{{ $desa ? $desa->name : '-' }}</td>
            </tr>
        @endforeach
        @php

            // $allAnswers = implode(', ', $customerAnswers);
            // dd($customerAnswers);
        @endphp



    </tbody>
</table>
