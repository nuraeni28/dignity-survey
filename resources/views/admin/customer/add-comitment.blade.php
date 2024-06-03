@extends('adminlte::page')

@section('title', 'Tambah Anggota Keluarga')

@section('content_header')
    <h1>Tambah Anggota Keluarga</h1>
@stop

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;800;900&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fontt-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


</head>
<style>
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {

        background-color: white;
        height: calc(2.875rem + 2px);

        padding-top: 7px;
        /* Adjust the value to your preference */
    }


    .required-asterisk {
        color: red;
        /* Color for the asterisk */
        font-weight: bold;
        /* Optionally make the asterisk bold */
    }

    /* Style for the dropdown arrow to match the rounded corners */
    .select2-container--default .select2-selection--single .select2-selection__arrow {

        background-color: #D9D9D9;
        height: calc(2.875rem + 2px);
        padding-top: 7px;
        /* Adjust the value to match the border-radius above */
    }

    .clone-form {
        border-top: 5px solid #ccc;
        /* Adjust color and thickness as needed */
        margin-top: 10px;
        /* Optional margin for spacing between forms */
        padding-top: 10px;
        /* Optional padding for spacing between forms */
    }
</style>
@php

    $cities = new App\Http\Controllers\DependentDropdownController();
    $cities = $cities->citiesData(Auth::user()->province->id);
    $districts = new App\Http\Controllers\DependentDropdownController();
    if (Auth::user()->city->id) {
        $districts = $districts->districtsData(Auth::user()->city->id);
    } else {
        $districts = [];
    }
    // dd($districts);
    $villages = new App\Http\Controllers\DependentDropdownController();
    if (old('indonesia_district_id')) {
        $villages = $villages->villagesData(old('indonesia_district_id'));
    } elseif (Gate::check('admin')) {
        $villages = $villages->villagesData(Auth::user()->district->id);
    } else {
        $villages = [];
    }
@endphp

