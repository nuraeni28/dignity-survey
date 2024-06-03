@extends('adminlte::page')
@section('title', 'Relawan')
@section('content')

@section('content_header')
    <form action="{{ route('relawan-sosmed.index') }}" method="GET">
        <div class="pb-3 pt-3 pr-2"
            style="background-color: white; border: 1px solid #f3f1f1; border-radius: 5px;margin-right:10px;margin-bottom:10px;margin-top:10px">
            <div class="ms-2 d-inline-block">
                <label for="lokasi" style="margin-right: 10px;">Pilih Sumber Informasi</label>
                <select name="recomended_id" class="form-select-sm" id="city" style="width: 250px;">
                    <option value="-" disabled selected data-subtext="Pilih">Sumber Informasi Perekrutan
                    </option>
                    <option value="sosial media">Sosial Media (FB/IG)</option>
                    <option value="karyawan benur kita">Karyawan Benur Kita</option>
                    <option value="yayasan baramuli">Guru/Siswa Yayasan Baramuli</option>
                    <option value="relawan">Relawan</option>
                    <option value="tsurvey">Tsurvey</option>
                </select>
            </div>
            <button class="btn-primary btn-sm btn" type="submit"
                style="width: 65px; color: black; font-weight: bold; background-color: #00A3FF;margin-left:30px">Filter</button>
        </div>
    </form>
    <div style="display: flex; align-items: center;">
        <h1 style="margin-right: 10px;">Data Relawan Sosmed</h1>

        <form action="{{ route('relawan-sosmed.index') }}" method="GET" class="form-inline ml-auto">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search...">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
         <div class="btn-group" role="group" style="margin-left: 10px">
            <a href="{{ route('relawan-sosmed.export') }}" class="btn btn-success" style="width: 100px">
                Export
                <i class="fas fa-save"></i>
            </a>
        </div>
        <!--<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#AllSosmed"-->
        <!--    style="width: auto;height:40px;margin-left:20px">-->
        <!--    Update Status Kabupaten-->
        <!--</button>-->
         <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateAll"
            style="width:auto;height:40px;margin-left:20px">

            Update Status Record
        </button>
    </div>

@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="" id="select_all_ids"></th>
                        <th style="width: 10px">No</th>
                        <th>Name</th>
                        <th>Owner</th>
                        <th>Admin</th>
                        <th>Email</th>
                        <th>Lolos</th>
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
                             $volunteerResponse = \App\Models\VolunteerResponse::where('id_user', $volunteer->id) ->orderBy('created_at', 'desc')
            ->first();

                             $volunteerDetail = null;
                            $allAnswersCorrect = true;
                        @endphp
                        <tr>
                            {{-- @php
                                dd($volunteer);
                            @endphp --}}
                              <td><input type="checkbox" name="ids[]" value="{{ $volunteer->id }}" class="checkbox_ids">
                            </td>
                            <td>{{ $i }}</td>
                            <td>{{ $volunteer->name }}</td>
                            <td>{{ \App\Models\User::find($volunteer->owner_id)->name }}</td>
                            <td>
                                @if ($volunteer->admin_id)
                                    {{ \App\Models\User::find($volunteer->admin_id)->name }}
                                @else
                                    Tidak ada Admin
                                @endif
                            </td>
                            <td>{{ $volunteer->email }}</td>
                             <td>
                                @if ($volunteerResponse && $volunteerResponse->id > 54)
                                    @php

                                        $volunteerResponseDetail = \App\Models\VolunteerResponseDetail::where('id_tutorial_response', $volunteerResponse->id)->get();

                                        $allAnswersCorrect = true;

                                        foreach ($volunteerResponseDetail as $responseDetail) {
                                            // Accessing the related question for the response detail
                                            $question = $responseDetail->question;

                                            // Here you can compare the answer with the expected correct answer
                                            // Example: Assuming the correct answer is stored in the question model
                                            $correctAnswer = $question->correct_answer;

                                            // Compare the given answer with the correct answer
                                            if ($responseDetail->answer != $correctAnswer) {
                                                $allAnswersCorrect = false;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if ($allAnswersCorrect)
                                        Ya
                                    @else
                                        Ada Jawaban Salah
                                    @endif
                                @elseif($volunteerResponse)
                                    Ya
                                @else
                                    Tidak
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
                                        <a href="{{ route('relawan-sosmed.edit', ['relawan_sosmed' => $volunteer->id, 'page' => $volunteers->currentPage()]) }}"
                                            class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </a>
                                        <form class="flex"
                                            action="{{ route('relawan-sosmed.destroy', ['relawan_sosmed' => $volunteer->id, 'page' => $volunteers->currentPage()]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Are you sure you want to delete?')"
                                                class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                                <i class="fa fa-lg fa-fw fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                    <a href="{{ route('relawan-sosmed.show', $volunteer->id) }}"
                                        class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                        <i class="fa fa-lg fa-fw fa-eye"></i>
                                    </a>
                                </nobr>
                            </td>
                        </tr>
                        @include('components.modal-status-sosmed')
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $volunteers->links() }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
      <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var selectAllCheckbox = document.getElementById('select_all_ids');
            var checkboxes = document.querySelectorAll('.checkbox_ids');
            var updateSelectedButton = document.getElementById('buttonStatus');

            updateSelectedButton.addEventListener('click', function() {
                var selectedIds = [];
                console.log(selectedIds);

                checkboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        selectedIds.push(checkbox.value);
                    }
                });

                if (selectedIds.length > 0) {
                    // Clear any previously added inputs
                    document.querySelectorAll('#updateAllSelectedRecordForm input[name="selected_ids[]"]')
                        .forEach(function(input) {
                            input.remove();
                        });

                    // Add the selected IDs to the form data
                    selectedIds.forEach(function(id) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_ids[]';
                        input.value = id;
                        document.querySelector('#updateAllSelectedRecordForm').appendChild(input);
                    });

                    // Submit the form
                    document.querySelector('#updateAllSelectedRecordForm').submit();
                } else {
                    alert('No checkboxes selected.');
                }
            });

            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        });
    </script>

@stop
