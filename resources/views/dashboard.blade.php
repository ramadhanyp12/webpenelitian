@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold">Halo {{ Auth::user()->name }}</h1>
    <p>Anda login sebagai <strong>{{ Auth::user()->role }}</strong></p>
@endsection
