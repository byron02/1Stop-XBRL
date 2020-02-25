@extends('layouts.frontsite')

@section('content')

<div class="wrapper">
    <div class="page-holder">
        <h2 class="content-title">Jobs</h2>
        <div id="dashboard_container">
	        <div id="cont" style="">
                <div class="filter-status" filter-value="2" title="New order">
                    <span class="name">New order</span><span class="job_count">0</span></div>
                <div class="filter-status" filter-value="3" title="In Progress">
                    <span class="name">In Progress</span><span class="job_count">17</span></div>
                <div class="filter-status" filter-value="4" title="In Revision">
                    <span class="name">In Revision</span><span class="job_count">0</span></div>
                <div class="filter-status" filter-value="7" title="Revisions Submitted">
                    <span class="name">Revisions Submitted</span><span class="job_count">0</span></div>
                </div>
        </div>
        <div class="add-btn">
            <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/add" 
            class="styled_btn submit_btn" style="text-decoration:none">
            <span><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/plus.png" 
            class="btn_icon">ADD JOB</span></a>
        </div>
        <div class="clear"></div>

        <table id="search">
            <tr>
                <td>Search by</td>
                <td>
                    <select>
                        <option>--Please Select--</option>
                        <option>Status</option>
                        <option>Query</option>
                    </select>
                </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
                <td>Select Action</td>
                <td>
                    <select>
                        <option>Complete for client sign of</option>
                        <option>Assign to vendor</option>
                        <option>Assign to back vendor</option>

                        </select>   
                </td>
                <td><a href="#" class="styled_btn submit_btn" style="padding:4px" id="batch_update">Update Status</a></td>

            </tr>
            
        </table>

        <div class="datagrid_wrap">
        <table class="job_list">
        <tr>
			<td class="datagrid_header" width="25px">Paid</td>
			<td class="datagrid_header" width="25px">Action <input type="checkbox" id="chk_all" style="position:relative;left:8px;"></td>
			<td class="datagrid_header" width="60px">Job No.<a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/jobs.id/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
			<td class="datagrid_header" width="25px">Client PO</td>
			<td class="datagrid_header" width="25px">Pricing Reference</td>
			<td class="datagrid_header" width="115px">Project Name <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/project_name/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
			<td class="datagrid_header">Pricing <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/computed_price/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
			<td class="datagrid_header" width="100px">Accounting Standards <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/t.name/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
			<td class="datagrid_header" width="100px">Company <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/Company/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
			<td class="datagrid_header">Due Date <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/jobs.due_date/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
			<td class="datagrid_header" width="100px">Status Vendor <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/job_status.name/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
		<!-- 	<td class="datagrid_header" width = '70px'>Output Files</td>  -->
			<td class="datagrid_header" valign="middle">Paid</td>
			<td class="datagrid_header" valign="middle">Invoice Number</td>

        </tr>
    </div>

        </table>

    </div>
</div>
@endsection
aa