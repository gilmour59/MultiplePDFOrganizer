<!-- Modal -->
<div class="modal fade" id="addFile" tabindex="-1" role="dialog" aria-labelledby="FileTitle" aria-hidden="true">
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
                    <form id="addFileForm" method="POST" action="/store" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="addFileName">File Name:</label>
                            <input type="text" class="form-control" id="addFileName" name="addFileName">
                            <span id="error-addFileName" class="invalid-feedback"></span>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="addFileUpload">Upload File: </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input <?php $errors->has('addFileUpload') ? "is-invalid": ""?>" id="addFileUpload" name="addFileUpload">
                                <label class="custom-file-label form-control-file" for="addFileUpload">Choose file</label>
                                <span id="error-addFileUpload" class="invalid-feedback"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="addDivision">Select Division:</label>
                            <select class="form-control" id="addDivision" name="addDivision">
                                <option value="0">--Select Here--</option>
                                <!--ajax options-->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="addCategory">Select Category:</label>
                            <select class="form-control" id="addCategory" name="addCategory" disabled>
                                <!--ajax options-->
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="addDate">Date Received: <small>mm/dd/yyyy</small></label>
                            <input class="form-control <?php $errors->has('addDate') ? "is-invalid": ""?>" type="date" name="addDate" id="addDate" autofocus>
                            <span id="error-addDate" class="invalid-feedback"></span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button id="closeAddFilebtn" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="addFileForm" class="btn btn-primary" value="Submit">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    
    document.getElementById("addDate").valueAsDate = new Date();

    $('#addFilebtn').on('click', function(){
        $('#addDivision').val(0).trigger('change');
    });

    $('#addDivision').change(function() { 
        if($('#addDivision').val() == 0){
            $('#addCategory').attr('disabled', true);
            $('#addCategory').find('option').remove();
        }else{
            var div_id = $('#addDivision').val();
            url = "/category/"+div_id;
            ajaxCategoryGenerate(url);
            $('#addCategory').removeAttr('disabled');
        }
    });
</script>