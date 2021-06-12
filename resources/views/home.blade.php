@extends('layouts.app')

@section('content')
    <feedslist-component></feedslist-component>

    <!-- Add feed modal -->
{{--    <div class="modal fade" id="addFeedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">--}}
{{--        <div class="modal-dialog" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title" id="exampleModalLabel">Add a feed</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <form action="{{url('addFeed')}}" method="post">--}}
{{--                        @csrf--}}
{{--                        <div class="form-group">--}}
{{--                            <input type="text" class="form-control" name="name" placeholder="Name">--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <input type="text" class="form-control" name="url" placeholder="URL">--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <div class="custom-file">--}}
{{--                                <label class="custom-file-label" for="file">Upload JSON configuration file</label>--}}
{{--                                <input type="file" id="file" ref="file" class="custom-file-input" onchange="displayFileName(this)">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <input type="text" class="form-control" placeholder="Merchant ID">--}}
{{--                        </div>--}}
{{--                        <div class="modal-footer">--}}
{{--                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
{{--                            <button type="submit" class="btn btn-primary">Add</button>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <!-- /Add feed modal -->

@endsection
