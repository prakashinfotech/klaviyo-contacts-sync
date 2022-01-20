@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card bg-light mt-3">
            <div class="card-header">
                Import Contacts
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('import-contacts') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control">
                    <br>
                    <button class="btn btn-success">Import</button>
                </form>
            </div>
        </div>
    </div>
@endsection