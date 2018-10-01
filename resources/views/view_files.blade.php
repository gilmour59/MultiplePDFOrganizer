@extends('layouts.app')

@section('content')
    <div class="table-responsive" style="font-size:14px">
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th width="1%">
                        File Name: 
                    </th>
                    <th width="1%">
                        Date: 
                    </th>
                    <th width="15%">
                        Content
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($passData as $row)
                <tr>
                    <td class="align-middle">{{ $row['file_name'] }}</td>
                    <td class="align-middle">{{ $row['date'] }}</td>
                    <td style="text-align:left">{{ str_limit($row['content'], 100) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection