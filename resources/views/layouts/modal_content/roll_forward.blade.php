
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Search Project to Roll Forward</h4>
          </div>
          <div class="modal-body">
              <div class="form-group">
                <div class="col-lg-4">
                  <label>Project Name</label>
                  <input type="text" class="form-control rollback_project" placeholder="Search project here...">
                  <input type="hidden" value="{{ Auth::user()->role_id == 8 ? 0 : Auth::user()->company_id }}" id="company_id">
                </div>
                <div class="clearfix"></div>
              </div>
              <div class="table-responsive search_result_div">
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th>Job #</th>
                            <th>Project Name</th>
                            <th>Company</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="jobs_rollback_body">
                      <tr>
                          <td colspan="4" class="text-center">Please search project name.</td>
                      </tr>
                    </tbody>
                </table>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
        
        <script>
          $(function(){
              $('.rollback_project').keyup(function(){
                  var that = $(this);
                  var search = that.val();
                  var company_id = $('#company_id').val();
                  if(search.length > 3)
                  {
                    var _token = '{{ csrf_token() }}';
                    $.post(URL+'rollback-search',{'_token':_token,'search':search,'company_id':company_id})
                      .done(function(result){
                          var data = JSON.parse(result);
                          var str = '<tr><td colspan="3" class="text-center">No records found.</td></tr>';
                          if(data.length > 0)
                          {
                            str = '';
                            $.each(data,function(i,x){
                                str += '<tr>'+
                                          '<td>'+x.id+'</td>'+
                                          '<td>'+x.project_name+'</td>'+
                                          '<td>'+x.name+'</td>'+
                                          '<td><button class="btn btn-danger btn-sm select-project-roll">Select</button></td>'+
                                        '</tr>';
                            });
                          }
                          $('.jobs_rollback_body').html(str);
                          selectProjectRoll();
                      });
                  }
                  else
                  {
                     $('.jobs_rollback_body').html(str);
                  }
              });

          });

          function selectProjectRoll()
          {
            $('.select-project-roll').click(function(){
                var that = $(this);
                var job_id = that.closest('tr').find('td:nth-child(1)').text();
                 var input = $("<input>").attr("type", "hidden")
                    .attr("name", "id")
                    .val(job_id);
                $("#select-job-form").append(input).submit();
            });
          }
        </script>