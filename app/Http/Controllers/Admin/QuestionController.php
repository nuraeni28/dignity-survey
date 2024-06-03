<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuestionType;
use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::check('user') && !Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')) {
            return abort('403');
        }
        $id = Auth::user()->id;
        $questions = null;
        if (Gate::check('admin')) {
            $id = Auth::user()->owner_id;
            $questions = Question::where('owner_id', $id)->paginate(10);
        }
        if (Gate::check('super-admin')) {
            $questions = Question::paginate(10);
            // dd($questions);
        } else {
            $questions = Question::where('owner_id', $id)->paginate(10);
        }

        return view('admin.question.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = QuestionType::cases();
        return view('admin.question.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreQuestionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Gate::check('owner')) {
            return abort('403');
        }
        $this->validate($request, [
            'question' => ['required'],
            'type' => ['required'],
            'option' => ['nullable', 'array'],
        ]);

        if ($request->has('option')) {
            $request->merge([
                'option' => $request->except(['question', 'type', '_token']),
            ]);
        }

        $request->merge([
            'answer' => json_encode($request->option),
        ]);

        if (Gate::check('owner')) {
            $request->merge([
                'owner_id' => Auth::user()->id,
            ]);
        }
        if (Gate::check('admin')) {
            $request->merge([
                'admin_id' => Auth::user()->id,
                'owner_id' => Auth::user()->owner_id,
            ]);
        }

        Question::create($request->only(['question', 'type', 'owner_id', 'admin_id', 'answer']));

        return redirect()
            ->back()
            ->with('success', 'Berhasil membuat pertanyaan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        return view('admin.question.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')) {
            return abort('403');
        }
        $id = Auth::user()->id;
        $questions = Question::where('admin_id', $id)
            ->orWhere('owner_id', $id)
            ->where('id', '<>', $question->id)
            ->get();
        // dd($question);
        return view('admin.question.edit', compact(['question', 'questions']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\UpdateQuestionRequest  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        if (!Gate::check('owner') && !Gate::check('admin')) {
            return abort('403');
        }

        $this->validate($request, [
            'question' => ['sometimes'],
            'type' => ['sometimes'],
            'option' => ['nullable', 'array'],
        ]);

        if ($request->has('option')) {
            $request->merge([
                'option' => $request->except(['question', 'type', '_token', '_method']),
            ]);
            $request->merge([
                'answer' => json_encode($request->option),
            ]);
        }

        if (Gate::check('owner')) {
            $request->merge([
                'owner_id' => Auth::user()->id,
            ]);
        }
        if (Gate::check('admin')) {
            $request->merge([
                'admin_id' => Auth::user()->id,
                'owner_id' => Auth::user()->owner_id,
            ]);
        }

        $question->update($request->only(['question', 'type', 'owner_id', 'admin_id', 'answer']));

        return redirect()
            ->back()
            ->with('success', 'Berhasil mengedit pertanyaan.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()
            ->route('question.index')
            ->with('message', __('Berhasil menghapus pertanyaan.'));
    }
}
