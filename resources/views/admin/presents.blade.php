@extends('mma::layouts.main')

@section('content')

    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="/{{ config('laravel-admin.root_url') }}">Dashboard</a></li>
            <li><a href="{{ config('laravel-admin.root_url') }}/utc/presents/{{ $promo->slug() }}">Presents - {{ $promo->title() }}</a></li>
        </ul>
    </nav>

    @if($presents->count() > 0)

        <div class="tile is-ancestor">

            @foreach($presents as $index => $present)

                @if( $index > 0 && $index % 4 === 0 )
        </div>
        <div class="tile is-ancestor">
            @endif

            <f-post inline-template action="/api/utc/presents/{{ $present->id }}">
                <div class="tile is-parent">
                    <div class="tile is-child">
                        <div class="card">
                            <header class="card-header">
                                <p class="card-header-title">{{ $present->title }}</p>
                                <p class="card-header-icon">Total given:  {{ $present->total_given }}</p>
                            </header>
                            <div class="card-content">
                                <div class="content">
                                    <div class="field">
                                        <label class="label">Daily limit</label>
                                        <input class="input is-danger" type="text" placeholder="Daily limit" value="{{ $present->daily_give }}" name="daily_give" ref="daily_give">
                                    </div>
                                    <div class="field">
                                        <label class="label">Overall limit</label>
                                        <input class="input is-danger" type="text" placeholder="Overall limit" value="{{ $present->total_give }}" name="total_give" ref="total_give">
                                    </div>
                                    <div class="field">
                                        <label class="label">Remaining</label>
                                        <input class="input is-danger" type="text" placeholder="Remaining" value="{{ $present->remaining }}" name="total_give" ref="remaining">
                                    </div>
                                </div>
                            </div>
                            <footer class="card-footer">
                                <a href="#" class="card-footer-item" @click="onSubmit">Update</a>
                            </footer>
                        </div>
                    </div>
                </div>
            </f-post>
            @endforeach
        </div>

    @endif

    {{ $presents->links('mma::partials.pagination') }}

@endsection

@section('js')

@stop