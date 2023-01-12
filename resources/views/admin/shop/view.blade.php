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
    }
  }
?>
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Shop View</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
    <div class="card-body">
      <div class="row">
        <div class="col-md-6 form-group">
          <label for="name">Shop Name</label>
        <input type="text" name="name" class="form-control" id="name" value="{{$name}}" placeholder="Enter Name" readonly>
        </div>
        <div class="col-md-6 form-group">
          <label for="email">Email</label>
        <input type="email" name="email" class="form-control" id="email" value="{{$email}}" placeholder="Enter Email" readonly>
        </div>
      </div>
  
      <div class="row">
        <div class="col-md-6 form-group">
          <label for="address">Address</label>
          <textarea id="address" name="address" placeholder="Address" class="form-control" rows="4" cols="50" readonly>{{$address}}</textarea>
        </div>
    
        <div class="col-md-6 form-group">
        <label for="image">Shop Picture</label>
          <br>
          <img id="img_prview" src="{{$image}}" alt="your image" height="100" width="100" />
        </div>
      </div>
    </div>
    <!-- /.card-body -->
</div>

@include('admin.product.index')
@endsection
