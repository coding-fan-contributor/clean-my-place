@extends('layouts.admin')

@section('content')
<!-- BEGIN CONTENT BODY -->
<div class="page-content">
    <div class="row" >
      <div class="col-md-12" >
          <div class="portlet light bordered" >
              <div class="portlet-title" ><h3>Reviews</h3></div>
              <div class="portlet-body">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="btn-group pull-right">
                                <a class="btn blue" href="/admin/create/review" id="add_new"><i class="fa fa-plus" style="font-size: 18px;"></i> Add Review</a>
                            </div>

                        </div>
                    </div>
                </div>

              <div class="table-responsive">
                  <table id="table_reviews" class="table table-striped table-bordered table-hover table-checkable order-column">
                      <thead>
                      <tr style="font-weight: bold;" >
                            <td>Id</td>
                            <td>Rating</td>
                            <td>Comment</td>
                            <td>User</td>
                            <td>Cleaner</td>
                            <td>Actions</td>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($reviews as $review)
                        <tr id="tr_{{$review->id}}">
                            <td>{{$review->id}}</td>
                            <td>{{$review->rating}} <i class="fa fa-star-o"></i></td>
                            <td style="width: 350px; max-width:350px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{$review->comment}}</td>
                            <td><a href="/admin/user/{{$review->user_id}}">{{$review->user->name}}</a></td>
                            <td><a href="/admin/cleaner/{{$review->cleaner_id}}">{{$review->cleaner->name}}</a></td>
                            @php
                                $review->comment = trim(preg_replace('/\s+/', ' ', $review->comment));
                                $review->comment = str_ireplace("'", "", $review->comment);
                            @endphp
                            <td>
                                <div class="btn-group"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="javascript:;" onclick="viewModal('{{$review->comment}}');"><i class="glyphicon glyphicon-zoom-out"></i> View</a></li><li><a href="javascript:;" onclick="delete_item({{$review->id}}, 'review');"><i class="glyphicon glyphicon-trash"></i> Delete</a></li></ul></div>
                            </td>
                        </tr>
                        @endforeach
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  </div>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Comment</h4>
        </div>
        <div class="modal-body">
            <p class="message"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>


  <style type="text/css">
      .table-scrollable{
          overflow-y: auto;
      }
  </style>
  <!-- END CONTENT BODY -->
  </div>

@endsection
