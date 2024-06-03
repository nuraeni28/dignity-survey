<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>NIK</th>
            <th>Nomor Handphone</th>
            <th>Jenis Kelamin</th>
            <th>Kecamatan</th>
            <th>Desa</th>
            <th>TPS</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($users as $key => $data)
            <tr>
                @php
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
                <td>{{ $data->email ? $data->email : '-' }}</td>
                <td>{{ $nik ? $nik : '-' }}</td>
                <td>{{ $data->phone ? $data->phone : '-' }}</td>
                <td>{{ $data->jenis_kelamin ? $data->jenis_kelamin : '-' }}</td>
                <td>{{ $kecamatan->name ? $kecamatan->name : '-' }}</td>
               <td>{{ $desa ? $desa->name : '-' }}</td>
               <td>{{ $data->tps ? $data->tps : '-' }}</td>




            </tr>
        @endforeach
        @php

            // $allAnswers = implode(', ', $customerAnswers);
            // dd($customerAnswers);
        @endphp



    </tbody>
</table>
