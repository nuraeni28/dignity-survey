  <!DOCTYPE html>
  <html>

  <head>
        <!--<link rel="stylesheet" href="{{ asset('public/css/daftar.css') }}">-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
            integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
     <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;900&display=swap" rel="stylesheet">


  </head>
  <!-- Meta Pixel Code -->
  <script>
      ! function(f, b, e, v, n, t, s) {
          if (f.fbq) return;
          n = f.fbq = function() {
              n.callMethod ?
                  n.callMethod.apply(n, arguments) : n.queue.push(arguments)
          };
          if (!f._fbq) f._fbq = n;
          n.push = n;
          n.loaded = !0;
          n.version = '2.0';
          n.queue = [];
          t = b.createElement(e);
          t.async = !0;
          t.src = v;
          s = b.getElementsByTagName(e)[0];
          s.parentNode.insertBefore(t, s)
      }(window, document, 'script',
          'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '272871322438664');
      fbq('track', 'PageView');
  </script>
  <noscript><img height="1" width="1" style="display:none"
          src="https://www.facebook.com/tr?id=272871322438664&ev=PageView&noscript=1" /></noscript>
  <!-- End Meta Pixel Code -->
  <style>
  
body {
    display: flex !important;
    flex-direction: column !important;
    align-items: center; /* Tengahkan vertikal */
    justify-content: center; /* Tengahkan horizontal */
    max-height: 100vh !important;
    margin: 30 !important;
    padding: 30 !important;
    overflow-x: hidden;
    background-color: white !important;
    font-family: 'Montserrat' !important;
    font-weight: 900;
}

section {
    margin-left: 50px;
}
    .formulir {
        flex: 1;

        /* Formulir akan memanfaatkan sisa lebar layar yang tersedia */
        /* max-width: 500px;  */
        /* Atur lebar maksimum formulir sesuai kebutuhan */
        width: 100%;
        margin-left: 10px !important;
        margin-right: 10px !important;
        margin-top:50px !important;
        /* Jarak antara gambar dan formulir */
        padding-left: 0 !important;
        text-align: center; /* Tengahkan teks di dalam formulir */
    }
    .formulir h6 {
    color: #B90B07;
    font-size: 24px !important;
    text-align: center;
    font-weight: bold !important;

}
      .formulir #message-success {
          align-items: center; /* Tengahkan vertikal */
    justify-content: center; /* Tengahkan horizontal */
    display: inline-block; /* Ganti display menjadi inline-block */
    border: 1px solid #ced4da;
    box-shadow: 0 1px 5px rgba(0, 0, 0, .1);
    margin-top: 10px;
    padding: 15px;
    position: relative;
    text-align: center; 
    color: black !important;
        }
  </style>
<body>
<div class="formulir">
    <h6>Terima Kasih</h6>
     <div id="message-success">
      Silakan periksa kotak masuk email Anda untuk mengonfirmasi pendaftaran
      ini!
  </div>
  </div>
</body>
 

  </html>
