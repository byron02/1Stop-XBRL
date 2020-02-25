@php
  $action = request('action');
  if($action == 'reload')
  {
    $link = 'onClick=window.location.reload()';
  }
  else
  {
    $link = '';
  }
@endphp

        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"  {{$link}}>&times;</button>
            <h4 class="modal-title">{{ request('title') }}</h4>
          </div>
          <div class="modal-body">
            {{ request('message') }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal" {{$link}}>Close</button>
          </div>
        </div>