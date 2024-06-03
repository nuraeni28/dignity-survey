<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Email</th>
            <th>NIK</th>
            <th>Nomor Handphone</th>
            <th>Alamat</th>
            <th>Kabupaten</th>
            <th>Kecamatan</th>
            <th>Sumber Informasi</th>
            <th>Terverifikasi</th>
            <th>Lolos</th>

        </tr>
    </thead>
    <tbody>

        @foreach ($users as $key => $data)
            <tr>
                @php
                    $volunteerResponse = \App\Models\VolunteerResponse::where('id_user', $data->id)->first();
                    $kabupaten = DB::table('indonesia_cities')
                        ->select()
                        ->where('id', $data->indonesia_city_id)
                        ->first();
                    $kecamatan = DB::table('indonesia_districts')
                        ->select()
                        ->where('id', $data->indonesia_district_id)
                        ->first();
                    $nik = "'" . $data->nik;

                @endphp
                <td>{{ $data->name ? $data->name : '-' }}</td>
                <td>{{ $data->jenis_kelamin ? $data->jenis_kelamin : '-' }}</td>
                <td>{{ $data->email ? $data->email : '-' }}</td>
                <td>{{ $nik ? $nik : '-' }}</td>
                <td>{{ $data->phone ? $data->phone : '-' }}</td>
                <td>{{ $data->address ? $data->address : '-' }}</td>
                <td>{{ $data->kabupaten ? $data->kabupaten : '-' }}</td>
                <td>{{ $data->kecamatan ? $data->kecamatan : '-' }}</td>
                <td>{{ $data->recomended_by ? $data->recomended_by : '-' }}</td>
                <td>{{ $data->email_verified_at ? $data->email_verified_at : '-' }}</td>
                <td>
                    @if ($volunteerResponse)
                        Ya
                    @else
                        Tidak
                    @endif
                </td>




            </tr>
        @endforeach
        @php

            // $allAnswers = implode(', ', $customerAnswers);
            // dd($customerAnswers);
        @endphp



    </tbody>
</table>
