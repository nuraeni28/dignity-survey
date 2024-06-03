<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\QuestionSosmed;
use Illuminate\Support\Facades\Auth;

class QuestionController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->recomended_by!=null){
          $questions = QuestionSosmed::where('admin_id', Auth::user()->admin_id)->orWhere('owner_id', Auth::user()->owner_id)->get(); 
        }
        else{
             $questions = Question::where('admin_id', Auth::user()->admin_id)->orWhere('owner_id', Auth::user()->owner_id)->get(); 
        }
      
        return $this->sendResponse(QuestionResource::collection($questions), 'Berhasil mengambil data pertanyaan');
    }
      public function questionSosmed()
    {
        $questions = QuestionSosmed::where('admin_id', Auth::user()->admin_id)->orWhere('owner_id', Auth::user()->owner_id)->get();
        return $this->sendResponse(QuestionResource::collection($questions), 'Berhasil mengambil data pertanyaan');
    }
    
    
}
