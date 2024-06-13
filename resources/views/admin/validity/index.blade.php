@extends('adminlte::page')

@section('title', 'Uji Validitas')

@section('content')
    <table id="validity-table" class="display">
        <thead>
            <tr>
                <th>Pertanyaan</th>
                <th>Nilai Validitas</th>
                <th>Status</th>
                <!-- Add more columns as needed -->
            </tr>
        </thead>
        <tbody>
            @foreach ($validities as $validity)
                <tr>
                    <td>{{ $validity['question'] }}</td>
                    <td>{{ $validity['validity'] }}</td>
                    <td>{{ $validity['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop

@section('js')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            $('#validity-table').DataTable({
                "order": [
                    [1, 'desc']
                ], // Adjusted column index for ordering
                "lengthChange": false // Hide the "Show [number] entries" dropdown
            });
        });
    </script>
@stop
