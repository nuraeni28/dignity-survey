    <!DOCTYPE html>
    <html>

    <head>
        <link href="{{ asset('public/css/daftar.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
            integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
     <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;900&display=swap" rel="stylesheet">



    </head>
    <style>
        header {
            position: relative;
            height: 80px !important;
            width: 100vw;
            padding-left: 50px;
            padding-top: 10px;
            padding-bottom: 10px;
            /*overflow: hidden;*/
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #C51915;
            box-shadow: 0px 0.1px 8px #00000033;
        }



        #district {
            display: none;
        }
          .carousel {
            display: none;
        }


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
        $desiredCityIds = [413,414, 416, 417, 418, 419, 415, 422];

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
       
          @if (auth()->check() == null)
            <div class="title-join">
              <h1>Ayo Bergabung<br><span style="color:#FFD833 !important">Menjadi Relawan!</span></h1>
            </div>
            <div class="title-deskripsi">
                <p> Daftarkan diri Anda menjadi relawan dan dapatkan <strong>keuntungan sebanyak-banyaknya </strong>.
                    Hanya
                    dengan
                    <strong style="color: #FFCE00"> mengumpulkan wawancara menggunakan smartphone</strong> Anda!
                </p>

            </div>
        @endif
        @if (auth()->check() && auth()->user()->email_verified_at)
            {{-- @php
                dd(auth()->user());
            @endphp --}}
            @include('admin.tutorial.index')
        @else
            <div class="content">
                <div class="wrap-image">
                   <div class="carousel">
                        <img src="public/assets/web_1.png" alt="">
                    </div>
                    <div class="carousel">
                        <img src="public/assets/web_2.jpg" alt="">
                    </div>
                    <div class="carousel">
                        <img src="public/assets/web_3.jpg" alt="">
                    </div>
                    <div class="carousel">
                        <img src="public/assets/web_4.jpg" alt="">
                    </div>
                    <div class="carousel">
                        <img src="public/assets/web_5.png" alt="">
                    </div>


                </div>
                <div class="formulir">
                    <h6>Formulir Pendaftaran</h6>


                    <!--<div id="message-success">-->
                    <!--    Silakan periksa kotak masuk email Anda untuk mengonfirmasi pendaftaran ini!-->
                    <!--</div>-->

                    <form id="register-form" class="register" method="POST">
                        @csrf
                        <x-adminlte-input name="name" placeholder="Nama" type="text" igroup-size="lg"
                            :value="old('name')" />
                        <x-adminlte-input name="email" placeholder="Alamat Email" type="email" igroup-size="lg"
                            :value="old('email')" />
                      
                        <x-adminlte-input name="phone" placeholder="Nomor Telepon" type="number" igroup-size="lg"
                            :value="old('phone')" />
                        <x-adminlte-select name="recomended_by" id="recomended" igroup-size="lg"
                            class="form-control
                        ">
                            <option value="-" disabled selected data-subtext="Pilih">Sumber Informasi Perekrutan
                            </option>
                            <option value="sosial media">Sosial Media (FB/IG)</option>
                            <option value="karyawan benur kita">Karyawan Benur Kita</option>
                            <option value="yayasan baramuli">Guru/Siswa Yayasan Baramuli</option>
                            <option value="relawan">Relawan</option>
                            <option value="tsurvey">Tsurvey</option>
                        </x-adminlte-select>
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
                        <x-adminlte-button class="btn-flat" type="submit" label="DAFTAR" id="button-daftar" />

                    </form>

                </div>
            </div>
            </div>
        @endif


    </body>

    @include('admin.register.partials.footer')

    </html>

 <script>
        document.addEventListener("DOMContentLoaded", function() {
            var currentImageIndex = 0;
            var imageContainers = document.querySelectorAll('.carousel');

            function showNextImage() {
                imageContainers[currentImageIndex].style.display = 'none';
                currentImageIndex = (currentImageIndex + 1) % imageContainers.length;
                imageContainers[currentImageIndex].style.display = 'block';
            }

            setInterval(showNextImage, 3000); // Ganti angka 3000 dengan interval yang diinginkan (dalam milidetik)
        });
    </script>
    <script>
    
        document.getElementById('recomended').addEventListener('click', function() {
            // Mengubah label opsi terpilih
            var selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value === '-') {
                selectedOption.text = 'Pilih';
            }
        });

        // Menangani kejadian ketika dropdown dibuka
        document.getElementById('recomended').addEventListener('mousedown', function() {
            // Mengubah label opsi default
            var defaultOption = this.options[0];
            defaultOption.text = 'Pilih';
        });

        // Menangani kejadian ketika dropdown tertutup
        document.getElementById('recomended').addEventListener('blur', function() {
            // Mengembalikan label opsi default jika tidak ada yang dipilih
            var selectedOption = this.options[this.selectedIndex];
            if (!selectedOption || selectedOption.value === '-') {
                var defaultOption = this.options[0];
                defaultOption.text = 'Sumber Informasi Perekrutan';
            }
        });
        document.getElementById('city').addEventListener('click', function() {
            // Mengubah label opsi terpilih
            var selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value === '-') {
                selectedOption.text = 'Pilih';
            }
        });

        // Menangani kejadian ketika dropdown dibuka
        document.getElementById('city').addEventListener('mousedown', function() {
            // Mengubah label opsi default
            var defaultOption = this.options[0];
            defaultOption.text = 'Pilih';
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
                selectedOption.text = 'Pilih';
            }
        });

        // Menangani kejadian ketika dropdown dibuka
        document.getElementById('district').addEventListener('mousedown', function() {
            // Mengubah label opsi default
            var defaultOption = this.options[0];
            defaultOption.text = 'Pilih';
        });

        // Menangani kejadian ketika dropdown tertutup
        document.getElementById('district').addEventListener('blur', function() {
            // Mengembalikan label opsi default jika tidak ada yang dipilih
            var selectedOption = this.options[this.selectedIndex];
            if (!selectedOption || selectedOption.value === '-') {
                var defaultOption = this.options[0];
                defaultOption.text = 'Kecamatan';
            }
        });
        $(document).ready(function() {
            $('#register-form').submit(function(e) {
                e.preventDefault();

                // Simpan elemen formulir dalam variabel
                var form = $(this);


                $.ajax({
                    url: "{{ route('register.store') }}",
                    type: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {

                            // $('#message-success').show();
                              window.location.href = '/sukses-daftar';

                            // Reset kolom formulir
                            form[0].reset();

                            // Opsional: sembunyikan elemen formulir
                            form.find(
                                'input[name="name"], input[name="email"], input[name="password"], input[name="phone"], input[name="appendSlot"]'
                            ).hide();
                            form.find('.invalid-feedback').hide().html('');
                            $('#password-toggle-icon').hide();
                            $('.input-group-text').hide();
                            $('.input-group-append').hide();
                            // Reset elemen select dan sembunyikan
                            form.find('#city, #district,  #recomended').val('-').hide();
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
                    $('#' + name).empty();
                    $('#' + name).removeAttr('disabled');
                    if (name == 'district') {
                        $('#' + name).append('<option disabled selected>Kecamatan</option>');
                    }

                    $.each(data, function(key, value) {
                        $('#' + name).append('<option value="' + key + '">' + value + '</option>');
                    });
                    // Set nilai 'old' untuk opsi kecamatan setelah perubahan
                    var oldCity = "{{ old('indonesia_city_id') }}";
                    var oldDistrict = "{{ old('indonesia_district_id') }}";

                    if (name == 'district' && oldCity == id && oldDistrict) {
                        $('#' + name).val(oldDistrict);
                    }

                    if (name == 'district') {
                        $('#district').show();
                    }
                }
            });
        }
        $(function() {
            // Set nilai 'old' untuk opsi kecamatan saat halaman dimuat
            var oldCity = "{{ old('indonesia_city_id') }}";
            var oldDistrict = "{{ old('indonesia_district_id') }}";

            if (oldCity && oldDistrict) {
                onChangeSelect("{{ route('districts') }}", oldCity, 'district');
            }

            // Tambahkan event listener untuk perubahan kota
            $('#city').on('change', function() {
                onChangeSelect("{{ route('districts') }}", $(this).val(), 'district');
            });

        });
    </script>
