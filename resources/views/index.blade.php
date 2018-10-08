        <div class="row">
            <div class="col-sm-6 align-self-end" style="text-align:left;">
                <h6>Total Data: <span id="total_records"></span></h6>
            </div>
            <div class="col-sm-6 pb-1 align-self-end"  style="text-align:right;">
                <!-- Button trigger modal -->
                <button id="addFilebtn" type="button" class="btn btn-primary" data-toggle="modal" data-target="#addFileModal">
                    Add
                </button>
            </div>
        </div>
        <div class="table-responsive" style="font-size:14px">
            <table class="table table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th width="1%">
                            <a href="javascript:ajaxLoad('{{url('/?field=id&sort='.(request()->session()->get('sort')=='asc'?'desc':'asc'))}}')">
                                ID{{request()->session()->get('field')=='id'?(request()->session()->get('sort')=='asc'?'▴':'▾'):''}}
                            </a>
                        </th>
                        <th width="1%">
                            <a href="javascript:ajaxLoad('{{url('/?field=date&sort='.(request()->session()->get('sort')=='asc'?'desc':'asc'))}}')">
                                Date{{request()->session()->get('field')=='date'?(request()->session()->get('sort')=='asc'?'▴':'▾'):''}}
                            </a>
                            <small class="d-block">yyyy/mm/dd</small>
                        </th>
                        <th width="1%">
                            <a href="javascript:ajaxLoad('{{url('/?field=file_name&sort='.(request()->session()->get('sort')=='asc'?'desc':'asc'))}}')">
                                File Name{{request()->session()->get('field')=='file_name'?(request()->session()->get('sort')=='asc'?'▴':'▾'):''}}
                            </a>
                        </th>
                        <th width="15%">
                            <a href="javascript:ajaxLoad('{{url('/?field=content&sort='.(request()->session()->get('sort')=='asc'?'desc':'asc'))}}')">
                                Content{{request()->session()->get('field')=='content'?(request()->session()->get('sort')=='asc'?'▴':'▾'):''}}
                            </a>
                        </th>
                        <th width="1%">
                            Division
                        </th>
                        <th width="1%"></th>
                        <th width="1%"></th>
                        <th width="1%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($archiveFiles as $row)
                    <tr>
                        <td class="align-middle">{{ $row->id }}</td>
                        <td class="align-middle">{{ $row->date }}</td>
                        <td class="align-middle">{{ $row->file_name }}</td>
                        <td style="text-align:left">{{ str_limit($row->content, 100) }}</td>
                        <!-- [$row->division_id - 1] because it was converted to an array and was reindexed -->
                        <td class="align-middle">{{ $division_name[$row->division_id - 1]['div_name'] }}</td>
                        <td class="align-middle"> <a style="font-size:12px" href="{{route('view', ['id' => $row->id])}}" target="_blank" class="btn btn-success">View</a> </td>
                        <td class="align-middle"> <button style="font-size:12px" type="button" class="btn btn-info" data-toggle="modal" data-target="#editFileModal" onclick="ajaxEdit('{{ route('edit', ['id' => $row->id]) }}')">Edit</button> </td>
                        <td class="align-middle"> 
                            <a style="font-size:12px" href="javascript:if(confirm('Are you sure want to delete?')) ajaxDelete('{{ route('destroy', ['id' => $row->id]) }}','{{csrf_token()}}')" class="btn btn-danger">X</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <ul class="pagination">
                {{ $archiveFiles->links() }}
            </ul>
        </div>

@include('modal.addModal')
@include('modal.editModal')

<script>
    $('.custom-file-input').on('change',function(){
        $(this).next('.form-control-file').addClass("selected").html($(this).val());
    });
</script>
