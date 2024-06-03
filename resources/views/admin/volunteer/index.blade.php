@extends('adminlte::page')
@section('title', 'Surveyor')
@section('content')
  @php
        $page = request('page', 1);
    @endphp
@section('content_header')
    <div style="display: flex; align-items: center;">
        <h1 style="margin-right: 10px;">Data Surveyor</h1>
        @canany(['admin'])
            <a href="{{ route('relawan.create') }}" type="button" class="btn btn-success">
                <i class="fas fa-lg fa-plus"></i>
            </a>
        @endcan

        <form action="{{ route('relawan.index') }}" method="GET" class="form-inline ml-auto">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search...">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
              @if (Auth::user()->hasRole('koordinator-area'))
            <div class="btn-group" role="group" style="margin-left: 10px">
                <a href="{{ route('relawan.export') }}" class="btn btn-success" style="width: 100px">
                    Export
                    <i class="fas fa-save"></i>
                </a>
            </div>
        @endif
          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#All"
            style="width: auto;height:40px;margin-left:20px">
            Update Status
        </button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 10px">No</th>
                        <th>Name</th>
                        <th>Owner</th>
                        <th>Admin</th>
                        <th>Email</th>
                        <th>Target</th>
                        <th>Terverifikasi</th>
                        <th>Status</th>
                        <th>Terakhir Login</th>
                        <th style="width: 60px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = ($volunteers->currentPage() - 1) * $volunteers->perPage();
                    @endphp
                    @foreach ($volunteers as $volunteer)
                        @php
                            $i = $i + 1;
                        @endphp
                        <tr>
                            {{-- @php
                                dd($volunteer);
                            @endphp --}}
                            <td>{{ $i }}</td>
                            <td>{{ $volunteer->name }}</td>
                            <td>{{ optional(\App\Models\User::find($volunteer->owner_id))->name ?? '' }}
                            </td>
            <td>
                                @if ($volunteer->admin_id)
                                    {{ optional(\App\Models\User::find($volunteer->admin_id))->name ?? '' }}
                                @else
                                    
                                @endif
                            </td>
                            <td>{{ $volunteer->email }}</td>
                            <td>
                                @if ($volunteer->targetInterview)
                                    {{ count($volunteer->doneInterviews) }} /
                                    {{ $volunteer->targetInterview->target_interviews }}
                                @else
                                    Belum ada target
                                @endif
                            </td>
                            <td>{{ $volunteer->email_verified_at ? $volunteer->email_verified_at->format('d/m/Y') : 'Belum diverifikasi' }}
                            </td>

                            <td>

                                {{ $volunteer->status ?? '-' }}
                            </td>
                            <td>
                                @if ($volunteer->statusLogin)
                                    {{ $volunteer->statusLogin->created_at ?? '-' }}
                                @else
                                    Belum ada history login
                                @endif
                            <td>
                                <nobr class="d-flex">
                                    @canany(['admin', 'super-admin'])
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#N{{ $volunteer->id }}">
                                            Pilih Status
                                        </button>
                                        <a href="{{ route('relawan.edit', ['relawan' => $volunteer->id, 'page' => $volunteers->currentPage()]) }}"
                                            class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </a>
                                        <form class="flex" action="{{ route('relawan.destroy',  ['relawan' => $volunteer->id, 'page' => $volunteers->currentPage()]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Are you sure you want to delete?')"
                                                class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                                <i class="fa fa-lg fa-fw fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                    <a href="{{ route('relawan.show', $volunteer->id) }}"
                                        class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                        <i class="fa fa-lg fa-fw fa-eye"></i>
                                    </a>
                                </nobr>
                            </td>
                        </tr>
                        @include('components.modal-status')
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $volunteers->links() }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

@stop
