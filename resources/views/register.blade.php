@extends('layout')

@section('title', 'Register')

@section('content')
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <p>
            <label for="username">Username</label><br>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required>
        </p>

        <p>
            <label for="phone_number">Phonenumber</label><br>
            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" required>
        </p>

        <button type="submit">Register</button>
    </form>
@endsection
