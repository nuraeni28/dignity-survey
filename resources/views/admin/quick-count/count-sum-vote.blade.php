@extends('adminlte::page')

@section('title', 'Update Perolehan Suara')
<!-- Add DataTables CSS -->
@section('content_header')


@stop

@section('content')


    <table id="count-sum-performa-table" class="display">


        <thead>
            <tr>

                <th hidden>No</th>
                <th>Nama</th>
                <th>Caleg</th>
                <th>Jumlah Perolehan Suara</th>


                <!-- Add more columns as needed -->
            </tr>
        </thead>
        <tbody>


            @foreach ($quickCounts as $q)
                @php
                    // dd($quickCounts);
                    $partai = \App\Models\Partai::where('id', $q['partai_id'])->first();
                @endphp


                @if ($partai->id == 2)
                    <tr>
                        <td hidden>{{ $partai->id }}</td>
                        <td>{{ $partai->name }}</td>

                        <td>
                            <table>
                                @foreach ($countCaleg as $c)
                                    <tr>
                                        <td>{{ $c->caleg->name }}</td>
                                        <td>{{ $c->total_suara_caleg }}</td>

                                    </tr>
                                @endforeach
                            </table>

                        </td>
                        <td>{{ $q['total_suara_partai'] }}</td>
                    </tr>
                @else
                    <tr>


                        <td hidden>{{ $partai->id }}</td>
                        <td>{{ $partai->name }}</td>
                        <td></td>


                        <td>{{ $q['total_suara_partai'] }}</td>


                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

@stop

@section('js')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            $('#count-sum-performa-table').DataTable({
                "lengthChange": false, // Hide the "Show [number] entries" dropdown
                "order": [
                    [0, "asc"]
                ] // Sort by the first column (assuming ID is the first column, change the index accordingly if it's not)
            });
        });
    </script>
@stop
