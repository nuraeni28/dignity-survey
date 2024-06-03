@extends('adminlte::page')
@section('title', 'Koordinator Area')

@section('content_header')
    <div class="row">

        @can('owner')
            <div class="col">
                <h1>Data Koordinator Area<a href="{{ route('kordinator.create') }}" type="button" class="btn btn-success ml-2">
                        <i class="fas fa-lg fa-plus"></i>
                    </a></h1>
            </div>
        @endcan
        @can('super-admin')
            <h1>Data Koordinator Area</h1>
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
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>TERVIRIFIKASI</th>
                        <th style="width: 40px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = ($coordinators->currentPage() - 1) * $coordinators->perPage();
                    @endphp
                    @foreach ($coordinators as $coordinator)
                        @php
                            $i = $i + 1;
                        @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $coordinator->name }}</td>
                            <td>{{ $coordinator->email }}</td>
                            <td>{{ $coordinator->email_verified_at ? $coordinator->email_verified_at->format('d/m/Y') : 'Belum diverifikasi' }}
                            </td>
                            <td>
                                @canany(['owner', 'super-admin'])
                                    <nobr class="d-flex">
                                        <a href="{{ route('kordinator.edit', $coordinator->id) }}"
                                            class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </a>
                                        <form class="flex" action="{{ route('kordinator.destroy', $coordinator->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Are you sure you want to delete?')"
                                                class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                                <i class="fa fa-lg fa-fw fa-trash"></i>
                                            </button>
                                        </form>

                                        <a href="{{ route('kordinator.show', $coordinator->id) }}"
                                            class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                            <i class="fa fa-lg fa-fw fa-eye"></i>
                                        </a>
                                    </nobr>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $coordinators->links() }}
        </div>
    </div>
@stop
