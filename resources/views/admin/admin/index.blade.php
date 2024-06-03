@extends('adminlte::page')
@section('title', 'Admin')

@section('content_header')

   @php
        $page = request('page', 1);
    @endphp
<div class="col">
        <div style="display: flex; align-items: center;">
            <h1>Data Admin
                @can('owner')
                    <a href="{{ route('admin.create') }}" type="button" class="btn btn-success ml-2">
                        <i class="fas fa-lg fa-plus"></i>
                    </a>
                @endcan
            </h1>

            @canany(['owner', 'super-admin'])
                <form action="{{ route('admin.index') }}" method="GET" class="form-inline d-flex" style="margin-left:auto">
                    <div class="input-group mr-auto ">
                        <input type="text" name="search" class="form-control" placeholder="Search...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
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
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>TERVIRIFIKASI</th>
                        <th style="width: 40px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = ($admins->currentPage() - 1) * $admins->perPage();
                    @endphp
                    @foreach ($admins as $admin)
                        @php
                            $i = $i + 1;
                        @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->email_verified_at ? $admin->email_verified_at->format('d/m/Y') : 'Belum diverifikasi' }}
                            </td>
                            <td>
                                <nobr class="d-flex">
                                    <a href="{{ route('admin.edit', ['admin' => $admin->id, 'page' => $page]) }}"
                                        class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                        <i class="fa fa-lg fa-fw fa-pen"></i>
                                    </a>
                                    <form class="flex" action="{{ route('admin.destroy', ['admin' => $admin->id, 'page' => $admins->currentPage()]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure you want to delete?')"
                                            class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                            <i class="fa fa-lg fa-fw fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.show', $admin->id) }}"
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
            {{ $admins->links() }}
        </div>
    </div>
@stop
