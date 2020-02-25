        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{ request('title') }}</h4>
          </div>
          <div class="modal-body">
            {{ request('message') }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger confirm-action" data-return="1">Yes</button>
            <button type="button" class="btn btn-default confirm-action" data-return="0">No</button>
          </div>
        </div>
        