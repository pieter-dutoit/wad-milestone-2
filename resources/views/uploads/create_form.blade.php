@extends('layouts.master')

@section('title')
    Create Bulk Enrolments
@endsection

@section('description')
    Create a new course and bulk enrol teacher and learners.
@endsection

@section('content')
    <div class="table-card">
        <h4>Upload Bulk Enrolment File</h4>
        <p>Select your completed enrolment file</p>
        <hr>

        <form action="{{ url('uploads') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="input-wrapper">
                <label for="bulk_file" class="form-label">Enrolment file</label>
                <input name="bulk_file" class="form-control" type="file" id="bulk_file" value={{ old('bulk_file') }}>
            </div>

            @if (count($errors->all()) > 0)
                <strong>Please correct the following file issues and try again.</strong>
                <ul>
                    @foreach (array_unique($errors->all()) as $error)
                        <li style="color: #f46000">
                            <em>{{ $error }}</em>
                        </li>
                    @endforeach
                </ul>
            @endif

            <button type="submit" class="btn btn-dark" type="submit">Create enrolments</button>
        </form>
    </div>
@endsection
