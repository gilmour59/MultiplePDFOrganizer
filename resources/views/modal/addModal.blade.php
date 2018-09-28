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
                    <form id="addFileForm" method="POST" action="/view_files" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="control-label" for="addDate">Date Received: <small>mm/dd/yyyy</small></label>
                            <input class="form-control <?php $errors->has('addDate') ? "is-invalid": ""?>" type="date" name="addDate" id="addDate" autofocus>
                            <span id="error-addDate" class="invalid-feedback"></span>
                        </div>
                        <div class="form-group">
                            <label for="addFileUpload">Upload File: </label>
                            <input type="file" class="form-control-file <?php $errors->has('addFileUpload') ? "is-invalid": ""?>" id="addFileUpload" name="addFileUpload[]" multiple="multiple" onchange="javascript:updateList()">
                            <span id="error-addFileUpload" class="invalid-feedback"></span>
                            <div id="fileList"></div>
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

    function updateList() {
        var input = document.getElementById('addFileUpload');
        output = '<ul>';
        for (var i = 0; i < input.files.length; ++i) {
            output += '<li>' + input.files.item(i).name + '</li>';
        }
        output += '</ul>';
        $('#fileList').html(output);
    }
    
</script>