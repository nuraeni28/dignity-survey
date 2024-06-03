@extends('adminlte::page')

@section('title', 'Responden')
@section('content')
 @php
        $page = request('page', 1);
    @endphp

@section('content_header')
<div class="col">
        <div style="display: flex; align-items: center;">
            <h1>Responden
                @canany(['owner', 'admin', 'koordinator-area'])
                    <a href="{{ route('responden.create') }}" type="button" class="btn btn-success ml-2">
                        <i class="fas fa-lg fa-plus"></i>
                    </a>
                @endcan
            </h1>

            @canany(['owner', 'super-admin', 'koordinator-area'])
                <form action="{{ route('responden.index') }}" method="GET" class="form-inline d-flex" style="margin-left:auto">
                    <div class="input-group mr-auto ">
                        <input type="text" name="search" class="form-control" placeholder="Search...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
                 <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#status-kunjungan"
                    style="height:40px;margin-left:20px">
                    Status Kunjungan
                </button>
                 <form action="{{ route('responden.getAdditionalCustomer') }}" method="GET" style="padding-left: 20px">
                    <button type="submit" id="additional-button" class="btn btn-warning">Data Tambahan</button>
                </form>
                 @canany(['super-admin'])
                <form action="{{ route('responden.getDuplicateCustomer') }}" method="GET" style="padding-left: 20px">
                    <button type="submit" id="duplicate-customer-button" class="btn btn-info">No HP Duplikat</button>
                </form>
                 @endcan
            @endcan
        </div>
                  <div class="col">
            <div style="display: flex; align-items: center;">
                @canany(['owner', 'admin', 'koordinator-area'])
                    <form action="{{ route('responden.createComitment') }}" method="GET" style="padding-top:20px">
                        <button type="submit" id="additional-button" class="btn btn-warning">Buat Pemantapan Data</button>
                    </form>
                @endcan
                @if (Auth::user()->hasRole('koordinator-area'))
                    <div class="btn-group" role="group" style="margin-left: 10px; padding-top:20px">
                        <a href="{{ route('responden.export') }}?search={{ Request::get('search') }}" class="btn btn-success">
                            Export Pemantapan Data
                            <i class="fas fa-save"></i>
                        </a>
                    </div>
                @endif
                @if (Auth::user()->hasRole('super-admin'))
                    <div class="btn-group" role="group" style="margin-left: 10px; padding-top:20px">
                        <a href="{{ route('customer.exportManual') }}" class="btn btn-success">
                            Export Pemantapan Data Manual
                            <i class="fas fa-save"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
  <div class="card-body p-0">
    <table class="table">
      <thead>
        <tr>
          <th style="width: 10px">NO</th>
          <th>NAMA RELAWAN</th>
          <th>NAMA RESPONDEN</th>
          <th>NIK</th>
          <th>NO KK</th>
          <th>EMAIL</th>
          <th>NOMOR HP</th>
          <th>STATUS KUNJUNGAN</th>
          <th style="width: 40px">AKSI</th>
        </tr>
      </thead>
      <tbody>
        @php
            $i = ($customers->currentPage() - 1) * $customers->perPage();
        @endphp
        @foreach($customers as $customer)
        @php
        $i = $i+1;
        @endphp
        <tr>
          <td>{{$i}}</td>
          <td>
        
                {{ $customer->surveyor }}

        </td>
          <td>{{$customer->name}}</td>
          <td>{{$customer->nik}}</td>
          <td>{{$customer->no_kk}}</td>
          <td>{{$customer->email}}</td>
          <td>{{$customer->phone}}</td>
          <td>{{$customer->status_kunjungan ?? 'Belum'}}</td>
          <td>
              
            <nobr class="d-flex">
                 @if (!Auth::user()->hasRole('koordinator-area') || Auth::user()->id == 2583 || Auth::user()->id == 2580 || Auth::user()->id == 2579 || Auth::user()->id == 2581)
                                        <a href="{{ route('responden.edit', ['responden' => $customer->id, 'page' => $page]) }}"
                                            class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </a>
                                    @endif
                @if(!Auth::user()->hasRole('koordinator-area'))
              
              <form class="flex" action="{{route('responden.destroy', ['responden' => $customer->id, 'page' => $page])}}" method="POST">
                @csrf
                @method('DELETE')
                <button onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                  <i class="fa fa-lg fa-fw fa-trash"></i>
                </button>
              </form>
              @endif
              <a href="{{route('responden.show', $customer->id)}}" class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
              </a>
              @if($customer->metode=='manual' || Auth::user()->id == 2583 || Auth::user()->id == 2580 || Auth::user()->id == 2579 || Auth::user()->id == 2581)
                <a href="{{ route('responden.addComitment', $customer->id) }}"
                                        class="btn btn-xs btn-default text-primary mx-1 shadow" title="Add">
                                        <i class="fa fa-lg fa-fw fa-plus"></i> Tambah Anggota
                </a>
                @endif
                
            </nobr>
          </td>
        </tr>
          @include('components.modal-status-kunjungan')
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer clearfix">
    {{$customers->links()}}
  </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
@stop
