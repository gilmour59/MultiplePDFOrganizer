@extends('layouts.app')

@section('content')
    <div class='text-center float-right'>
        @if (!empty($passData))
        <button type="submit" form="saveFileForm" class="btn btn-primary" value="Submit">Save</button>
        @else
        <a class="btn btn-primary" href="{{route('index')}}">Go Back</a>
        @endif
    </div>
    <div class="table-responsive" style="font-size:14px">
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th width="1%">
                        ID: 
                    </th>
                    <th width="4%">
                        File Name: 
                    </th>
                    <th width="1%">
                        Date: 
                    </th>
                    <th width="15%">
                        Content
                    </th>
                    <th width="10%">
                        Division
                    </th>
                    <th width="1%">
                    </th>
                </tr>
            </thead>
            <form action="/store" method="POST" id="saveFileForm">
                @csrf
                <tbody>
                    <script>
                        var num = [];    
                    </script>
                        @foreach ($passData as $key => $row)
                        <tr>
                            <script>
                                var div = {{$key}}
                                var key_div = {{$row['key_div']}}
                                num[div] = key_div;
                                console.log(key_div);
                            </script>

                            <td class="align-middle">{{ $key }}</td>
                            <td class="align-middle">{{ $row['file_name'] }}</td>
                            <td class="align-middle">
                                <input class="form-control" type="date" name="saveDate{{ $key }}" id="saveDate{{ $key }}" value="{{ $row['date'] }}">
                            </td>
                            <td style="text-align:left">{{ str_limit($row['content'], 100) }}</td>
                            <td class="align-middle">
                                <select class="form-control col-sm-10" id="saveDivision{{ $key }}" name="saveDivision{{ $key }}">
                                    <!-- ajax generate -->
                                </select>
                            </td>
                            <td class="align-middle">
                                <button type="button" class="btn btn-danger" id="delete{{ $key }}" name="delete{{ $key }}" onclick='location.href="?delete={{ $key }}"'>x</button>
                            </td>
                        </tr>
                        <input type="hidden" name="saveId{{ $key }}" id="saveId{{ $key }}" value="{{ $key }}">
                        <input type="hidden" name="saveFileName{{ $key }}" id="saveFileName{{ $key }}" value="{{ $row['file_name'] }}">
                        <input type="hidden" name="saveContent{{ $key }}" id="saveContent{{ $key }}" value="{{ $row['content'] }}">
                        @endforeach
                        <script>
                            console.log(num);    
                        </script>
                </tbody>
            </form>
        </table>  
    </div>
@endsection

@section('js')
  <script src="{{ asset('js/ajaxcrud.js') }}"></script>
  <script>
    $(document).ready(function(){ 
        
        console.log(num);
        ajaxDivisionGenerateForViewFiles('/division', num);
            
    });

    $('#division').change(function() { 
        if($('#division').val() == 0){
            $('#category').attr('disabled', true);
            $('#category').find('option').remove();
            ajaxLoad('{{route('index')}}?division=0');
            
        }else{
            var div_id = $('#division').val();
            url = "/category/"+div_id;
            ajaxLoad('{{route('index')}}?division='+div_id+'&category=0');
            ajaxCategoryGenerateForSearch(url);
            $('#category').removeAttr('disabled');
        }
    });

    $('#category').change(function() { 
        if($('#category').val() == 0){
            ajaxLoad('{{route('index')}}?category=0');
        }else{
            var cat_id = $('#category').val();
            ajaxLoad('{{route('index')}}?category='+cat_id);
        }
    });
  </script>
@endsection