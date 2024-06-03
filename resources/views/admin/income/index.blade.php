@extends('adminlte::page')

@section('title', 'Pendapatan')

@section('content_header')
<div class="row">
  <h1>Pendapatan</h1>
  @canany(['owner', 'admin'])
  <a href="{{route('income.create')}}" type="button" class="btn btn-success ml-2">
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
            $i = ($incomes->currentPage() - 1) * $incomes->perPage();
        @endphp
        @foreach($incomes as $income)
        @php
        $i = $i+1;
        @endphp
        <tr>
          <td>{{$i}}</td>
          <td>{{$income->name}}</td>
          <td>
            <nobr class="d-flex">
              <a href="{{route('income.edit', $income->id)}}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                <i class="fa fa-lg fa-fw fa-pen"></i>
              </a>
              <form class="flex" action="{{route('income.destroy', $income->id)}}" method="POST">
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
    {{$incomes->links()}}
  </div>
</div>
@stop
