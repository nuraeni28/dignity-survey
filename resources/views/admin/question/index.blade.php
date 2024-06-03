@extends('adminlte::page')

@section('title', 'Pertanyaan Responden Lapangan')

@section('content_header')
    <div class="row">
        
        @canany(['owner'])
            <div class="col">
                <h1>Pertanyaan Responden Lapangan<a href="{{ route('question.create') }}" type="button" class="btn btn-success ml-2">
                        <i class="fas fa-lg fa-plus"></i>
                    </a></h1>
            </div>
        @endcan
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 10px">NO</th>
                        <th>PERTANYAAN</th>
                        <th>TIPE</th>
                        <th>Jawaban</th>
                        <th style="width: 40px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                            $i = ($questions->currentPage() - 1) * $questions->perPage();
                    @endphp
                    @foreach ($questions as $question)
                        @php
                            $i = $i + 1;
                            $answer = json_decode($question->answer);
                        @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $question->question }}</td>
                            <td>{{ $question->type }}</td>
                            <td>

                                @if (is_array($answer) && count($answer) > 0)
                                    @foreach ($answer as $option)
                                        {{ $option }}
                                    @endforeach
                                @elseif (is_object($answer) && isset($answer->option))
        @foreach ($answer->option as $option)
            {{ $option }}
        @endforeach
    @else
        -
    @endif
                            </td>
                            <td>
                                <nobr class="d-flex">
                                    <a href="{{ route('question.edit', $question->id) }}"
                                        class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                        <i class="fa fa-lg fa-fw fa-pen"></i>
                                    </a>
                                    <form class="flex" action="{{ route('question.destroy', $question->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure you want to delete?')"
                                            class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                            <i class="fa fa-lg fa-fw fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('question.show', $question->id) }}"
                                        class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                        <i class="fa fa-lg fa-fw fa-eye"></i>
                                    </a>
                                </nobr>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $questions->links() }}
        </div>
    </div>
@stop
