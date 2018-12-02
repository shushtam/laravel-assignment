@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        <div class="alert alert-danger hidden"></div>
                        <div class="ui-widget">
                            <label for="tags">Search: </label>
                            <input id="tags">
                        </div>
                    </div>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{asset('js/cities.js')}}"></script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBNZLQ276Qv1aiWeACLHpg00gI3qyZCnw4&callback=initMap">
    </script>
@endsection