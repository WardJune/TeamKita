<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <form action="{{ route('coba') }}" method="POST">
        @csrf
        @foreach ($member as $mem)
            <small>{{ $mem }}</small>
            <input type="hidden" name="member[]" value={{ $mem }}>
        @endforeach
        <button type="submit">Oke</button>
    </form>

</body>

</html>
