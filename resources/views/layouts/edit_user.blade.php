<form id="edit_user_form" novalidate>
    {{ csrf_field() }}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">Edit User</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <h4 data-toggler="toggled" data-target="#account_details"> <i class="glyphicon glyphicon-chevron-down"></i> Account Details</h4>  
        </div>
        <div id="account_details" class="toggled in">
            <div class="form-group">
                <label data-error="wrong" data-success="right">Username</label>
                <input type="text" id="username" name="username" class="form-control validate" value="{{ $info[0]->username }}">
                   <input type="hidden" name="user_id" value="{{ $info[0]->id }}">
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right">Email Address</label>
                <input type="email" id="email" name="email" class="form-control validate" value="{{ $info[0]->email }}">
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right">User Role</label>
                 <select class="form-control" name="role" style="font-size: 15px;">
                     @foreach($roles as $key => $role)
                        <option {{ $info[0]->role_id == $role->id ? 'selected' : '' }} value="{{ $role->id }}">{{ $role->name }}</option>
                     @endforeach
                 </select>
            </div>
            <div class="form-group">
                @if($info[0]->status == 0)
                    <button type="button" class="btn btn-danger user-action btn-sm" data-action="activate">Activate User</button>
                    <button type="button" class="btn  btn-danger user-action btn-sm" data-action="reject">Reject User</button>
                @endif
            </div>
        </div>
        <div>
             <div class="form-group">
                <h4 data-toggler="toggled" data-target="#set_password"> <i class="glyphicon glyphicon-chevron-right"></i> Set New Password</h4>  
            </div>
            <div id="set_password" class="toggled " style="display:none;">
                <div class="form-group">
                    <span class="message-span"></span>
                    <div class="input-group input-group">
                      <input type="password" class="form-control" placeholder="New Password" id="new_password" required>
                      <span class="input-group-addon" id="new-pass-option">show</span>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm set-user-password">Save New Password</button>
                <button type="button" class="btn btn-danger btn-sm reset-click">Reset Password</button>
            </div>
        </div>
        <div class="form-group">
            <h4 data-toggler="toggled" data-target="#personal_info"> <i class="glyphicon glyphicon-chevron-down"></i> User Personal Information</h4>  
        </div>
        <div id="personal_info" class="toggled in">
            <div class="form-group">
                <label data-error="wrong" data-success="right">First Name</label>
                <input type="text" id="fname" name="fname" class="form-control validate" value="{{ $info[0]->first_name }}">
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right" >Last Name</label>
                <input type="text" id="lname" name="lname" class="form-control validate" value="{{ $info[0]->last_name }}">
            </div>

            <div class="form-group">
                <label data-error="wrong" data-success="right">Job Title</label>
                <input type="text" id="job_title" name="job_title" class="form-control validate" value="{{ $info[0]->job_title }}">
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right">Address Line 1</label>
                <input type="text" id="address1" name="address1" class="form-control validate" value="{{ $info[0]->address_line_1 }}">
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right">Address Line 2</label>
                <input type="text" id="address2" name="address2" class="form-control validate" value="{{ $info[0]->address_line_2 }}">
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right">Address Line 3</label>
                <input type="text" id="address3" name="address3" class="form-control validate" value="{{ $info[0]->address_line_3 }}">
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right">County</label>
                <input type="text" id="city" name="city" class="form-control validate" value="{{ $info[0]->city }}">
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right">Country</label>
                 <select class="form-control" name="country" style="font-size: 15px;">
                     @foreach($countries as $key => $country)
                        <option <?=$info[0]->country == $country->id ? 'selected' : '' ?> value="{{ $country->id }}">{{ $country->name }}</option>
                     @endforeach
                     

                 </select>
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right">Post Code</label>
                <input type="text" id="post_code" name="post_code" class="form-control validate" value="{{ $info[0]->post_code }}">
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right">Telephone Number</label>
                <input type="text" id="tel_number" name="tel_number" class="form-control validate" value="{{ $info[0]->telephone_number }}">
            </div>
            <div class="form-group">
                <label data-error="wrong" data-success="right">Mobile Number</label>
                <input type="text" id="mobile_number" name="mobile_number" class="form-control validate" value="{{ $info[0]->mobile_number }}">
            </div>
            <div class="form-group">
                <label>Timezone :</label>
                <div class="checkbox">
                    @foreach($timezone as $tz)
                        @php
                            $timezoneNameImage = strtolower($tz->name).'.png';
                        @endphp
                        <label>
                            <input type="radio" name="timezone" value="{{ $tz->id }}" {{ $tz->id == $info[0]->timezone ? 'checked' : '' }}> 
                            <img class="img_timezone" src="{{ url('public/img/'.$timezoneNameImage) }}"> {{ $tz->name }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer d-flex">
        <div class="text-right">
            <input type="submit" class="styled_btn submit_btn" name="addUser" value="Save Changes">
        </div>
        
    </div>
</form>
<form method="post" action="{{url('password/email')}}" id="reset-password-form">
    {{csrf_field()}}
    <input type="hidden" name="admin_send" value="{{ $info[0]->email }}">
    <input type="hidden" name="email" value="{{ $info[0]->email }}">
     <button type="submit" class="btn  btn-danger reset-password-action hidden" data-email="{{ $info[0]->email }}">Reset Password</button>

 </form>


<script>
    $(function(){
        $('[data-toggler]').click(function(){
            var that = $(this);
            var target_class = that.attr('data-toggler');
            var target_id = that.attr('data-target');

            if($(target_id).hasClass('in'))
            {
                $(target_id).removeClass('in');
                that.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
            }
            else
            {
                $(target_id).addClass('in');
                that.find('i').removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
            }
            if(target_id == '#set_password')
            {
                $('#account_details,#personal_info').hide();
                $('#account_details,#personal_info').removeClass('in');
                $('[data-target="#account_details"],[data-target="#personal_info"]').find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
            }
            $(target_id).toggle();

        });


        $('.set-user-password').click(function(){
            var that = $(this);
            var new_pass = $('#new_password').val();
            var _token = '{{ csrf_token() }}';
            var user = $('input[name="user_id"]').val();
            $.post(URL+'set-password',{'new_pass':new_pass,'_token':_token,'user':user})
                .done(function(result){
                    if(result == 'success')
                    {
                        $('#new_password').val('');
                        $('.message-span').text('Password updated successfully.').css('color','#28a745');
                    }
                    else
                    {
                        $('#new_password').focus();
                        $('.message-span').text('Invalid password. Passwords must be at least 6 characters long.').css('color','#d9534f');
                    }
                });
        });

        $('.reset-click').click(function(){
            $('.reset-password-action').click();
        });
        $('#reset-password-form').submit(function(){
            var that = $(this);
            $('.reset-click').attr('disabled',true);
            $('.reset-click').text('Sending Email...');
            var url_action = that.attr('action');
            var data = that.serialize();
            var _token = '{{ csrf_token() }}';
            $.post(url_action,data)
                .done(function(result){
                    $('.reset-click').text('Reset Password');
                    $('.reset-click').attr('disabled',false);
                    alertModal('User Status','Success! Reset password link sent.','');
                });

            return false;

            // $.ajax({
            //       url: URL+'reset-user',
            //       type:'POST',
            //       data: {'_token':_token, 'email':email},
            //       success: function(result) {
            //             console.log(result);
            //         },
            //         error:function(result){
            //             console.log(result);
            //         }
            //     });
        });
        $('#new-pass-option').click(function(){
            var that = $(this);
            if(that.text() == 'show')
            {
                $('#new_password').attr('type','text');
                that.text('hide');
            }
            else
            {
                $('#new_password').attr('type','password');
                that.text('show');
            }
        });

        $('#edit_user_form').submit(function(){
            var data = $(this).serialize();
            $.post(URL+'update-user',data)
                .done(function(result){
                    if(result == 'success')
                    {
                        alertModal('User Status','Update done successfully!','reload');
                    }
                   
                });
           return false;
        });

        $('.user-action').click(function(){
            var that = $(this);
            var action = that.attr('data-action');
            var user = $('input[name="user_id"]').val();
            $.get(URL+'change-status/'+user+'/'+action)
                .done(function(result){
                    // console.log(result);
                    alertModal('User Status','Update done successfully!','reload');
                    // location.reload();
                });
            return false;
        });

        $('.set-user-password').click(function(){
            var that = $(this);

            return false;
        });
    });
</script>