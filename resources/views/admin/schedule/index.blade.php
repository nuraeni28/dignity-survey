@extends('adminlte::page')

@section('title', 'Jadwal')

@section('content_header')
<div class="row">
  <h1>Jadwal</h1>
  @canany(['owner', 'admin'])
  <a href="{{route('schedule.create')}}" type="button" class="btn btn-success ml-2">
    <i class="fas fa-lg fa-plus"></i>
  </a>
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
          <th>PERIODE</th>
          <th>INTERVIEWER</th>
          <th>RESPONDEN</th>
          <th>TANGGAL INTERVIEW</th>
          <th style="width: 40px">AKSI</th>
        </tr>
      </thead>
      <tbody>
        @php
            $i = ($schedules->currentPage() - 1) * $schedules->perPage();
        @endphp
        @foreach($schedules as $schedule)
        @php
        $i = $i+1;
        @endphp
        <tr>
          <td>{{$i}}</td>
          <td>{{$schedule->period->start_date->format('d/m/Y')}} - {{$schedule->period->end_date->format('d/m/Y')}}</td>
          <td>{{$schedule->user->name}}</td>
          <td>{{$schedule->customer->name}}</td>
          <td>{{$schedule->interview_date ? $schedule->interview_date->format('d/m/Y') : 'Interview belum dilakukan'}}</td>
          <td>
            <nobr class="d-flex">
              <form class="flex" action="{{route('schedule.destroy', $schedule->id)}}" method="POST">
                @csrf
                @method('DELETE')
                <button onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                  <i class="fa fa-lg fa-fw fa-trash"></i>
                </button>
              </form>
              @if ($schedule->interview_date)
              <a href="{{ route('schedule.show', $schedule->id) }}" class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
              </a>
              @endif
            </nobr>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer clearfix">
    {{$schedules->links()}}
  </div>
</div>
@stop
