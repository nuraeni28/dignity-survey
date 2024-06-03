<table>
    <thead>
        <tr>
            <th>Nama Relawan</th>
            <th>Email Relawan</th>
            <th>Nama Responden</th>
            <th>NIK</th>
            <th>NO KK</th>
            <th>Usia</th>
            <th>Pekerjaan</th>
            <th>TPS</th>
            <th>Jumlah Pemilih Dalam KK</th>
            <th>Nomor Handphone</th>
            <th>Kabupaten</th>
            <th>Kecamatan</th>
            <th>Desa</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($customers as $key => $data)
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
                    $nokk = "'" . $data->no_kk;
                    if ($data->status_kunjungan == 'Sudah') {
                        $status = 'DTDC';
                    } elseif ($data->type == 'pemantapan') {
                        $status = 'Tambahan';
                    } else {
                        $status = 'Belum';
                    }

                    $tanggalLahir = \Carbon\Carbon::parse($data->dob);
                    $usia = $tanggalLahir->age;
                    // dd($data);
                @endphp
                <td>{{ $data->surveyor ? $data->surveyor : '-' }}</td>
                <td>{{ isset($data->user->email) ? $data->user->email : (isset($data->email) ? $data->email : '-') }}
                </td>

                <td>{{ $data->name ? $data->name : '-' }}</td>
                <td>{{ $nik ? $nik : '-' }}</td>
                <td>{{ $nokk ? $nokk : '-' }}</td>
                <td>{{ $usia ? $usia : '-' }}</td>
                <td>{{ $data->job ? $data->job : '-' }}</td>
                <td>{{ $data->tps ? $data->tps : '-' }}</td>
                <td>{{ $data->family_election ? $data->family_election : '-' }}</td>
                <td>{{ $data->phone ? $data->phone : '-' }}</td>
                <td>{{ $kabupaten->name ? $kabupaten->name : '-' }}</td>
                <td>{{ $kecamatan->name ? $kecamatan->name : '-' }}</td>
                <td>{{ $desa ? $desa->name : '-' }}</td>
                <td>{{ $status }}</td>
            </tr>
        @endforeach
        @php

            // $allAnswers = implode(', ', $customerAnswers);
            // dd($customerAnswers);
        @endphp



    </tbody>
</table>
