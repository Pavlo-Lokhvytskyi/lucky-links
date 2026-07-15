@extends('layout')

@section('title', 'Page A')

@section('content')
    <p>
        Your link: <a href="{{ $link->url() }}">{{ $link->url() }}</a><br>
        Valid until: {{ $link->expires_at->toDateTimeString() }}
    </p>

    @if ($draw = session('draw'))
        <p>
            Number: {{ $draw['number'] }}<br>
            Result: {{ $draw['result'] }}<br>
            Win amount: {{ $draw['payout'] }}
        </p>
    @endif

    <form method="POST" action="{{ route('play.lucky', $link->token) }}">
        @csrf
        <button type="submit">Imfeelinglucky</button>
    </form>

    <form method="GET" action="{{ route('play.history', $link->token) }}">
        <button type="submit">History</button>
    </form>

    <form method="POST" action="{{ route('play.regenerate', $link->token) }}">
        @csrf
        <button type="submit">Regenerate link</button>
    </form>

    <form method="POST" action="{{ route('play.deactivate', $link->token) }}">
        @csrf
        <button type="submit">Deactivate link</button>
    </form>
@endsection
