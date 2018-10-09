<!-- Modal -->
<div class="modal fade" id="addFileModal" tabindex="-1" role="dialog" aria-labelledby="FileTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="FileTitle">Add File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    @if (count($errors) > 0)
                        @foreach ($errors->all() as $error) <!--all() because the object has arrays as values-->
                            <div id="addErrorMsg" class="alert alert-danger">
                                {{$error}} <!-- Errors from validations (not sessions) -->
                            </div>
                        @endforeach
                        <script>
                            setTimeout(function() {
                                $("#addErrorMsg").fadeTo(200, 0).slideUp(200, function(){
                                    $(this).remove(); 
                                });
                            }, 2000);
                        </script>
                    @endif
                    <form id="addFileForm" method="POST" action="{{ route('view_files') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="control-label" for="addDate">Date Received: <small>mm/dd/yyyy</small></label>
                            <input class="form-control" type="date" name="addDate" id="addDate" value="{{ old('addDate') }}" autofocus>
                        </div>
                        <div class="form-group">
                            <label for="addFileUpload">Upload File: </label>
                            <input type="file" class="form-control-file" id="addFileUpload" name="addFileUpload[]" multiple="multiple" onchange="javascript:updateList()">
                            <div id="fileList"></div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button id="closeAddFilebtn" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="submitAddFilebtn" type="submit" form="addFileForm" class="btn btn-primary" value="Submit">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    function updateList() {
        var input = document.getElementById('addFileUpload');
        output = '<ul>';
        for (var i = 0; i < input.files.length; ++i) {
            output += '<li>' + input.files.item(i).name + '</li>';
        }
        output += '</ul>';
        $('#fileList').html(output);
    }

    $('#addFilebtn').click(function(){
        $('#addFileForm')[0].reset();
        document.getElementById("addDate").valueAsDate = new Date();
        updateList();
    });

    $('#submitAddFilebtn').click(function(){
        $('.loading').show();
    });

</script>