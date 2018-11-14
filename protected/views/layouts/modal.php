<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" id="alertModel">
            <div class="modal-header">
                <button type="button" class="close" name="closeModel" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <p id="alertText" class="text-danger"  style="font-size: 20px"></p>

            </div>
            <div class="modal-footer">
                <button type="button" name="closeModel" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" id="confirmAlertModel">
            <div class="modal-header">
                <button type="button" class="close" name="closeModel" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <p id="confirmText" class="text-danger"  style="font-size: 20px"></p>

            </div>
            <div class="modal-footer">
                <button type="button" id="confirmAlertButton" name="confirmModel" class="btn btn-default" data-dismiss="modal">确认</button>
                <button type="button" name="closeModel" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $(window).keydown(function(event){
            if(event.keyCode==13||event.keyCode==32){
                $('#myModal').modal('hide');
            }
            return true;
        });
    });
</script>