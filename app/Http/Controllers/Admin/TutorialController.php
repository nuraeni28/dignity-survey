<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\VolunteerResponse;
use App\Models\VolunteerResponseDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TutorialController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->input('user');

        $tutorialId = VolunteerResponse::create([
            'id_user' => $user[0],
        ]);
        // dd($tutorialId);
        if ($tutorialId) {
            // return $tutorialId;

            $answers = $request->input('answer');
            $question_id = $request->input('question_id');

            try {
                foreach ($answers as $key => $answer) {
                    $responseDetail = new VolunteerResponseDetail();
                    $responseDetail->id_tutorial_response = $tutorialId->id; // Sesuaikan dengan struktur database Anda
                    $responseDetail->answer = $answer; // Sesuaikan dengan struktur database Anda
                    $responseDetail->id_question = $question_id[$key]; // Sesuaikan dengan struktur database Anda
                    $responseDetail->save();

                    // dd($responseDetail);
                }

                // // Update status user
                // $user = User::find($user[0]);
                // $user->status = 'Aktif';
                // $user->save();

                return response()->json(['success' => true, 'message' => 'Jawaban berhasil disimpan.'], 200);
            } catch (\Exception $e) {
                return response()->json(['failed' => false, 'message' => 'Terjadi kesalahan saat menyimpan jawaban.'], 500);
            }
        }

        return response()->json(['success' => true]);
    }
}
