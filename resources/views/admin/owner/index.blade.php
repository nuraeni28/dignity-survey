@extends('adminlte::page')
@section('title', 'Owner')

@section('content_header')
    <div class="row">
        <div class="col">
            <h1>Data Owner @can('super-admin')
                    <a href="{{ route('owner.create') }}" class="btn btn-success ml-2">
                        <i class="fas fa-lg fa-plus"></i>
                    </a>
                @endcan
            </h1>
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
                            $i = ($owners->currentPage() - 1) * $owners->perPage();
                    @endphp
                    @foreach ($owners as $owner)
                        @php
                            $i = $i + 1;
                        @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $owner->name }}</td>
                            <td>{{ $owner->email }}</td>
                            <td>{{ $owner->email_verified_at ? $owner->email_verified_at->format('d/m/Y') : 'Belum diverifikasi' }}
                            </td>
                            <td>
                                <nobr class="d-flex">
                                    <a href="{{ '/admin/owner/' . $owner->id . '/edit' }}"
                                        class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                        <i class="fa fa-lg fa-fw fa-pen"></i>
                                    </a>
                                    <form class="flex" action="{{ '/admin/owner/' . $owner->id }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure you want to delete?')"
                                            class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                            <i class="fa fa-lg fa-fw fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="{{ '/admin/owner/' . $owner->id }}"
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
            {{ $owners->links() }}
        </div>
    </div>
@stop
