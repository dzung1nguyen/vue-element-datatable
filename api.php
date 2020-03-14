<?php
// Example router to get data for datatable
Router::get('/users/data', function() {
   return Yajra\DataTables\DataTables::of(App\User::select('users.*'))->make(true);
});
