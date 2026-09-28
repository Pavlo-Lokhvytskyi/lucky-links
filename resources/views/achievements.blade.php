@extends('layout')

@section('title', 'Achievements')

@section('content')
    @foreach (['Spins' => $spins, 'Turnover' => $turnover] as $label => $group)
        <h3>{{ $label }}</h3>

        @foreach ($group as $achievement)
            <div class="achievement">
                <div class="achievement__head">
                    <span>Level {{ $achievement->level }}</span>
                    @if ($achievement->status->value === 'completed')
                        <span class="badge badge--win">Completed</span>
                    @else
                        <span class="badge badge--progress">In progress</span>
                    @endif
                </div>
                <div class="progress-track">
                    <div
                        class="progress-fill"
                        style="width: {{ min(100, (int) round($achievement->progress / $achievement->level * 100)) }}%"
                    ></div>
                </div>
                <p class="meta">{{ $achievement->progress }} / {{ $achievement->level }}</p>
            </div>
        @endforeach
    @endforeach

    <a class="back-link" href="{{ $link->url() }}">&larr; Back</a>
@endsection
