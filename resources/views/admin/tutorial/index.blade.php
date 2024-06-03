    <html>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <head>
        <link href="{{ asset('public/css/tutorial.css') }}" rel="stylesheet" type="text/css" />
    </head>

    <!--<div class="description">-->
    <!--    <h6 style="width:50%"><strong>Putar</strong> video dibawah ini dan <strong>jawablah</strong>-->
    <!--        pertanyaan yang-->
    <!--        berada-->
    <!--        di akhir video dengan benar untuk bergabung menjadi relawan !!!</h6>-->
    <!--    <h6 style="font-style: italic; text-align:right">Putar layar HP Anda <img src="public/assets/ic_landscape.png"-->
    <!--            style="width: 24px;height:24px" alt="" srcset=""> <br> (mode landscape)-->

    <!--    </h6>-->    


    <!--</div>-->
      <div class="tutorial-join">
        <h1 style="color:#FFD833 !important;">Selamat!</h1>
    </div>
    <h4 style="text-align:center; color:white;font-weight:bold;padding-left:10px;padding-right:10px"><strong
            style="color: white">Sedikit lagi</strong> kamu
        akan bergabung
        menjadi
        <strong style="color: #FFCE00">Relawan!</strong>
    </h4>
   
    <div class="content-tutorial">
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
         <div class="information">
            <p>
                Untuk memudahkan anda mengkampanyekan Ahmad Abdy Baramuli, silahkan tonton video mengenai<strong>Profil
                    Singkat</strong> serta<strong>Visi Misi Ahmad Abdy Baramuli</strong> dan<strong>Tutorial Penggunaan
                    Aplikasi</strong> dibawah ini!
            </p>
        </div>
       <div class="intruksi"  style="padding-bottom: 10px !important; margin-bottom: 10px !important">
            <h4> <strong>Putar video</strong> dibawah ini dan
                <strong style="color: #B30A06">jawab pertanyaan dengan benar!</strong>
            </h4>
        </div>

 
        <div class="video-container">
            <video id="videoPlayer" controls>
                <source src="{{ asset('public/assets/Tutorial.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>


            <div class="question-overlay" id="questionOverlay">
                <form id="question-form" method="POST">
                    <div class="question">
                        {{-- @foreach ($questions as $q) --}}
                        <p style="text-align:center;font-weight:bold">Jawab pertanyaan di bawah ini</p>
                        <p id="questionText"></p>
                        <ul id="answerList"></ul>
                    </div>
                    <button id="answerButton" class="answerButton">Selanjutnya</button>
                    <button id="previousButton" class="previousButton" style="display: none">Sebelumnya</button>

                </form>
            </div>
            <div class="alert-answer" id="alertAnswer">
                <form id="alert-form" method="POST">
                    <div class="alert">
                        {{-- @foreach ($questions as $q) --}}

                        <p id="alertText"></p>
                        <p>Tonton Ulang Video dan Jawab Kembali</p>

                    </div>
                    {{-- @endforeach  --}}
                    <button id="restartButton" class="restartButton"> <img src="public/assets/ic_youtube.png"
                            alt="YouTube Logo" width="32px" height="32px"></button>

                </form>
            </div>
            <div class="alert-success-answer" id="alertSuccess" style="  display: none;">
                <div class="alertSuccess-answer">
                    <p>Tes Telah Selesai dan Semua Jawaban Anda Benar</p>
                    <p><strong style="color: #B30A06">Silahkan Bergabung</strong> Ke dalam Group WhatsApp</p>
                    <p>dengan klik link berikut</p>
                    <a href="{{ url('https://chat.whatsapp.com/KYTrZlEAeUrLShVAllZf8C') }}" target="_blank"
                        style="text-decoration: underline !important; color: black !important; cursor: pointer !important; font-weight:bold !important">
                        https://chat.whatsapp.com/KYTrZlEAeUrLShVAllZf8C
                    </a>
                </div>

            </div>
        </div>

    </div>
    <script>
        var video = document.getElementById("videoPlayer");

        document.addEventListener('DOMContentLoaded', function() {

            var video = document.getElementById('videoPlayer');
            var questionOverlay = document.getElementById('questionOverlay');
            var answerButton = document.getElementById('answerButton');
            // var p1Value = document.getElementById('p1').value;
            // var user = document.getElementById('user').value;
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var doneAnswer = false;
            var formSubmitted = false;
            var currentQuestionIndex = 0;
            var Answers = [];
            var Users = [];
            var Questions = [];
            var questions = {!! isset($questions) ? json_encode($questions) : '[]' !!};
            var part1EndTime = 207; // seconds
            var part2StartTime = 6;
            var part2EndTime = 10;

            var lastKnownTime = 0;
            var part1 = false;

            // Deklarasikan variabel untuk menyimpan status pemilihan jawaban

            function startTime(time) {
                video.currentTime = time;

                // Add an event listener for the 'loadedmetadata' event to ensure video metadata is loaded
                video.addEventListener('loadedmetadata', function onLoadedMetadata() {
                    // Remove the 'loadedmetadata' event listener
                    video.removeEventListener('loadedmetadata', onLoadedMetadata);

                    // Add an event listener for the 'play' event to pause the video when part1 is true
                    video.addEventListener('play', function onPlay() {
                        if (part1 && video.currentTime >= part1EndTime) {
                            video.pause();
                            // Remove the 'play' event listener to prevent interference
                            video.removeEventListener('play', onPlay);
                        }
                    });

                    // Play the video
                    video.play().catch(function(error) {
                        console.error('Error playing video:', error);
                    });
                });
            }




            // Atur event listener untuk event loadedmetadata video

            // Fungsi untuk memeriksa apakah elemen dalam mode fullscreen
            function isElementFullScreen(element) {
                return !!(document.fullscreenElement || document.webkitFullscreenElement || document
                    .mozFullScreenElement || document.msFullscreenElement);
            }

            // Fungsi untuk memulai atau keluar dari mode fullscreen
            function toggleFullscreen(element) {
                if (isElementFullScreen(element)) {
                    exitFullscreen(element);
                } else {
                    enterFullscreen(element);
                }
            }

            // Fungsi untuk memulai mode fullscreen pada elemen tertentu
            function enterFullscreen(element) {
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }
            }

            // Fungsi untuk keluar dari mode fullscreen pada elemen tertentu
            function exitFullscreen(element) {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
            if (video.readyState >= 2) {
                video.currentTime = 10;
            }


            function displayQuestion() {
                // console.log('Current Index:', currentQuestionIndex);
                // console.log('All Questions:', questions);

                var currentQuestion = questions[currentQuestionIndex];

                // Periksa apakah currentQuestion terdefinisi
                if (currentQuestion) {
                    // console.log('Current Question:', currentQuestion);

                    // Periksa apakah currentQuestion.answers terdefinisi
                    if (currentQuestion.answer !== undefined) {
                        try {
                            // Mengonversi string JSON menjadi array
                            var answersArray = JSON.parse(currentQuestion.answer);

                            // Pastikan bahwa answersArray adalah array
                            if (Array.isArray(answersArray)) {
                                // Menampilkan pertanyaan
                                document.getElementById('questionText').innerText = currentQuestion.position +
                                    '. ' + currentQuestion.question;


                                // Menampilkan jawaban
                                // Cek apakah ini pertanyaan nomor 3


                                var answerList = document.getElementById('answerList');
                                answerList.innerHTML = '';
                                // Cek apakah ini pertanyaan nomor 3
                                if (currentQuestion.position === 3 && Answers[1] !== null) {
                                    // Jika ini pertanyaan nomor 3, tambahkan gambar sebelum pertanyaan
                                    var imageElement = document.createElement('img');
                                    imageElement.src = 'public/assets/question3.png'; // Ganti dengan path gambar Anda
                                    imageElement.alt = 'Image Description'; // Ganti dengan deskripsi gambar Anda
                                    imageElement.style.width = '300px'; // Sesuaikan lebar gambar sesuai kebutuhan
                                     imageElement.style.height = 'auto'; // Sesuaikan tinggi gambar sesuai kebutuhan
                                    imageElement.style.borderRadius = 0;
                                    imageElement.id = 'imgQuestion'
                                    answerList.appendChild(imageElement);
                                }
                                // if (currentQuestion.position === 1 && Answers[1] !== null) {
                                //     var question = document.createElement('div');
                                //     question.className = 'question';
                                //     question.style.paddingLeft = '20px';
                                //     question.style.paddingBottom = '20px';
                                //     question.style.display = 'flex';
                                //     question.style.flexDirection = 'row';

                                //     var optionsGroup1 = ['a. Klik "Daftarkan NIK Baru"',
                                //         'b. Izinkan akses Lokasi & Microphone', 'c. Masukkan NIK',
                                //         'd. Kirim Hasil Wawancara', 'e. Masukkan No HP',
                                //         'f. Foto Bersama Pemilih',
                                //     ];
                                //     var optionsGroup2 = ['g. Isi Biodata', 'h. Login',
                                //         'i. Mulai Wawancara & Jawab Pertanyaan', 'j. Input 4 Digit OTP',
                                //         'k. Pilih Verifikasi OTP (SMS/Whatsapp)', 'l. Klik “Mulai DTDC”'
                                //     ]

                                //     // Fungsi untuk membuat elemen div dari kelompok opsi
                                //     function createOptionsDiv(options) {
                                //         var optionsDiv = document.createElement('div');
                                //         optionsDiv.style.flex =
                                //             '1'; // Setiap kolom akan mendapatkan sebagian dari lebar yang sama

                                //         // Menambahkan jarak antar baris
                                //         optionsDiv.style.marginBottom = '10px';

                                //         options.forEach(function(option) {
                                //             var optionElement = document.createElement('div');
                                //             optionElement.innerText = option;
                                //             optionsDiv.appendChild(optionElement);
                                //         });
                                //         return optionsDiv;
                                //     }

                                //     // Membuat elemen div untuk setiap kelompok opsi
                                //     var optionsDiv1 = createOptionsDiv(optionsGroup1);
                                //     var optionsDiv2 = createOptionsDiv(optionsGroup2);

                                //     // Menambahkan kedua kelompok opsi ke dalam elemen pertanyaan
                                //     question.appendChild(optionsDiv1);
                                //     question.appendChild(optionsDiv2);
                                    
                                //       // Menambahkan tulisan 'Urutan penggunaan aplikasi'
                                //     var usageOrderLabel = document.createElement('div');
                                //     usageOrderLabel.className = 'question';
                                //     usageOrderLabel.style.paddingBottom = '20px';
                                //     usageOrderLabel.innerText = 'Urutan penggunaan yang benar adalah';

                                //     // Menambahkan elemen pertanyaan ke dalam daftar jawaban
                                //     answerList.appendChild(question);
                                //     answerList.appendChild(usageOrderLabel);
                                // }

                                answersArray.forEach(function(answer) {
                                    var li = document.createElement('li');
                                    li.className = 'answer-option';

                                    var label = document.createElement('label');
                                    label.appendChild(document.createTextNode(answer));

                                    var radio = document.createElement('input');
                                    radio.type = 'radio';
                                    radio.name = 'answer';
                                    radio.value = answer;
                                    li.appendChild(radio);
                                    li.appendChild(label);
                                    // console.log(radio.checked)
                                    // console.log(answerButton.style.backgroundColor)
                                    answerButton.style.backgroundColor =
                                        '#EDEDED'; // Change this to your desired #CCCCCC color

                                    radio.addEventListener('change', function() {
                                        // Check if the radio button is checked
                                        if (radio.checked) {
                                            // Change the background color of the answer button to red when checked
                                            answerButton.style.backgroundColor =
                                                '#FFD600'; // Change this to your desired color
                                        } else {
                                            // Change the background color back to #CCCCCC when unchecked
                                            answerButton.style.backgroundColor =
                                                '#EDEDED'; // Change this to your desired color
                                        }
                                    });


                                    answerList.appendChild(li);
                                });



                            } else {
                                console.error('Invalid answers property in currentQuestion:', currentQuestion);
                            }
                        } catch (error) {
                            console.error('Error parsing JSON in currentQuestion:', currentQuestion);
                        }
                    } else {
                        console.error('answers property is undefined in currentQuestion:', currentQuestion);
                    }
                } else {
                    console.error('currentQuestion is undefined at index:', currentQuestionIndex);
                }
                var previousButton = document.getElementById('previousButton');
                // console.log(previousButton)
                if (currentQuestion.position > 1) {
                    previousButton.style.display = 'block';
                } else {
                    previousButton.style.display = 'none';
                }
            }

            $('#previousButton').on('click', function() {
                if (currentQuestionIndex > 0) {
                    var selectedValue = $('input[name="answer"]:checked').val();
                    Answers[currentQuestionIndex] = selectedValue;

                    // Move to the previous question
                    currentQuestionIndex--;

                    // Display the previous question
                    displayQuestion();

                    // Restore the selected answer for the current question
                    var previousAnswer = Answers[currentQuestionIndex];
                    if (previousAnswer !== undefined) {
                        $('input[name="answer"]').filter('[value="' + previousAnswer + '"]').prop('checked',
                            true);
                    } else {
                        $('input[name="answer"]').prop('checked', false);
                    }

                }

            });

            function checkAndDisplayQuestion() {
                var currentQuestion = questions[currentQuestionIndex];


                if (!doneAnswer && !formSubmitted && currentQuestion) {
                    var currentTime = video.currentTime;

                    console.log('Position:', currentQuestion.position);
                    console.log('Current Time:', currentTime);
                    if (currentTime >= part1EndTime && part1 != true) {
                        // part1 = true
                        questionOverlay.style.display = 'block';
                        // video.controls = false;
                        exitFullscreen(video);
                        displayQuestion();
                        video.pause();


                    }
                    
                }
            }
          



            video.addEventListener('timeupdate', checkAndDisplayQuestion);



            var incorrectQuestionIndices = [];

            function resetVideoAndQuestions() {
                console.log('nilai part1' + part1)
                video.currentTime = 0;
                doneAnswer = false;
                formSubmitted = false;
                alertAnswer.style.display = 'none';
                currentQuestionIndex = 0;
                Answers = [];
                Users = [];
                Questions = [];
                incorrectQuestionIndices = [];
                video.play();

            }

            function sendAnswerToServer(Questions, Answers, Users) {
                // Ambil CSRF token dari meta tag
                var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content');
                console.log('Answer:', Answers);
                console.log('User:', Users);
                // Kirim data ke server menggunakan Ajax
                $.ajax({
                    url: "{{ route('tutorial.store') }}",
                    type: 'POST',
                    data: {
                        question_id: Questions,
                        answer: Answers,
                        user: Users
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        var videoPlayer = document.getElementById('videoPlayer');
                        videoPlayer.controls = false;
                        // alert('Jawaban berhasil disimpan.');
                        console.log(response)
                        video.pause();

                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan saat menyimpan jawaban.');
                        console.error(xhr.responseText);
                    }
                });

            }

            // Menyembunyikan pertanyaan saat pengguna menjawab dan melanjutkan video
            $('#answerButton').on('click', function() {
                // displayQuestion();


                // button.addEventListener('click', function() {
                var selectedValue = $('input[name="answer"]:checked').val();
                if (selectedValue) {
                    questionOverlay.style.display = 'none'; // Menyembunyikan pertanyaan
                    // doneAnswer = true;

                    // Anda dapat memeriksa jawaban dan melakukan sesuatu dengan jawaban ini di sini
                    var answer = selectedValue;
                    var correctAnswer = questions[currentQuestionIndex].correct_answer;
                    Answers[currentQuestionIndex] = answer;
                    // console.log(questions)
                    console.log('CSRF Token:', csrfToken);
                    console.log('Question ID:', questions[currentQuestionIndex].id);
                    console.log('Jawaban Benar:', questions[currentQuestionIndex]
                        .correct_answer);

                    console.log('User:', {{ Auth::user()->id }});
                    Questions.push(questions[currentQuestionIndex].id);

                    Users.push({{ Auth::user()->id }});
                    console.log(Answers)
                    // Cek apakah jawaban untuk pertanyaan ini sudah ada
                    if (Answers[currentQuestionIndex] !== undefined) {
                        // Jika sudah ada, perbarui jawaban
                        Answers[currentQuestionIndex] = answer;
                    } else {
                        // Jika belum, tambahkan jawaban baru
                        Answers.push(answer);
                    }
                    console.log('Answer:', answer);
                    // Check if the selected answer is correct
                    if (answer === correctAnswer) {
                        console.log('Answer is correct');
                        // Perform actions for correct answer (e.g., send data to the server)
                    } else {
                        console.log('Answer is incorrect');
                        var incorrectQuestionNumber = currentQuestionIndex + 1;
                        console.log('Incorrect Question Number:', incorrectQuestionNumber);

                        // Store the index of the incorrect question
                        incorrectQuestionIndices.push(incorrectQuestionNumber);


                        // Perform actions for incorrect answer (e.g., store information about incorrect answer)
                    }

                    currentQuestionIndex++;

                    var isQuestion2Answered = false;
                    // Tampilkan pertanyaan baru
                    // console.log(currentQuestionIndex < questions.length)
                    // console.log(currentQuestionIndex)
                    if (currentQuestionIndex < questions.length) {
                     
                        questionOverlay.style.display = 'block'; // Menampilkan pertanyaan
                        displayQuestion();
                        // Restore the selected answer for the current question
                        var nextAnswer = Answers[currentQuestionIndex];
                        if (nextAnswer !== undefined) {
                            $('input[name="answer"]').filter('[value="' + nextAnswer + '"]').prop('checked',
                                true);
                        } else {
                            $('input[name="answer"]').prop('checked', false);
                        }


                        //     video.pause();
                        // }
                    } else {
                        // All questions answered, show alert here


                        if (incorrectQuestionIndices.length > 0) {
                            // part1 = true;
                            // console.log(part1)
                            formSubmitted = false;
                            alertAnswer.style.display = 'flex';
                            const uniqueIncorrectQuestions = [...new Set(
                                incorrectQuestionIndices)]; // Menghapus duplikasi

                            const incorrectQuestionsText = uniqueIncorrectQuestions.map(index => {
                                return 'Nomor ' + index + ': ' + questions[index - 1]
                                    .correct_answer;
                            }).join(', ');

                            document.getElementById('alertText').innerHTML =
                                'Jawaban Nomor : ' + (uniqueIncorrectQuestions.length > 0 ?
                                    uniqueIncorrectQuestions
                                    .join(
                                        ', ') + '<strong> Salah </strong>' : '');
                            formSubmitted = false;
                            sendAnswerToServer(Questions, Answers, Users);
                            // if (numericIndices.length > 1) {

                            // }

                        } else {
                            formSubmitted = false;
                            sendAnswerToServer(Questions, Answers, Users);
                            alertSuccess.style.display = 'flex';
                            video.pause();
                        }


                    }


                }


            });

            restartButton.addEventListener('click', function(e) {
                e.preventDefault();
                resetVideoAndQuestions();
                checkAndDisplayQuestion(); // Call the function when restarting
            });

          
        })
        $('#question-form').on('submit', function(e) {
            e.preventDefault();
        });
        $('#alert-form').on('submit', function(e) {
            e.preventDefault();
            resetVideoAndQuestions();
            checkAndDisplayQuestion(); // Call the function after form submission
        });
    </script>
