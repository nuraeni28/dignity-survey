@extends('adminlte::page')

@section('title', 'Detail Interview')

@section('content_header')
    <h1>Detail Interview</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <tr>
                    <td width="50%">{{ __('Periode Interview') }}</td>
                    <td width="50%">
                        {{ $schedule->period ? $schedule->period->start_date->format('d/m/Y') . '-' . $schedule->period->end_date->format('d/m/Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td width="50%">{{ __('Interviewer') }}</td>
                    <td width="50%">
                        <a href="{{ route('relawan.show', $schedule->user->id) }}">{{ $schedule->user->name }}</a>
                    </td>
                </tr>
                <tr>
                    <td width="50%">{{ __('Responden') }}</td>
                    <td width="50%">
                        <a href="{{ route('responden.show', $schedule->customer->id) }}">{{ $schedule->customer->name }}</a>
                    </td>
                </tr>
                <tr>
                    <td width="50%">{{ __('Lokasi Interview') }}</td>
                    <td width="50%">
                        @if ($schedule->interview->location)
                            {{ $schedule->interview->location }} </br><a
                                href="http://maps.google.com/?q={{ $schedule->interview->lat }},{{ $schedule->interview->long }}">Buka
                                Maps</a>
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">{{ __('Maps Point') }}</td>
                    <td width="50%">
                        @if ($schedule->interview->lat)
                            {{ $schedule->interview->lat }} , {{ $schedule->interview->long }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">{{ __('Tanggal Interview') }}</td>
                    <td width="50%">
                        @if ($schedule->interview_date)
                            {{ $schedule->interview_date->format('d/m/Y') }}
                        @else
                            {{ __('Interview belum dilakukan') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">{{ __('Durasi Interview') }}</td>
                    <td width="50%">
                        {{ \Carbon\Carbon::parse($schedule->interview->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($schedule->interview->end_time)->format('H:i') }}
                        {{ \Carbon\Carbon::parse($schedule->interview->start_time)->diffInMinutes(\Carbon\Carbon::parse($schedule->interview->end_time)) }}
                        Menit
                    </td>
                </tr>
                @if ($schedule->interview->record_file)
                    <tr>
                        <td width="50%">{{ __('File Rekaman') }}</td>
                        <td width="50%">
                            <audio controls>
                                <source src="{{ asset('public/public/record/' . $schedule->interview->record_file) }}"
                                    type="audio/mp4">
                                Your browser does not support the audio element.
                            </audio>
                        </td>
                    </tr>
                @endif
                @if ($schedule->interview->photo)
                    <tr>
                        <td width="50%">{{ __('Foto') }}</td>
                        <td width="50%">
                            <img src="{{ url('/public/public/image/' . $schedule->interview->photo) }}" alt="Image"
                                height="200" width="200" />
                        </td>
                    </tr>
                @endif
                @if ($schedule->interview)
                    <table class="table table-borderless p-0">
                        <thead>
                            <tr>
                                <th>
                                    {{ __('No') }}
                                </th>
                                <th>
                                    {{ __('Pertanyaan') }}
                                </th>
                                <th>
                                    {{ __('Jawaban') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedule->interview->data as $data)
                                <tr>
                                    <td>
                                        {{ $loop->index + 1 }}
                                    </td>
                                    <td>
                                        {{ $data->question }}
                                    </td>
                                    <td>
                                        @if ($data->customer_answer)
                                            {{ $data->customer_answer }}
                                        @else
                                            {{ __('-') }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </table>
        </div>
    </div>
@stop
