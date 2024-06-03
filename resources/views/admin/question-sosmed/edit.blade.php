@extends('adminlte::page')

@section('title', 'Edit Pertanyaan')

@section('content_header')
    <h1>Edit Pertanyaan</h1>
@stop

@section('content')
    <form action="{{ route('question-sosmed.update', $question->id) }}" method="post">
        @csrf
        @method('PUT')
        @php
            $answers = json_decode($question->answer, true);
        @endphp
        <x-adminlte-input name="question" value="{{ old('question', $question->question) }}" required label="Pertanyaan"
            placeholder="pertanyaan" type="text" igroup-size="md" />
        <x-adminlte-select id="type" required value="{{ old('type', $question->type) }}" name="type" label="Tipe">
            <option value="essai" {{ old('type', $question->type) == 'essai' ? 'selected' : '' }}>Essai</option>
            <option value="option" {{ old('type', $question->type) == 'option' ? 'selected' : '' }}>Pilihan</option>
            <option value="multiple" {{ old('type', $question->type) == 'multiple' ? 'selected' : '' }}>Multiple</option>
        </x-adminlte-select>
        @if ($answers && $answers['option'])
            @foreach ($answers['option'] as $option)
                <x-adminlte-input value="{{ old('type', $option) }}" name="option[]" label="Opsi" placeholder="Opsi"
                    type="text" igroup-size="md" />
            @endforeach
        @endif
        @for ($i = 1; $i < 15; $i++)
            @if (isset($answers[$i . '_option']))
                <x-adminlte-input name="{{ $i . '_question' }}" value="{{ old($i . '_question', $answers[$i . '_question']) }}"
                    required label="Pertanyaan" placeholder="pertanyaan" type="text" igroup-size="md" />
                <x-adminlte-select id="type" required value="{{ old('type', $answers[$i . '_type']) }}"
                    name="{{ $i . '_type' }}" label="Tipe">
                    <option value="Essai" {{ old('type', $answers[$i . '_type']) == 'Essai' ? 'selected' : '' }}>Essai
                    </option>
                    <option value="Option" {{ old('type', $answers[$i . '_type']) == 'Option' ? 'selected' : '' }}>Pilihan
                    </option>
                    <option value="Multiple" {{ old('type', $answers[$i . '_type']) == 'Multiple' ? 'selected' : '' }}>Multiple
                    </option>
                </x-adminlte-select>
                @foreach ($answers[$i . '_option'] as $option)
                    <x-adminlte-input value="{{ old('type', $option) }}" name="{{ $i . '_option[]' }}" label="Opsi"
                        placeholder="Opsi" type="text" igroup-size="md" />
                @endforeach
            @endif
        @endfor
        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>
@stop
