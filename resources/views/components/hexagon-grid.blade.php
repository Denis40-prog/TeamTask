@props(['teams'])

<style>
.hex-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0;
    justify-content: center;
}

.hex-row {
    display: flex;
}

.hex {
    width: 120px;
    height: 120px;
    background: #102569;
    color: white;
    text-align: center;
    line-height: 120px;
    font-weight: bold;
    font-size: medium;
    clip-path: polygon(
        50% 0%, 93% 25%, 93% 75%,
        50% 100%, 7% 75%, 7% 25%
    );
    transition: background 0.3s ease;
    position: relative;
}

.hex:hover {
    background: #3b82f6;
}
.hex-row:nth-child(even) {
    margin-left: 60px;
}
</style>

<div class="hex-container">
    @foreach ($teams->chunk(6) as $rowIndex => $chunk)
        <div class="hex-row">
            @foreach ($chunk as $team)
                <a href="{{ $team->hexagon_route }}" class="hex">
                    {{ $team->name }}
                </a>
            @endforeach
        </div>
    @endforeach
</div>
