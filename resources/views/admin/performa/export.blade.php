<table>
    <thead>
        <tr>
            <th>Email</th>
            <th>Nama</th>
            <th>Jumlah Wawancara</th>
            <th>Frekuensi</th>
            <th>Performa Rate</th>
            <th>Kecamatan</th>
            <th>Desa</th>


        </tr>
    </thead>
    <tbody>
        @php
            $sortedInterviews = $interviews->sortByDesc(function ($data) {
                // Hitung ulang performance rate
                $iv = App\Models\InterviewSchedule::with(['user', 'interview'])
                    ->has('user')
                    ->has('interview')
                    ->where('user_id', $data->user_id)
                    ->select(\DB::raw('count(user_id) as interview_count'))
                    ->first();

                $active_rate = ($data->frequency / $data->days_since_first_interview) * 100;
                $qty_rate = ($iv->interview_count / 50) * 100;
                $performance_rate = ($qty_rate + $active_rate) / 2;

                $data->performance_rate = $performance_rate; // Simpan performance rate pada objek $data

                return $performance_rate;
            });
        @endphp

        @foreach ($sortedInterviews as $key => $data)
            <tr>
                @php
                    $iv = App\Models\InterviewSchedule::with(['user', 'interview'])
                        ->has('user')
                        ->has('interview')
                        ->where('user_id', $data->user_id)
                        ->select(\DB::raw('count(user_id) as interview_count')) // Use aggregate function
                        ->first();

                    $active_rate = ($data->frequency / $data->days_since_first_interview) * 100;
                    $qty_rate = ($iv->interview_count / 50) * 100;
                    $performance_rate = ($qty_rate + $active_rate) / 2;
                @endphp


                <td>{{ $data->user ? $data->user->email : '-' }}</td>
                <td>{{ $data->user ? $data->user->name : '-' }}</td>
                <td>{{ $iv->interview_count }}</td>
                <td>{{ $data->frequency }}</td>
                <td>{{ number_format($performance_rate, 0) }}%</td>
                 <td>{{ $data->user && $data->user->district ? $data->user->district->name : '-' }}</td>
                <td>{{ $data->user && $data->user->village ? $data->user->village->name : '-' }}</td>



            </tr>
        @endforeach
        @php

            // $allAnswers = implode(', ', $customerAnswers);
            // dd($customerAnswers);
        @endphp



    </tbody>
</table>
