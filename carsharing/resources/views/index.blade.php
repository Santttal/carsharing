@extends('skeleton')
@section('body')
    <div class="col-sm-10">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="/state">
                    @csrf
                    {{ method_field('PUT') }}
                    @if ($state)
                        <button type="submit" name="state" value="{{ (string)!$state }}" class="btn btn-success">
                            State
                        </button>
                    @else
                        <button type="submit" name="state" value="{{ (string)!$state }}" class="btn btn-danger">
                            State
                        </button>
                    @endif
                </form>
                <form method="POST" action="/state">
                    @csrf
                    {{ method_field('PUT') }}
                    @if ($state)
                        <button type="submit" name="state" value="{{ (string)!$state }}" class="btn btn-success">
                            Order
                        </button>
                    @else
                        <button type="submit" name="state" value="{{ (string)!$state }}" class="btn btn-danger">
                            Order
                        </button>
                    @endif
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4>Filters</h4>
                <form method="POST" action="/filters">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="form-row align-items-center">
                        <div class="col-sm-3 my-1">
                            <label class="sr-only">Username</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">min oil</div>
                                </div>
                                <input name="min_oil" type="text" size=3 class="form-control" value="{{ $filters->minOil }}">
                            </div>
                        </div>
                        <div class="col-sm-3 my-1">
                            <label class="sr-only">Username</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">max price</div>
                                </div>
                                <input name="max_price" type="text" class="form-control" value="{{ $filters->maxPrice }}">
                            </div>
                        </div>
                        @foreach($filters->companies as $name => $enabled)
                            <div class="col-auto my-1">
                                <div class="form-check">
                                    <input type='hidden' value="0" name="companies[{{ $name }}]">
                                    <input name="companies[{{ $name }}]" class="form-check-input" type="checkbox" id="{{ $name }}Check" @if ($enabled) checked @endif>
                                    <label class="form-check-label" for="{{ $name }}Check">
                                        {{ $name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-auto my-1">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">State</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($polygons as $polygon)
                <tr>
                    <td>{{ $polygon->name }}</td>
                    <td>
                        <form method="POST" action="/polygon/{{ $polygon->id }}">
                            @csrf
                            {{ method_field('PUT') }}
                            @if ($polygon->state)
                                <button type="submit" name="state" value="{{ (string)!$polygon->state }}"
                                        class="btn btn-success">Enabled
                                </button>
                            @else
                                <button type="submit" name="state" value="{{ (string)!$polygon->state }}"
                                        class="btn btn-danger">Disabled
                                </button>
                            @endif
                        </form>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td>Радиус({{ $radius->amount }} м)</td>
                <td>
                    <div class="row">
                        <div class="col">
                            <form method="POST" action="/radius" class="form-inline pull-left">
                                {{ method_field('PUT') }}
                                @csrf
                                @if ($radius->state)
                                    <button type="submit" name="state" value="{{ (int)!$radius->state }}"
                                            class="btn btn-success">Enabled
                                    </button>
                                @else
                                    <button type="submit" name="state" value="{{ (int)!$radius->state }}"
                                            class="btn btn-danger mb-2">Disabled
                                    </button>
                                @endif
                            </form>
                        </div>
                        <div class="col">
                            <form method="POST" action="/radius" class="form-inline pull-left">
                                {{ method_field('PUT') }}
                                <input type="hidden" name="coordinates"
                                       value="{{ $home->getLatitude() }}, {{ $home->getLongitude() }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Set home</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="first_map" style="width:100%; height: 400px"></div>
@endsection

@section('js')

    <script type="text/javascript">
        var my_map;

        ymaps.ready(function () {
            my_map = new ymaps.Map("first_map", {
                center: [{{ $radius->coordinates->getLatitude() . ', ' . $radius->coordinates->getLongitude()}}],
                zoom: 12
            });
            @foreach ($polygons as $polygon)
                var myPolygon = new ymaps.Polygon([[
                            {{ $polygon->jsArray }}
                    ]]);
                my_map.geoObjects.add(myPolygon);
            @endforeach

            carsArray = [];
            var loadCars = function() {
                $.ajax({
                    type: 'GET',
                    url: '/cars',
                    dataType: 'json'
                }).done(function (cars) {
                    carsArray.forEach(function (yandexCar) {
                        my_map.geoObjects.remove(yandexCar);
                    });
                    carsArray = [];
                    cars.forEach(function (car) {
                        var yandexCar = new ymaps.Placemark([car.coordinates[0], car.coordinates[1]], {}, {preset: car.yandexStyle});
                        my_map.geoObjects.add(yandexCar);
                        carsArray.push(yandexCar);
                    });
                });
            };
            loadCars();
            setInterval(function() {
                loadCars();
            }, 10000);
        });
    </script>
@endsection

