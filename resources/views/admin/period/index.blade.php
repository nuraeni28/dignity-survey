@extends('adminlte::page')

@section('title', 'Periode')

@section('content_header')
    <div class="row">
         @canany(['super-admin', 'owner'])
        <div class="col">
            <h1>Periode <a href="{{ route('period.create') }}" class="btn btn-success  ml-2"><i
                        class="fas fa-lg fa-plus"></i></a></h1>
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
          <th>TANGGAL MULAI</th>
          <th>TANGGAL SELESAI</th>
           @canany(['super-admin', 'owner'])
          <th style="width: 40px">AKSI</th>
          @endcan
        </tr>
      </thead>
      <tbody>
        @php
            $i = ($periods->currentPage() - 1) * $periods->perPage();
        @endphp
        @foreach($periods as $period)
        @php
        $i = $i+1;
        @endphp
        <tr>
          <td>{{$i}}</td>
          <td>{{$period->start_date->format('d/m/Y')}}</td>
          <td>{{$period->end_date->format('d/m/Y')}}</td>
           @canany(['super-admin', 'owner'])
          <td>
            <nobr class="d-flex">
              <a href="{{route('period.edit', $period->id)}}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                <i class="fa fa-lg fa-fw fa-pen"></i>
              </a>
              <form class="flex" action="{{route('period.destroy', $period->id)}}" method="POST">
                @csrf
                @method('DELETE')
                <button onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                  <i class="fa fa-lg fa-fw fa-trash"></i>
                </button>
              </form>
            </nobr>
          </td>
          @endcan
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer clearfix">
    {{$periods->links()}}
  </div>
</div>
@stop
