@extends('layout')

@section('title', 'Register')

@section('content')
    @if ($errors->any())
        <div class="alert alert--error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="field">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required>
        </div>

        <div class="field">
            <label for="phone_number">Phone number</label>
            <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}" required>
        </div>

        <button type="submit" class="btn btn--primary">Get my lucky link</button>
    </form>
@endsection
