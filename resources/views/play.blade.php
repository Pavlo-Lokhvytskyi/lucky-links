@extends('layout')

@section('title', 'Page A')

@section('content')
    <div class="link-box">
        <a href="{{ $link->url() }}">{{ $link->url() }}</a>
    </div>
    <p class="meta">Valid until {{ $link->expires_at->toDateTimeString() }}</p>

    @if ($draw = session('draw'))
        <div class="result {{ $draw['result'] === 'Win' ? 'result--win' : 'result--lose' }}">
            <p class="result__number">{{ $draw['number'] }}</p>
            <span class="badge {{ $draw['result'] === 'Win' ? 'badge--win' : 'badge--lose' }}">
                {{ $draw['result'] }}
            </span>
            @if ($draw['result'] === 'Win')
                <p class="result__payout">Payout: {{ $draw['payout'] }}</p>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('play.lucky', $link->token) }}">
        @csrf
        <button type="submit" class="btn btn--primary">🍀 I'm Feeling Lucky</button>
    </form>

    <form method="GET" action="{{ route('play.history', $link->token) }}">
        <button type="submit" class="btn btn--secondary">View history</button>
    </form>

    <form method="GET" action="{{ route('play.achievements', $link->token) }}">
        <button type="submit" class="btn btn--secondary">🏆 Achievements</button>
    </form>

    <hr class="divider">

    <form method="POST" action="{{ route('play.regenerate', $link->token) }}">
        @csrf
        <button type="submit" class="btn btn--ghost">Regenerate link</button>
    </form>

    <form method="POST" action="{{ route('play.deactivate', $link->token) }}">
        @csrf
        <button type="submit" class="btn btn--danger">Deactivate link</button>
    </form>
@endsection
