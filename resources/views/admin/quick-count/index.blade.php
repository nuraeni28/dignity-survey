@extends('adminlte::page')

@section('title', 'Real Count')
@section('content')
    @php
        $page = request('page', 1);
    @endphp

@section('content_header')
    <div class="col">
        <div style="display: flex; align-items: center;">
            <h1>Real Count
                @canany(['koordinator-area'])
                    <a href="{{ route('real-count.create') }}" type="button" class="btn btn-success ml-2">
                        <i class="fas fa-lg fa-plus"></i>
                    </a>
                @endcan
            </h1>

            @canany(['koordinator-area'])
                <form action="{{ route('real-count.index') }}" method="GET" class="form-inline d-flex" style="margin-left:auto">
                    <div class="input-group mr-auto ">
                        <input type="text" name="search" class="form-control" placeholder="Search...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            @endcan
              @canany(['koordinator-area'])
                <form action="{{ route('quick-count.countCaleg') }}" method="GET" style="padding-left: 20px">
                    <button class="btn btn-warning">TPS</button>
                </form>
            @endcan
             @if (Auth::user()->hasRole('super-admin'))
                <div class="btn-group" role="group" style="margin-left: 10px; padding-top:20px">
                    <a href="{{ route('quick-count.export') }}" class="btn btn-success">
                        Export
                        <i class="fas fa-save"></i>
                    </a>
                </div>
                 <form action="{{ route('quick-count.countSumVote') }}" method="GET"
                    style="padding-left: 20px; padding-top:20px">
                    <button class="btn btn-warning">Update Perolehan Suara</button>
                </form>
            @endif
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
                        
                        <th>PARTAI</th>
                        <th>TOTAL SUARA PARTAI</th>
                        <th>KABUPATEN</th>
                        <th>KECAMATAN</th>
                        <th>DESA</th>
                        <th>TPS</th>
                        <th style="width: 40px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = ($quickCounts->currentPage() - 1) * $quickCounts->perPage();
                    @endphp
                    @foreach ($quickCounts as $quickCount)
                        @php
                            $i = $i + 1;
                          
                        @endphp
                        <tr>
                            <td>{{ $i }}</td>
                           

                    <td>{{ $quickCount->partai->name }}</td>
                    <td>{{ $quickCount->jumlah_suara_partai }}</td>
                    <td>{{ $quickCount->city->name }}</td>
                    <td>{{ $quickCount->district->name }}</td>
                    <td>{{ $quickCount->village->name }}</td>
                    <td>{{ $quickCount->tps }}</td>


                    <td>

                        <nobr class="d-flex">
                            <a href="{{ route('real-count.show', $quickCount->id) }}"
                                class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>
  @if (!Auth::user()->hasRole('koordinator-area'))
                                <form class="flex"
                                    action="{{ route('real-count.destroy', [$quickCount->id, 'page' => $page]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                        class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                        <i class="fa fa-lg fa-fw fa-trash"></i>
                                    </button>
                                </form>
                                
                            @endif
                             <a href="{{ route('real-count.edit', $quickCount->id) }}"
                                class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>
                        </nobr>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $quickCounts->links() }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
@stop
