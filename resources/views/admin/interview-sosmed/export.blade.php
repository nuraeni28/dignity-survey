@foreach ($interview as $key => $data)
    @php
        $surveys = DB::table('interview_data')
            ->select()
            ->where('interview_id', $data->interview->id)
            ->get();
        // dd($interview);
    @endphp
@endforeach
<table>
    <thead>
        <tr>
            <th>ID Respon</th>
            <th>Start Time</th>
            <th>Waktu Penyelesaian</th>
            <th>Lokasi Responden</th>
            <th>Nama Relawan</th>
            <th>NIK Relawan</th>
            <th>No Telpon Relawan</th>
            <th>Email Relawan</th>
            <th>NIK Responden</th>
            <th>Nama Responden</th>
            <th>No Telpon Responden</th>
            <th>TPS Responden</th>
            <th>Alamat</th>
            <th>Kabupaten</th>
            <th>Kecamatan</th>
            <th>Desa</th>
            <th>Usia</th>
            <th>Agama</th>
            <th>Pendidikan Terakhir</th>
            <th>Pekerjaan</th>
            <th>Jumlah Anggota Keluarga</th>
            <th>Jumlah Pemilih Dalam KK</th>
            <th>Status Perkawinan</th>
            <th>Pendapatan Rata-rata Perbulan</th>
            <th>Rekaman</th>
            <th>Foto</th>
            @foreach ($surveys as $survey)
                <th>{{ $survey->question }}</th>
                {{-- @php dd($survey->question);@endphp --}}
            @endforeach
        </tr>
    </thead>
    <tbody>
        {{-- @php
            dd($interview);
        @endphp --}}
        @foreach ($interview as $key => $data)
            <tr>
                @php
                    $kabupaten = DB::table('indonesia_cities')
                        ->select()
                        ->where('id', $data->customer->indonesia_city_id)
                        ->first();
                    $kecamatan = DB::table('indonesia_districts')
                        ->select()
                        ->where('id', $data->customer->indonesia_district_id)
                        ->first();
                    $desa = DB::table('indonesia_villages')
                        ->select()
                        ->where('id', $data->customer->indonesia_village_id)
                        ->first();
                    $nik = "'" . $data->user->nik;
                    $nikResponden = "'" . $data->customer->nik;
                    $tanggalLahir = \Carbon\Carbon::parse($data->customer->dob);
                    $usia = $tanggalLahir->age;
                    $baseURL = 'https://app.inside.web.id/public/public/record/';
                    $baseURLPhoto = 'https://app.inside.web.id/public/public/image/';
                    $baseURLMaps = 'http://maps.google.com/?q';
                    $photoFile = $data->interview->photo;
                    $photoFile = str_replace(' ', '%20', $photoFile);
                    $lat = $data->interview->lat;
                    $long = $data->interview->long;
                    $recordFile = $data->interview->record_file;
                    $recordFile = str_replace(' ', '%20', $recordFile);
                    $link = $baseURL . $recordFile;
                    $linkPhoto = $baseURLPhoto . $photoFile;
                    $linkMaps = $baseURLMaps . '=' . $lat . ',' . $long;
                    // dd($link);
                    // dd($data->customer->dob);
                @endphp


                {{-- @php
                      dd($data->user->nik);
                  @endphp --}}
                @php
                    $surveys = DB::table('interview_data')
                        ->select()
                        ->where('interview_id', $data->interview->id)
                        ->get();
                @endphp
                <td>{{ $data->interview->id }}</td>
                <td>{{ $data->interview->start_time }}</td>
                <td>{{ $data->interview->end_time }}</td>
                <td><a href="{{ $linkMaps }}">{{ $linkMaps }}</a></td>
                <td>{{ $data->user->name }}</td>
                <td>{{ $nik }}</td>
                <td>{{ $data->user->phone }}</td>
                <td>{{ $data->user->email }}</td>
                <td>{{ $nikResponden }}</td>
                <td>{{ $data->customer->name }}</td>
                <td>{{ $data->customer->phone }}</td>
                <td>{{ $data->customer->tps }}</td>
                <td>{{ $data->customer->address }}</td>
                <td>{{ $kabupaten->name }}</td>
                <td>{{ $kecamatan->name }}</td>
                <td>{{ $desa->name }}</td>
                <td>{{ $usia }}</td>
                <td>{{ $data->customer->religion }}</td>
                <td>{{ $data->customer->education }}</td>
                <td>{{ $data->customer->job }}</td>
                <td>{{ $data->customer->family_member }}</td>
                <td>{{ $data->customer->family_election }}</td>
                <td>{{ $data->customer->marrital_status }}</td>
                <td>{{ $data->customer->monthly_income }}</td>
                <td><a href="{{ $link }}">{{ $link }}</a></td>
                <td><a href="{{ $linkPhoto }}">{{ $linkPhoto }}</a></td>
                @foreach ($surveys as $survey)
                    <td>{{ $survey->customer_answer }}</td>
                @endforeach

            </tr>
        @endforeach
        @php

            // $allAnswers = implode(', ', $customerAnswers);
            // dd($customerAnswers);
        @endphp



    </tbody>
</table>
