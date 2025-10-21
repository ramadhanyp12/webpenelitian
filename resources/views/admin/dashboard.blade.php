@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

  <div class="bg-white shadow rounded p-6">
    <p>Selamat datang, {{ Auth::user()->name }}! 🎉</p>
    <p class="mt-2 text-gray-600">Gunakan sidebar untuk mengelola tiket dan approvals.</p>
  </div>
@endsection
