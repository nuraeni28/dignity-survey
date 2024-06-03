@extends('adminlte::page')

@section('title', 'Detail Pertanyaan')

@section('content_header')
<h1>Detail Pertanyaan</h1>
@stop

@section('content')
<div class="card">
  <div class="card-body p-0">
    <table class="table">
      <tr>
        <td width="50%">Pertanyaan</td>
        <td width="50%">
          {{$question->question}}
        </td>
      </tr>
      <tr>
        <td width="50%">Tipe</td>
        <td width="50%">
          {{$question->type}}
        </td>
      </tr>
      <tr>
        @php
        $i = 1;
        $answer = json_decode($question->answer);
        @endphp
        <td width="50%">Jawaban</td>
        <td width="50%">
          {{join(' | ',$answer->option ?? [])}}
        </td>
      </tr>
      @for($j = $i; $j < 15; $j++) @if(isset(((array) $answer)[$j."_option"])) @foreach(((array) $answer)["$j"."_option"] as $option) @if($j==$i) <tr>
        <td width="50%">{{$option}}</td>
        <td width="50%">{{((array) $answer)[$j."_question"]}}</td>
        </tr>
        <tr>
          <td width="50%">Jawaban</td>
          <td width="50%">{{join(' | ',((array) $answer)[$j."_option"])}}</td>
        </tr>
        @endif
        @php
        $i++;
        @endphp
        @endforeach
        @endif
        @endfor
    </table>
  </div>
</div>
@stop
