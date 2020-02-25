@extends('layouts.frontsite')

@section('content')

    <form id="download_csv_form" action="{{route('accounting-date-range-download')}}" method="GET" >

        <input type="hidden" name="filename" id="download_zip"/>
    </form>

    <div class="wrapper">
        <div class="page-holder">
            <h2 class="content-title">Generate Account Sheet</h2>
            <div class="clear"></div>


            <form id="generate-csv-form" action="{{route('accounting-date-range')}}" method="GET" >
                <table>
                    <tr>
                        <td class="date_input">
                            <input type="text" class="datepicker" name="start_date"/>
                            <span id="help-block-date-range-start-date" class="form-select-error"> </span>
                        </td>
                        <td class="date_input">
                            <input type="text" class="datepicker" name="end_date"/>
                            <span id="help-block-date-range-end-date" class="form-select-error"> </span>
                        </td>
                        <td>
                            <button id="generate-accounting-sheet-btn" class="styled_btn" type="submit">
                                Submit
                            </button></td>
                    </tr>
                </table>

            </form>
        </div>


        <div id="snackbar">Generating CSV file..</div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>





        <script>

            $(document).ready(function() {

                $("#generate-accounting-sheet-btn").click(function (e) {

                    e.preventDefault();
                    var x = document.getElementById("snackbar");

                    // Add the "show" class to DIV
                    x.className = "show";


                    errorElements = document.getElementsByClassName('form-select-error');
                    for (var i = 0; i < errorElements.length; ++i) {
                        errorElements[i].innerHTML = '';
                    }

                    var frm = $('#generate-csv-form');

                    $.ajax({
                        type: frm.attr('method'),
                        url: frm.attr('action'),
                        data: frm.serialize(),
                        success: function (data) {
                            console.log('Submission was successful.');
                            console.log(data);
                            console.log(data['filename']);

                            var input = $("#download_zip");
                            input.val(data['filename']);

                            var x = document.getElementById("snackbar");
                            x.className = x.className.replace("show", "");
                            $('#download_csv_form').submit();


                        },
                        error: function (xhr, request, error) {
                            console.log('An error occurred.');

                            var x = document.getElementById("snackbar");
                            x.className = x.className.replace("show", "");

                            console.log('readyState: ' + xhr.readyState);
                            console.log('status: ' + xhr.status);
                            console.log('response text: ' + xhr.responseText);

                            try {
                                responseJson = JSON.parse(xhr.responseText);

                                if (responseJson['start_date']) {

                                    $('#help-block-date-range-start-date').text(responseJson['start_date']);
                                }


                                if (responseJson['end_date']) {

                                    $('#help-block-date-range-end-date').text(responseJson['end_date']);
                                }


                            } catch (err) {
                                console.log(err);
                            }


                        },
                    });

                    return false;
                });

            });
        </script>
    </div>
@endsection