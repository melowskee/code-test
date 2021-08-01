@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header bg-secondary text-white">{{ __('Code Test') }}</div>

                    <div id="app" class="bg-light">
                        <br>
                        <calendar-component></calendar-component>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
