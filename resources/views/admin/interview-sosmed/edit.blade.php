@extends('adminlte::page')

@section('title', 'Edit Interview Relawan Sosmed')

@section('content_header')
    <h1>Edit Interview Relawan Sosmed</h1>
@stop



@section('content')
    <form action="{{ route('interview-sosmed.update', ['interview_sosmed', $interview->id]) }}" method="post"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @if ($interview && $interview->interview)
            <x-adminlte-input value="{{ old('interview_date', $interview->interview->interview_date) }}" name="interview_date"
                label="Tanggal Interview" placeholder="tanggal interview" type="date" igroup-size="md" />
            <div class="mt-2" style="padding-bottom: 20px">
                <label for="current_image">Photo</label>
                <br>
                @if ($interview && $interview->interview && $interview->interview->photo)
                    <img src="{{ asset('/public/public/image/' . $interview->interview->photo) }}"
                        alt="Current Profile Image" class="img-thumbnail" width="200">
                @else
                    <p>No photo available</p>
                @endif
            </div>
        @else
            <p>No interview data available</p>
        @endif
        <x-adminlte-input-file name="profile_image" placeholder="Choose a file..." type="file" accept="image/*">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-lightblue">
                    <i class="fas fa-upload"></i>
                </div>
            </x-slot>
        </x-adminlte-input-file>
        <div class="mt-2" style="padding-bottom: 20px">
            <label for="record">Record</label>
            <br>
            @if ($interview && $interview->interview)
                @if ($interview->interview->record_file)
                    <audio controls>
                        <source src="{{ asset('public/public/record/' . $interview->interview->record_file) }}"
                            type="audio/mp4">
                        Your browser does not support the audio element.
                    </audio>
                @else
                    <p>No record available</p>
                @endif
            @else
                <p>No interview data available</p>
            @endif
        </div>
        @foreach ($interview->interview->data ?? []  as $index => $data)
            <x-adminlte-input value="{{ old('pertanyaan_' . $index, $data->question) }}"
                name="pertanyaan_{{ $index }}" label="Pertanyaan {{ $index + 1 }}"
                placeholder="Masukkan pertanyaan" type="text" igroup-size="md" />
            @php
                // Memisahkan string option menjadi array
                $options = explode(', ', $data->answer);

            @endphp
            @if ($data->type == 'option')
                <select name="jawaban_{{ $index }}" id="jawaban_{{ $index }}" class="form-control">
                    <option value="">-- Pilih Jawaban --</option>
                    @foreach ($options as $option)
                        @php
                            // Menghapus tanda kurung siku [ dan ] dari pilihan
                            $cleanedOption = trim($option, '[]');
                        @endphp
                        <option value="{{ $cleanedOption }}"
                            {{ old('Jawaban_' . $index, $data->customer_answer) == $cleanedOption ? 'selected' : '' }}>
                            {{ $cleanedOption }}
                        </option>
                    @endforeach
                </select>
            @else
                <x-adminlte-input value="{{ old('Jawaban_' . $index, $data->customer_answer ?: '-') }}"
                    name="jawaban_{{ $index }}" label="Jawaban {{ $index + 1 }}" placeholder="Masukkan Jawaban"
                    type="text" igroup-size="md" />
            @endif
        @endforeach

        <x-adminlte-button style="margin-top:20px" class="btn-flat" type="submit" label="Submit" theme="success"
            icon="fas fa-lg fa-save" />
    </form>
@stop