@section('content')
    {{-- <form action="{{ route('responden.storeNewComitment') }}" method="post">
        @csrf
       
        <x-adminlte-input value="{{ old('name') }}" name="name" required label="Nama " placeholder="nama"
            type="text" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
            <x-slot name="label">
                <label for="name" class="required-label">Nama <span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input value="{{ old('nokk') }}" name="nokk" label="NO KK" required placeholder="No KK"
            value="{{ $customer->no_kk }}" readonly type="number" igroup-size="md" id="nik-input"> <x-slot name="label">
                <label for="name" class="required-label">NO KK<span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input value="{{ old('nik') }}" name="nik" label="NIK" required placeholder="nik"
            type="number" igroup-size="md" id="nik-input"> <x-slot name="label">
                <label for="name" class="required-label">NIK <span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input value="{{ $customer->phone }}" name="phone" label="Phone (Tidak Wajib)" placeholder="phone" readonly
            type="number" igroup-size="md" />



        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form> --}}
    <form action="{{ route('responden.storeNewComitment') }}" method="post" id="dynamicForm">
        @csrf
        <!-- Add Form button -->
        <button type="button" class="btn btn-primary" id="addForm">Tambah Form</button>

        <!-- Kurang Form button -->
        <button type="button" class="btn btn-danger" id="removeForm" style="display:none;">Hapus Form</button>
        <!-- Submit button -->
        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
        <!-- Initial set of fields -->
        <x-adminlte-input name="idCus" igroup-size="lg" hidden style="margin-top:10px" value="{{ $customer->id }}">

        </x-adminlte-input>
        <x-adminlte-input name="surveyor_id[]" igroup-size="lg" hidden style="margin-top:10px"
            value="{{ $customer->user->id }}">

        </x-adminlte-input>
      

        <x-adminlte-input value="{{ old('name') }}" name="name[]" required label="Nama " placeholder="nama"
            type="text" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
            <x-slot name="label">
                <label for="name" class="required-label">Nama <span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>

     
        <x-adminlte-input value="{{ old('nokk') }}" name="nokk[]" label="NO KK" required placeholder="No KK"
            value="{{ $customer->no_kk }}" readonly type="number" igroup-size="md" id="nik-input">
            <x-slot name="label">
                <label for="name" class="required-label">NO KK<span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>

        <x-adminlte-input value="{{ old('nik') }}" name="nik[]" label="NIK" required placeholder="nik"
            type="number" igroup-size="md" id="nik-input">
            <x-slot name="label">
                <label for="name" class="required-label">NIK <span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>

        <x-adminlte-input value="{{ $customer->phone }}" name="phone[]" label="Phone" placeholder="phone" readonly
            type="number" igroup-size="md" />

        <!-- Dynamic form fields will be added here -->




    </form>

    <!-- Template for cloned form fields -->
    <div id="formTemplate" style="display: none;">
        <!-- Clone the existing form fields here and modify the names as needed -->
        <x-adminlte-input name="surveyor_id[]" igroup-size="lg" hidden style="margin-top:10px"
            value="{{ $customer->user->id }}">

        </x-adminlte-input>
        <x-adminlte-input name="surveyor[]" igroup-size="lg" readonly label="Relawan " style="margin-top:10px"
            value="{{ $customer->user->name }}">
            <x-slot name="label">
                <label for="name" class="required-label">Relawan <span
                        class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>

        <x-adminlte-input value="{{ old('name') }}" name="name[]" required label="Nama " placeholder="nama"
            type="text" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
            <x-slot name="label">
                <label for="name" class="required-label">Nama <span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>

        <x-adminlte-select name="gender[]" label="Jenis Kelamin (Tidak Wajib)">
            <option value="-" disabled selected>Pilih Jenis Kelamin</option>
            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            <option value="Laki-Laki" {{ old('gender') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
        </x-adminlte-select>

        <x-adminlte-input value="{{ old('nokk') }}" name="nokk[]" label="NO KK" required placeholder="No KK"
            value="{{ $customer->no_kk }}" readonly type="number" igroup-size="md" id="nik-input">
            <x-slot name="label">
                <label for="name" class="required-label">NO KK<span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>

        <x-adminlte-input value="{{ old('nik') }}" name="nik[]" label="NIK" required placeholder="nik"
            type="number" igroup-size="md" id="nik-input">
            <x-slot name="label">
                <label for="name" class="required-label">NIK <span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>

        <x-adminlte-input value="{{ old('phone') }}" name="phone[]" label="Phone" placeholder="phone"
            type="number" igroup-size="md" />

        <!-- Repeat for other form fields... -->
    </div>

@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('input[name^="nik"]').on('input', function() {
                if ($(this).val().length > 16) {
                    $(this).val($(this).val().slice(0, 16));
                }
            });
        });

        $(document).ready(function() {
            // Counter to track the number of added forms
            let formCounter = 1;

            // Add Form button click event
            $('#addForm').on('click', function() {
                // Clone the form template
                const clonedForm = $('#formTemplate').clone().removeAttr('id').addClass('clone-form');

                // Update the names of the cloned form fields
                clonedForm.find('[name]').each(function() {
                    const originalName = $(this).attr('name');
                    const newName = originalName.replace(/\[\d+\]/, '[' + formCounter + ']');
                    $(this).attr('name', newName);
                });

                // Append the cloned form to the dynamicForm
                $('#dynamicForm').append(clonedForm);

                // Show the cloned form
                clonedForm.show();

                // Increment the form counter
                formCounter++;

                // Show Kurang Form button when there is more than one form
                if (formCounter > 1) {
                    $('#removeForm').show();
                }
            });

            // Kurang Form button click event
            $('#removeForm').on('click', function() {
                // Remove the last added form
                $('#dynamicForm .clone-form:last').remove();

                // Decrement the form counter
                formCounter--;

                // Hide Kurang Form button when there is only one form
                if (formCounter === 1) {
                    $('#removeForm').hide();
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('image-preview');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                previewContainer.style.display = 'none';
            }
        }
    </script>
    <script>
        // function previewImage(event) {
        //     var input = event.target;
        //     var preview = document.getElementById('image-preview');
        //     var container = document.getElementById('image-preview-container');

        //     if (input.files && input.files[0]) {
        //         var reader = new FileReader();

        //         reader.onload = function(e) {
        //             preview.src = e.target.result;
        //             preview.style.display = 'block';
        //         };

        //         reader.readAsDataURL(input.files[0]);

        //         // Update the file name in the label
        //         var fileName = input.files[0].name;
        //         var label = document.querySelector('.custom-file-label');
        //         label.innerText = 'Profile Picture: ' + fileName;
        //     } else {
        //         preview.style.display = 'none';
        //     }
        // }
        $(document).ready(function() {
            $('#nik-input').on('input', function() {
                if ($(this).val().length > 16) {
                    $(this).val($(this).val().slice(0, 16));
                }
            });
        });

        function onChangeSelect(url, id, name) {
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    id: id
                },
                success: function(data) {
                    var dropdown = $('#' + name);
                    console.log('Data dari ' + name + ':', data); // Tambahkan baris ini

                    dropdown.empty().removeAttr('disabled');

                    if (name == 'district') {
                        dropdown.append('<option disabled selected>Pilih Kecamatan</option>');
                    }
                    if (name == 'village') {
                        $('#village').empty();
                        $('#village').append('<option disabled selected>Pilih Desa</option>');
                    }

                    $.each(data, function(key, value) {
                        dropdown.append('<option value="' + key + '">' + value + '</option>');
                    });

                    // Set nilai 'old' untuk opsi kecamatan setelah perubahan
                    var oldCity = "{{ Auth::user()->city->id }}";
                    var oldDistrict = "{{ old('indonesia_district_id') }}";
                    var oldVillage = "{{ old('indonesia_village_id') }}";

                    if (name == 'district' && oldCity == id && oldDistrict) {
                        dropdown.val(oldDistrict);
                    }

                    if (name == 'district') {
                        dropdown.show();
                        console.log('Setting village value to old value:', oldDistrict);
                    }

                    if (name == 'village' && oldVillage) {
                        dropdown.val(oldVillage);
                    }

                    if (name == 'village') {
                        dropdown.show();

                    }

                    // if (name == 'village' && oldCity == id && oldDistrict && oldVillage) {
                    //     dropdown.val(oldVillage);
                    //     console.log('Setting village value to old value:', oldVillage);
                    // }

                }
            });
        }

        $(function() {
            var oldCity = "{{ Auth::user()->city->id }}";
            var oldDistrict = "{{ old('indonesia_district_id') }}";
            var oldVillage = "{{ old('indonesia_village_id') }}";

            if (oldCity && oldDistrict) {
                onChangeSelect("{{ route('districts') }}", oldCity, 'district');

            }

            if (oldCity && oldDistrict && oldVillage) {
                onChangeSelect("{{ route('villages') }}", oldDistrict, 'village');
            }
            // Tambahkan event listener untuk perubahan kota
            $('#city').on('change', function() {
                // console.log('Nilai dari dropdown "Kota":', $(this).val()); // Tambahkan baris ini
                onChangeSelect("{{ route('districts') }}", $(this).val(), 'district');

            });
            $('#district').on('change', function() {
                onChangeSelect("{{ route('villages') }}", $(this).val(), 'village');
            });

            var userCityId = "{{ Auth::user()->city->id }}";
            $('#volunteer').select2({
                placeholder: "Pilih Relawan",
                allowClear: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('relawan') }}?_=" + new Date().getTime(),
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            id: userCityId
                        };
                    },
                },
            });

            // Hapus opsi sebelum menambahkan yang baru
            $('#volunteer').on('select2:select', function(e) {
                var currentValue = e.params.data.id;
                console.log("ID user yang dipilih: " + e.params.data.id);
                var $this = $(this);
                $this.find('option').remove();
                $this.append('<option value="' + currentValue + '">' + e.params.data.text + '</option>');
            });

        });
    </script>
@stop
