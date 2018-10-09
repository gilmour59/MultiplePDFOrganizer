@extends('layouts.app')

@section('css')
  <style>
    .loading {
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: rgba(0,0,0,.5);
        text-align: center;
        z-index: 2000;
        display: none;
    }

    .loading-spin{
        width: 100%;
        height: auto;
        margin-top: -50px;
        margin-left: -50px;
        
        position: fixed;
        top: 50%;
        left: 0;
        
        border-width: 30px;
        border-radius: 50%;
    }

    .form-group.required label:after {
        content: " *";
        color: red;
        font-weight: bold;
    }
  </style>
@endsection

@section('content')
<div class="card mx-auto" style="width: 1150px;">
    <div class="card-header font-weight-bold">
        Add Files!
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div id="viewErrorMsg" class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $key => $error)
                        <li>{{ 'ID ' . $key . ' ' . $error }}</li>
                    @endforeach
                </ul>
            </div>
            <script>
                setTimeout(function() {
                    $("#viewErrorMsg").fadeTo(200, 0).slideUp(200, function(){
                        $(this).remove(); 
                    });
                }, 2000);
            </script>
        @endif
                    
        <div class="row pb-1">
            <div class="col-sm-6">
                <span>Error in IDs: </span>
                @foreach ($passData as $key => $row)
                    @if($row['key_div'] == 0)
                        <span style="color:red;">{{$key . ', '}}</span>
                    @endif
                @endforeach
            </div>
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-5 pt-2 text-right">
                        <label for="addDivision">Select All:</label>
                    </div>
                    <div class="col-sm-7">
                        <select class="form-control" name="allDivision" id="allDivision">
                            <!-- ajax generate -->
                        </select>
                    </div>
                </div>  
            </div>
            <div class="offset-1 col-sm-1 pb-1 align-self-end" style="text-align:right;">
                @if (!empty($passData))
                <button id="submitBtn" type="submit" form="saveFileForm" class="btn btn-primary" value="Submit">Save</button>
                @else
                <a class="btn btn-primary" href="{{route('index')}}">Go Back</a>
                @endif
            </div>
        </div>
        <div class="row">
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
                    <form action="{{route('store')}}" method="POST" id="saveFileForm">
                        @csrf
                        <tbody id="changeDivision">
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
                                        <input class="changeDate form-control" type="date" name="saveDate{{ $key }}" id="saveDate{{ $key }}" value="<?php echo old('saveDate' . $key) ? old('saveDate' . $key) : $row['date'] ?>">
                                    </td>
                                    <td style="text-align:left">{{ str_limit($row['content'], 100) }}</td>
                                    <td class="align-middle">
                                        <select class="changeDivision form-control col-sm-10" id="saveDivision{{ $key }}" name="saveDivision{{ $key }}">
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
                                <input type="hidden" name="saveAllDivision" id="saveAllDivision" value="0">
                        </tbody>
                    </form>
                </table>  
            </div>
        </div>
    </div>
</div>
<div class="loading">
    <div class='loading-spin'>
        <i class="fas fa-spinner fa-spin fa-5x"></i>
        <br>
        <span id='loading'>Loading</span>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/ajaxcrud.js') }}"></script>
    <script>
        $(document).ready(function(){ 
            console.log(num);
            ajaxDivisionGenerateForViewFiles('division', num); 
        });

        //Disables Submit Button If no division is selected
        $('#allDivision').change(function(){
            if($('#allDivision').val() == 0){
                $('.changeDivision, .changeDate').trigger('change');
                $('.changeDivision, .changeDate').change(function(){
                    var isValid = true
                    $('.changeDivision').each(function(){
                        if ($(this).val() == 0)
                            isValid = false;
                    });
                    $('.changeDate').each(function(){
                        if ($(this).val() == false)
                            isValid = false;
                    });
                    if( isValid ) {
                        $('#submitBtn').prop('disabled', false);
                    } else {
                        $('#submitBtn').prop('disabled', true);
                    }
                });        
            }else{
                $('#submitBtn').prop('disabled', false);
            }
        });

        $('.changeDivision, .changeDate').change(function(){
            var isValid = true
            $('.changeDivision').each(function(){
                if ($(this).val() == 0)
                    isValid = false;
            });

            $('.changeDate').each(function(){
                if ($(this).val() == false)
                    isValid = false;
            });
             if( isValid ) {
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#submitBtn').prop('disabled', true);
            }
        });

        $('#allDivision').change(function() { 
            if($('#allDivision').val() == 0){
                //remove disabled of children
                $('#changeDivision').find(".changeDivision").removeAttr('disabled');
                $('#saveAllDivision').val(0);
            }else if($('#allDivision').val() > 0){
                //$('#changeDivision').find(".changeDivision").val($('#allDivision').val());
                $('#changeDivision').find(".changeDivision").attr('disabled', true);
                $('#saveAllDivision').val($('#allDivision').val());
            }
        });

        $('#submitBtn').click(function(){
            $('.loading').show();
        });
    </script>
@endsection