<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{ asset('output.css') }}" rel="stylesheet">
  <link href="{{ asset('main.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
</head>
<body>
  <main class="bg-[#FAFAFA] max-w-[640px] mx-auto min-h-screen relative flex flex-col has-[#CTA-nav]:pb-[120px] has-[#Bottom-nav]:pb-[120px]">


   @yield('content')

   @stack('before-scripts')
   {{-- File JS semua halaman --}}
   @stack('after-scripts')


  </main>
</body>
</html>

