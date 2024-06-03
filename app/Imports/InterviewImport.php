<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\Customer;
use App\Models\InterviewSchedule;
use App\Models\InterviewData;
use App\Models\Interview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class InterviewImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $user = User::find($row[0]);
        // dd($row[3]);
        if ($user) {
            $nikValue = substr($row[5], 0, 1) === "'" ? substr($row[5], 1) : $row[5];
            $customer = Customer::create([
                'name' => $row[2],
                'email' => $row[3],
                'phone' => $row[4],
                'nik' => $nikValue,
                'dob' => date('Y-m-d', strtotime($row[6])),
                'jenis_kelamin' => $row[7],
                'address' => $row[8],
                'indonesia_province_id' => $row[9],
                'indonesia_city_id' => $row[10],
                'indonesia_district_id' => $row[11],
                'indonesia_village_id' => $row[12],
                'religion' => $row[13],
                'education' => $row[14],
                'job' => $row[15],
                'family_member' => $row[16],
                'family_election' => $row[17],
                'marrital_status' => $row[18],
                'monthly_income' => $row[19],
                'tps' => $row[20],
                'owner_id' => $user->owner_id,
                'admin_id' => $user->admin_id,
                'surveyor_id' => $user->id
            ]);
            $data = [
                'period_id' => 28,
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                 'interview_date' => date('Y-m-d', strtotime($row[1])),
            ];

            $schedule = InterviewSchedule::create($data);
            $dataInterview = ['interview_date' => date('Y-m-d', strtotime($row[1])), 'owner_id' => $user->owner_id, 'admin_id' => $user->admin_id, 'interview_schedule_id' => $schedule->id];

            $interview = Interview::create($dataInterview);

            $questions = [
                [
                    'question' => 'Apakah anda bersedia memilih Ahmad Abdy Baramuli pada pemilu 14 februari mendatang?',
                    'type' => 'option', // Gantilah dengan tipe data yang sesuai
                    'answer' => '[Ya, Tidak, Tidak Tahu/ Tidak Jawab]', // Gantilah dengan jawaban yang sesuai
                    'customer_answer' => $row[21], // Gantilah dengan jawaban pelanggan yang sesuai
                    'question_id' => 115, // Gantilah dengan ID pertanyaan yang sesuai
                    'interview_id' => $interview->id,
                ],
                [
                    'question' => 'Kalau boleh tahu, apakah alasan Anda memilih Ahmad Abdy Baramuli?',
                    'type' => 'option', // Gantilah dengan tipe data yang sesuai
                    'answer' => '[Suka Dengan Profilnya, Suka Dengan Visi-Misinya, Tidak Tahu/ Tidak Jawab]', // Gantilah dengan jawaban yang sesuai
                    'customer_answer' => $row[22], // Gantilah dengan jawaban pelanggan yang sesuai
                    'question_id' => 115, // Gantilah dengan ID pertanyaan yang sesuai
                    'interview_id' => $interview->id,
                ],
            ];
            foreach ($questions as $question) {
                $interviewData = InterviewData::create($question);
            }

            return $interviewData;
        }

        return null;
    }
}
