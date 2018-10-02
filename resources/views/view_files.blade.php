@extends('layouts.app')

@section('content')
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
                    <th width="7%">
                        Division
                    </th>
                </tr>
            </thead>
            <tbody>
                <script>
                    var num = [];    
                </script>
                <form action="">
                    @foreach ($passData as $key => $row)
                    <tr>
                        {{dd(count($passData))}}
                        <script>
                            var div = {{$key}}
                            var key_div = <?php $row['key_div'] ?>
                            num[div] = key_div;
                        </script>

                        <td class="align-middle">{{ $key }}</td>
                        <td class="align-middle">{{ $row['file_name'] }}</td>
                        <td class="align-middle">
                            <input class="form-control" type="date" name="addDate[]" id="addDate" value="{{ $row['date'] }}">
                        </td>
                        <td style="text-align:left">{{ str_limit($row['content'], 100) }}</td>
                        <td class="align-middle">
                            <select class="form-control col-sm-10" id="divisionViewFiles{{ $key }}" name="divisionViewFiles{{ $key }}">
                                <!-- ajax generate -->
                            </select>
                        </td>
                    </tr>
                    ADD HIDDEN DATA HERE TO SEND. AND JUST LOOP USING THE DATA OF COUNT($PASSDATA) IN CONTROLLER!
                    @endforeach
                </form>
            </tbody>
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