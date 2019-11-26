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

            @foreach($presents as $present)

                <f-post inline-template action="/api/utc/presents/{{ $present->id }}">

                    <div class="tile">

                        <div class="card">
                            <header class="card-header">
                                <p class="card-header-title">
                                    {{ $present->title }}
                                </p>
                                <p class="card-header-icon">
                                    Σύνολο δοθέντων:  {{ $present->total_given }}
                                </p>
                            </header>

                            <div class="card-content">
                                <div class="content">
                                    <div class="field">
                                        <label class="label">
                                            Ημερήσιο σύνολο
                                        </label>
                                        <input class="input is-danger" type="text" placeholder="Ημερήσιο σύνολο" value="{{ $present->daily_give }}" name="daily_give" ref="daily_give">
                                    </div>
                                    <div class="field">
                                        <label class="label">
                                            Γενικό σύνολο
                                        </label>
                                        <input class="input is-danger" type="text" placeholder="Ημερήσιο σύνολο" value="{{ $present->total_give }}" name="total_give" ref="total_give">
                                    </div>
                                    {{--                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec iaculis mauris.--}}
                                    {{--                                <a href="#">@bulmaio</a>. <a href="#">#css</a> <a href="#">#responsive</a>--}}
                                    {{--                                <br>--}}
                                    {{--                                <time datetime="2016-1-1">11:09 PM - 1 Jan 2016</time>--}}
                                </div>
                            </div>
                            <footer class="card-footer">
                                <a href="#" class="card-footer-item" @click="onSubmit">
                                    Update
                                </a>
                                {{--                            <a href="#" class="card-footer-item">Edit</a>--}}
                                {{--                            <a href="#" class="card-footer-item">Delete</a>--}}
                            </footer>
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