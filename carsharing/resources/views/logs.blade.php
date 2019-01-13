@extends('skeleton')
@section('body')
    <div class="col-sm-10">
        <div class="card">
            <div class="card-body">
                @foreach ($logs as $log)
                    @if ($log->level === 'success')
                        <div class="alert alert-success" id="id">{{ $log->message }}</div>
                    @else
                        <div class="alert alert-danger" id="id">{{ $log->message }}</div>
                    @endif
                @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')

@endsection
