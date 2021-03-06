<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Titre de la page -->
    <title>{{ $title }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('parts/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('parts/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('parts/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('parts/favicon/site.webmanifest') }}">

    <!-- Métadonnées, css et javascript -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    {{ $metaData }}
</head>
<body>
<h1 aria-level="1" role="heading" class="sr_only">
    {!! __('app.slogan') !!}
</h1>
<!-- Contenu -->
<main class="main">
    {{ $content }}
</main>

<!-- Différents scripts -->
@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-kjU+l4N0Yf4ZOJErLsIcvOU2qSb74wXpOhqTvwVx3OElZRweTnQ6d31fXEoRD1Jy" crossorigin="anonymous"></script>
<script src="{{ asset('/js/bootstrap.js') }}"></script>
{{ $script }}

<script>
    const btns = document.querySelectorAll('.btn-see');

    if(btns) {
        btns.forEach(btn => {
            let isShowed = false;
            btn.addEventListener('click', () => {
                btn.classList.toggle('show');
                if(isShowed) {
                    isShowed = false;
                    btn.previousElementSibling.type = 'password';
                } else {
                    isShowed = true;
                    btn.previousElementSibling.type = 'text';
                }
            });
        })
    }

</script>

</body>
</html>
