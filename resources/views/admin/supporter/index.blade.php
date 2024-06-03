@extends('adminlte::page')

@section('title', 'Pendukung AAB')
@section('content')
    @php
        $page = request('page', 1);
    @endphp

@section('content_header')
    <div class="col">
        <div style="display: flex; align-items: center;">
            <h1>Pendukung AAB</h1>

            @canany(['owner', 'super-admin'])
                <form action="{{ route('pendukung.index') }}" method="GET" class="form-inline d-flex" style="margin-left:auto">
                    <div class="input-group mr-auto ">
                        <input type="text" name="search" class="form-control" placeholder="Search...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <div class="btn-group" role="group" style="margin-left: 10px">
                    <a href="{{ route('supporter.export') }}" class="btn btn-success" style="width: 100px">
                        Export
                        <i class="fas fa-save"></i>
                    </a>
                </div>

            @endcan
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
                        <th>No HP</th>

                        <th style="width: 40px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = ($supporters->currentPage() - 1) * $supporters->perPage();
                    @endphp
                    @foreach ($supporters as $supporter)
                        @php
                            $i = $i + 1;
                        @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $supporter->name }}</td>
                            <td>{{ $supporter->nik }}</td>
                            <td>{{ $supporter->phone }}</td>

                            <td>

                                <nobr class="d-flex">

                                    <a href="{{ route('pendukung.show', $supporter->id) }}"
                                        class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                        <i class="fa fa-lg fa-fw fa-eye"></i>
                                    </a>
                                </nobr>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $supporters->links() }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
@stop
