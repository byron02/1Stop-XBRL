         <script>
          $(function(){
              var from = $('.from_date').val();
              var to = $('.to_date').val();

             $( ".datepicker" ).datepicker({ dateFormat: 'M dd,yy' });
             $('.filter_type').change(function(){
                var that = $(this);
                if(that.val() == 'date_range')
                {
                    $('.date_range').removeClass('hidden');
                }
                else
                {
                   $('.date_range').addClass('hidden');
                }

             });

             $('.from_date').change(function(){
                from = $(this).val();
             });

             $('.to_date').change(function(){
                to = $(this).val();
             });


             $('.export-job-btn').click(function(){
                var that = $(this);
                var link = that.attr('data-href');
                if($('.filter_type:checked').val() == 'date_range')
                {
                    var str = '';
                    var flag = 0;



                    // $('.date_range .datepicker').each(function(){
                    //     if($(this).val() == '')
                    //     {
                    //         $(this).css('border','1px solid red').focus();
                    //     }
                    //     else
                    //     {
                    //         str += $(this).val()+'/';
                    //         flag++;
                    //     }
                    // });

                    // console.log($('.date_range .datepicker').length);
     
                        link = link+'/'+from+'/'+to;
                        window.location = link;

                    
                }
                else
                {
                    window.location = link;
                }
                 $('#alert-modal-sm').modal('hide');
                return false;
             });
          });
        </script>

 <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Export Jobs</h4>
          </div>
          <div class="modal-body">
              <label>Export Filter :</label>
              <div class="form-group checkbox">
                  <label><input type="radio" class="filter_type" name="filter_type" value="show_all" checked/> Show All</label>
                  <label><input type="radio" class="filter_type" name="filter_type" value="date_range" /> Date Range</label>
              </div>
              <div class="date_range hidden">
                <div class="form-group">
                    <label>From</label>
                    <input type="text" class="form-control datepicker from_date" value="{{ date('M 01, Y') }}" placeholder="mm-dd-yyyy" name="from">
                </div>
                 <div class="form-group">
                    <label>To</label>
                    <input type="text" class="form-control datepicker to_date" value="{{ date('M d, Y') }}"placeholder="mm-dd-yyyy" name="to">
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <a href="#" class="btn btn-dark export-job-btn" data-href="{{ route('export-jobs') }}">Export CSV</a>
          </div>
        </div>

