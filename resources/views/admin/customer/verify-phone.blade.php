@extends('adminlte::page')

@section('title', 'Verifikasi Nomor HP Responden')
@php
    $page = request('page', 1);
@endphp

@section('content_header')
    <div class="col">
        <div style="display: flex; align-items: center;">
            <h1>Responden
            </h1>


            <form action="{{ route('responden.verifyPhone') }}" method="GET" class="form-inline d-flex"
                style="margin-left:auto">
                <div class="input-group mr-auto ">
                    <input type="text" name="search" class="form-control" placeholder="Search...">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>

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
                        <th>NAMA</th>
                        <th>NIK</th>
                        <th>NO KK</th>
                        <th>EMAIL</th>
                        <th>NOMOR HP</th>
                        <th>STATUS</th>
                        <th style="width: 40px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = ($customers->currentPage() - 1) * $customers->perPage();
                    @endphp
                    @foreach ($customers as $customer)
                        @php
                            $i = $i + 1;
                        @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->nik }}</td>
                            <td>{{ $customer->no_kk }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->status_verified ?? 'Unverified' }}</td>
                            <td>
                                <nobr class="d-flex">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#N{{ $customer->id }}">
                                        Pilih Status
                                    </button>
                                </nobr>
                            </td>
                        </tr>
                        @include('components.modal-status-phone')
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $customers->links() }}
        </div>
    </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
@stop
