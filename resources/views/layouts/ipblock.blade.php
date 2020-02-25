@extends('layouts.frontsite')

@section('content')

    <?php
    error_reporting(-1); // reports all errors
    ini_set("display_errors", "1"); // shows all errors
    ini_set("log_errors", 1);
    ini_set("error_log", "/tmp/php-error.log");
    ?>
    <div class="wrapper">
        <div class="page-holder">
            <h2 class="content-title">IP Address Block List</h2>
            <div class="clearfix"></div>
             <div class="table-responsive">
                ﻿

                <table class="table table-striped table-condensed table-hover" id="ipTable">
                    <tr class="dark">
                        <td data-field="ip_address">IP Address</td>
                        <td data-field="status">Status</td>
                        <td>Action</td>
                    </tr>
                    <tbody>
                        @foreach($ipaddress as $ip)
                        <tr>
                            <td>{{ $ip->ip_address }}</td>
                            <td>Blocked</td>
                            <td>
                                <a href="{{ url('unblock-ip/'.$ip->id) }}">
                                    <button class="btn btn-danger btn-sm" onclick="if (!confirm('Are you sure to unblock this ip address?')) return false;">Unblock</button>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination-container">
             </div>
            </div>
        </div>
    </div>

  
    @endsection