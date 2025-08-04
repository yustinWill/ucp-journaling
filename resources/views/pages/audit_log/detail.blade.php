@extends('layouts.default')
@section('page_title')
    Detail Audit Log
@stop
@section('content')
    <!-- Header Section -->
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Audit Log -> {{ $form_title }} </h5>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card mb-6">
        <div class="card-body">
            <span>
                <h6>Jenis Audit Log: {{ $form_type }}</h6>
                <h6>Timestamp Audit Log: {{ $log_data->created_at }}</h6>
                <h6>User Yang Melakukan Perubahan: {{ $user->id }} - {{ $user->name }}</h6>
            </span>
        </div>
        <div class="card-datatable">
            <table id="main-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Field</th>
                        <th>Data Sebelum</th>
                        <th>Data Sesudah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($metadata['comparison'] as $row)
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td>{{ is_array($row['before']) || is_object($row['before']) ? json_encode($row['before']) : $row['before'] }}
                            </td>
                            <td>{{ is_array($row['after']) || is_object($row['after']) ? json_encode($row['after']) : $row['after'] }}
                            </td>
                            <td>
                                @if ($row['before'] == $row['after'])
                                    <span class="badge bg-primary">Tidak Ada Perubahan</span>
                                @else
                                    <span class="badge bg-warning">Ada Perubahan</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
            </table>
        </div>
    </div>
@stop

@section('js_script')
@endsection
