    <!DOCTYPE html>
    <html>

    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;800;900&display=swap"
            rel="stylesheet">

        <link href="{{ asset('css/daftar.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
            integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        


    </head>
    <style>
        header {
            position: relative;
            height: 80px !important;
            width: 100vw;
            padding-left: 50px;
            padding-top: 10px;
            padding-bottom: 10px;
            /* overflow: hidden; */
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #C51915;
            box-shadow: 0px 0.1px 8px #00000033;
        }





        /*
        .carousel img {
            width: 100%;
            height: auto;
            margin-bottom: 30px;
        } */

        .content .formulir #message-success {
            display: none;
            border: 1px solid #ced4da;
            box-shadow: 0 1px 5px rgba(0, 0, 0, .1);
            margin-top: 10px;
            padding: 15px;
            position: relative;
        }
    </style>
    @php
        $citiesController = new App\Http\Controllers\DependentDropdownController();

        // Fetch all cities for province ID 27
        $allCitiesCollection = $citiesController->citiesData(27);

        // Convert the collection to a standard PHP array
        $allCities = $allCitiesCollection->toArray();

        // Specify the desired city IDs
        $desiredCityIds = [413, 414, 416, 417, 418, 419, 415, 422];

        // Filter the cities based on the desired IDs
        $filteredCities = array_filter($allCities, function ($city) use ($desiredCityIds) {
            return in_array($city['id'], $desiredCityIds);
        });

        $districts = new App\Http\Controllers\DependentDropdownController();
        if (old('indonesia_city_id')) {
            $districts = $districts->districtsData(old('indonesia_city_id'));
        } else {
            $districts = [];
        }
        $villages = new App\Http\Controllers\DependentDropdownController();
        if (old('indonesia_district_id')) {
            $villages = $villages->villagesData(old('indonesia_district_id'));
        } else {
            $villages = [];
        }
    @endphp


    <!DOCTYPE html>

    <header>
        <div style="display: flex; align-items: center;">
            <img src="public/assets/2aab-white.png" alt="" style="height: 60px">
        </div>
        {{-- <div class="title-join">
            <h1>BERGABUNG</h1>
            <h1>MENJADI RELAWAN</h1>
        </div> --}}

    </header>

    <body>
       
            <div class="title-join">
                <h1>Ayo Bergabung<br><span style="color:#FFD833 !important">Menjadi Pendukung AAB!</span></h1>
            </div>
            <div class="title-deskripsi">
                <p> Daftarkan diri Anda menjadi pendukung AAB dan dapatkan <strong>KARTU BEF </strong>
                </p>

            </div>
    

            <div class="content">
                <div class="wrap-image">
                    <img src="public/assets/kartu_bef.png" alt="">
                </div>
                <div class="formulir">
                    <h6>Formulir Pendaftaran</h6>

                    <div id="message-success">
                        Pendaftaran Berhasil
                    </div>

                    <form id="register-form" class="register" method="POST">
                        @csrf
                        <x-adminlte-input name="name" placeholder="Nama" type="text" igroup-size="lg"
                            :value="old('name')" />
                        <x-adminlte-input name="nik" placeholder="NIK" type="number" igroup-size="lg"
                            id="nik-input" :value="old('nik')" />
                        <x-adminlte-input name="phone" placeholder="Nomor Telepon" type="number" igroup-size="lg"
                            :value="old('phone')" />
                        <x-adminlte-select id="city" name="indonesia_city_id" igroup-size="lg">
                            <option value="-" disabled selected>Kabupaten</option>
                            @foreach ($filteredCities as $city)
                                <option value="{{ $city['id'] }}"
                                    {{ old('indonesia_city_id') == $city['id'] ? 'selected' : '' }}>
                                    {{ $city['name'] }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                        <x-adminlte-select id="district" name="indonesia_district_id" igroup-size="lg">
                            <option value="-" disabled selected>Kecamatan</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district->id }}"
                                    {{ old('indonesia_district_id') == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                        <x-adminlte-select id="village" name="indonesia_village_id" igroup-size="lg">
                            <option value="-" disabled selected>Desa</option>
                            @foreach ($villages as $village)
                                <option value="{{ $village->id }}"
                                    {{ old('indonesia_village_id') == $village->id ? 'selected' : '' }}>
                                    {{ $village->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                        <x-adminlte-button class="btn-flat" type="submit" label="DAFTAR" id="button-daftar" />

                    </form>

                </div>
            </div>
            </div>
    
    </body>
    @include('admin.register.partials.footer')



    <script>
        $(document).ready(function() {
            $('#nik-input').on('input', function() {
                if ($(this).val().length > 16) {
                    $(this).val($(this).val().slice(0, 16));
                }
            });
        });

        document.getElementById('city').addEventListener('click', function() {
            // Mengubah label opsi terpilih
            var selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value === '-') {
                selectedOption.text = 'Pilih Kabupaten/Kota';
            }
        });

        // Menangani kejadian ketika dropdown dibuka
        document.getElementById('city').addEventListener('mousedown', function() {
            // Mengubah label opsi default
            var defaultOption = this.options[0];
            defaultOption.text = 'Pilih Kabupaten/Kota';
        });

        // Menangani kejadian ketika dropdown tertutup
        document.getElementById('city').addEventListener('blur', function() {
            // Mengembalikan label opsi default jika tidak ada yang dipilih
            var selectedOption = this.options[this.selectedIndex];
            if (!selectedOption || selectedOption.value === '-') {
                var defaultOption = this.options[0];
                defaultOption.text = 'Kabupaten';
            }
        });
        document.getElementById('district').addEventListener('click', function() {
            // Mengubah label opsi terpilih
            var selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value === '-') {
                selectedOption.text = 'Pilih Kecamatan';
            }
        });

        // Menangani kejadian ketika dropdown dibuka
        document.getElementById('district').addEventListener('mousedown', function() {
            // Mengubah label opsi default
            var defaultOption = this.options[0];
            defaultOption.text = 'Pilih Kecamatan';
        });

        // Menangani kejadian ketika dropdown tertutup
        document.getElementById('district').addEventListener('blur', function() {
            // Mengembalikan label opsi default jika tidak ada yang dipilih
            var selectedOption = this.options[this.selectedIndex];
            if (!selectedOption || selectedOption.value === '-') {
                var defaultOption = this.options[0];
                defaultOption.text = 'Pilih Kecamatan';
            }
        });
        document.getElementById('village').addEventListener('click', function() {
            // Mengubah label opsi terpilih
            var selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value === '-') {
                selectedOption.text = 'Pilih Desa';
            }
        });

        // Menangani kejadian ketika dropdown dibuka
        document.getElementById('village').addEventListener('mousedown', function() {
            // Mengubah label opsi default
            var defaultOption = this.options[0];
            defaultOption.text = 'Pilih Desa';
        });

        // Menangani kejadian ketika dropdown tertutup
        document.getElementById('village').addEventListener('blur', function() {
            // Mengembalikan label opsi default jika tidak ada yang dipilih
            var selectedOption = this.options[this.selectedIndex];
            if (!selectedOption || selectedOption.value === '-') {
                var defaultOption = this.options[0];
                defaultOption.text = 'Pilih Desa';
            }
        });
        $(document).ready(function() {
            $('#register-form').submit(function(e) {
                e.preventDefault();

                // Simpan elemen formulir dalam variabel
                var form = $(this);


                $.ajax({
                    url: "{{ route('register.storeResponden') }}",
                    type: "POST",
                    data: form.serialize(),
                  
                    success: function(response) {
                        if (response.success) {

                            $('#message-success').show();
                            // Reset kolom formulir
                            form[0].reset();

                            // Opsional: sembunyikan elemen formulir
                            form.find(
                                'input[name="name"], input[name="nik"], input[name="password"], input[name="phone"], input[name="appendSlot"]'
                            ).hide();
                            form.find('.invalid-feedback').hide().html('');
                            $('#password-toggle-icon').hide();
                            $('.input-group-text').hide();
                            $('.input-group-append').hide();
                            // Reset elemen select dan sembunyikan
                            form.find('#city, #district,  #village').val('-').hide();
                            form.find('#button-daftar').hide();
                          

                            // Anda juga dapat melakukan redirect atau tindakan lain di sini
                        } else {
                            form.off('submit'); // Remove the submit event listener
                            form.submit(); // Submit the form traditionally
                        }
                    },
                    error: function(xhr, status, error) {
                        // Tangani kesalahan yang mungkin terjadi saat mengirim permintaan AJAX
                        console.error(xhr.responseText);

                        // If there's an error, you might want to handle it here
                    },
                    complete: function() {
                        // Whether success or error, if you want to submit the form without AJAX,
                        // you can do it here.
                        // form.submit(); // Uncomment this line if you want to submit the form traditionally
                    }
                });


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
                    dropdown.empty().removeAttr('disabled');
                    console.log(data)
                    if (name == 'district') {
                        $('#' + name).append('<option disabled selected>Kecamatan</option>');
                    }
                    if (name == 'village') {
                        $('#village').empty();
                        $('#village').append('<option disabled selected>Desa</option>');
                    }

                    $.each(data, function(key, value) {
                        $('#' + name).append('<option value="' + key + '">' + value + '</option>');
                    });
                    // Set nilai 'old' untuk opsi kecamatan setelah perubahan
                    var oldCity = "{{ old('indonesia_city_id') }}";
                    var oldDistrict = "{{ old('indonesia_district_id') }}";
                    var oldVillage = "{{ old('indonesia_village_id') }}";

                    if (name == 'district' && oldCity == id && oldDistrict) {
                        $('#' + name).val(oldDistrict);
                    }

                    if (name == 'district') {
                        $('#district').show();
                    }
                    if (name == 'village' && oldVillage) {
                        dropdown.val(oldVillage);
                    }

                    if (name == 'village') {
                        dropdown.show();

                    }
                }
            });
        }
        $(function() {
            // Set nilai 'old' untuk opsi kecamatan saat halaman dimuat
            var oldCity = "{{ old('indonesia_city_id') }}";
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
                onChangeSelect("{{ route('districts') }}", $(this).val(), 'district');
            });
            $('#district').on('change', function() {
                onChangeSelect("{{ route('villages') }}", $(this).val(), 'village');
            });
        });
    </script>
