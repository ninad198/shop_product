@extends('layouts.app')

@section('content')
<?php
  if(isset($data) && $data != ''){
    $id                 = $data->id;
    $name               = $data->name;
    $email              = $data->email;
    $address            = $data->address;
    $image              = $data->image;
    if($image!= ''){
      $image =  asset('uploads/shops/images')."/".$image;
    } else {

    }
  }else{
    $id                 = '0';
    $name               = old('name');
    $email              = old('email');
    $address            = old('address');
    $image        = "";
    if(!empty($image) && $image!= ''){
      $image =  asset('uploads/shops/images')."/".$image;
    }else{
      $image = asset('uploads/default')."/shop-placeholder.jpg";
    }
  }
?>
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Shop {{isset($id) && $id != 0 ? 'Edit': 'Add'}}</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form method="post" action="{{isset($id) && $id != 0 ? route('admin.shop.update') :route('admin.shop.store')}}" id="shop-form"  enctype="multipart/form-data" >
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" name="edit_id" value="{{isset($id)?$id:'0'}}">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6 form-group">
          <label for="name">Shop Name</label>
        <input type="text" name="name" class="form-control" id="name" value="{{$name}}" placeholder="Enter Name">
        </div>
        <div class="col-md-6 form-group">
          <label for="email">Email</label>
        <input type="email" name="email" class="form-control" id="email" value="{{$email}}" placeholder="Enter Email">
        @error('email')
          <label id="email-error" class="error" for="email">{{ $message }}</label>
        @enderror
        </div>
      </div>
  
      <div class="row">
        <div class="col-md-12 form-group">
          <label for="address">Address</label>
          <textarea id="address" name="address" placeholder="Address" class="form-control" rows="4" cols="50">{{$address}}</textarea>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6 form-group">
        <label for="image">Shop Picture</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" name="image" id="image">
            <input type="hidden" class="custom-file-input" value="{{(isset($data->image) && $data->image != '' ? $data->image : '')}}" name="hiddenimage" id="hiddenimage">
            <label class="custom-file-label" for="image">Choose file</label>
          </div>
          @if(!empty($image))
            <img id="img_prview" src="{{$image}}" alt="your image" height="100" width="100" />
          @endif
        </div>
      </div>
    </div>
    <!-- /.card-body -->

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>


@endsection
@push('script')
<script type="text/javascript">
  $(document).ready(function () {
    $('#shop-form').validate({ // initialize the plugin
      ignore: ":hidden:not(#address),.note-editable.card-block",
        rules: {
          name: {
            required: true
          },
          address: {
            required: true
          },
          email: {
            required: true
          },
          image: {
            extension: "jpg|jpeg|png"
          }

        }
    });

    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#img_prview').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]); // convert to base64 string
      }
    }

    $("#image").change(function() {
      readURL(this);
    });
  });

</script>
@endpush