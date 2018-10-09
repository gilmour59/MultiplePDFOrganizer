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
        Search
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="division">Division:</label>
                    <select class="form-control col-sm-10" id="division" name="division">
                        <option value="0">All</option>
                    </select>
                </div> 
            </div>
            <div class="col-sm-9 align-self-center">
                <div class="input-group">
                    <button id="refreshFile" class="btn btn-outline-success offset-1" onclick="ajaxLoad('{{route('index')}}?search=')">
                        <i class="fas fa-redo"></i>
                    </button>
                    <input class="form-control col-sm-5" id="search" name="search" type="text" placeholder="Search Here" 
                    value="{{ request()->session()->get('search') }}" onkeydown="javascript:if(event.keyCode == 13){ajaxLoad('{{route('index')}}?search='+this.value)}"/>
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-outline-primary" onclick="ajaxLoad('{{route('index')}}?search='+$('#search').val())">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="content"> <!-- THIS GETS PASSED IN THE 'js/ajaxcrud.js' (ajaxLoad function) -->
        @include('index')
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
        Division = {{$division}};
        ajaxDivisionGenerateForSearch('division', Division);
        ajaxDivisionGenerate('division');
        //ajaxLoad('/');
        @if (count($errors) > 0)
            @if (session('isAdd'))
                $('#addFileModal').modal('show');
            @endif
        @endif
    });

    $('#refreshFile').click(function(){
        $('#search').val('');
    });

    $('#division').change(function() { 
        if($('#division').val() == 0){
            ajaxLoad('{{route('index')}}?division=0');
        }else{
            var div_id = $('#division').val();
            ajaxLoad('{{route('index')}}?division='+div_id);
        }
    });
</script>
@endsection