@extends('adminlte::page')

@section('title', 'Pekerjaan')

@section('content_header')
<div class="row">
  <h1>Pekerjaan</h1>
  @canany(['owner', 'admin'])
  <a href="{{route('occupation.create')}}" type="button" class="btn btn-success ml-2">
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
          <th>Opsi</th>
          <th style="width: 40px">AKSI</th>
        </tr>
      </thead>
      <tbody>
        @php
            $i = ($occupations->currentPage() - 1) * $occupations->perPage();
        @endphp
        @foreach($occupations as $occupation)
        @php
        $i = $i+1;
        @endphp
        <tr>
          <td>{{$i}}</td>
          <td>{{$occupation->name}}</td>
          <td>
            <nobr class="d-flex">
              <a href="{{route('occupation.edit', $occupation->id)}}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                <i class="fa fa-lg fa-fw fa-pen"></i>
              </a>
              <form class="flex" action="{{route('occupation.destroy', $occupation->id)}}" method="POST">
                @csrf
                @method('DELETE')
                <button onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                  <i class="fa fa-lg fa-fw fa-trash"></i>
                </button>
              </form>
            </nobr>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer clearfix">
    {{$occupations->links()}}
  </div>
</div>
@stop
