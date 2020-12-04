@extends('master')

@section('title', 'Vue.js App')

@section('content')

<!-- Page Wrapper -->

<router-view></router-view>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
  <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        Select "Logout" below if you are ready to end your current session.
      </div>
      <div class="modal-footer">
        <button class="btn btn-link" type="button" data-dismiss="modal">
          Cancel
        </button>
        <a class="btn btn-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" action="#" method="POST" style="display: none">
          @csrf
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
@section('pagescript')
<script src="js/app.js"></script>
@stop