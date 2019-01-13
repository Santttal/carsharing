@extends('skeleton')
@section('body')
    <div class="col-sm-10">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="form-group">
                        <label for="coordinates">Coordinates</label>
                        <input type="text" class="form-control" id="coordinates" name="coordinates"
                               value="{{ $radius->coordinates->getLatitude() }}, {{ $radius->coordinates->getLongitude() }}"
                        >
                    </div>
                    <div class="form-group">
                        <label for="amount">Radius</label>
                        <input type="text" class="form-control" id="amount" name="amount" value="{{ $radius->amount }}">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="state" name="state" {{ ($radius->state) ? 'checked' : '' }}>
                        <label class="form-check-label" for="state">Enabled</label>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('#coordinates').on('focus', function() {
            $(this).val('');
        });
    </script>
@endsection
