@extends('layouts.app')

@section('content')
<?php
  $shop_id = $shop_id;
  if(isset($data) && $data != ''){
    $id                 = $data->id;
    $name               = $data->name;
    $price              = $data->price;
    $stock              = $data->stock;
    $video              = $data->video;
    if($video!= ''){
      $video =  asset('uploads/products/videos')."/".$video;
    }
  }else{
    $id                 = '0';
    $name               = old('name');
    $price              = old('price');
    $stock              = old('stock');
    $video              = "";
    if($video!= ''){
      $video =  asset('uploads/products/videos')."/".$video;
    }else{
      $video = asset('uploads/default')."/product-video-placeholder.jpg";
    }
  }
?>
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Product {{isset($id) && $id != 0 ? 'Edit': 'Add'}}</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form method="post" action="{{isset($id) && $id != 0 ? route('admin.product.update',$shop_id) :route('admin.product.store',$shop_id)}}" id="product-form"  enctype="multipart/form-data" >
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" name="edit_id" value="{{isset($id)?$id:'0'}}">
    <input type="hidden" name="shop_id" value="{{isset($shop_id)?$shop_id:'0'}}">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6 form-group">
            <label for="name">Product Name</label>
            <input type="text" name="name" class="form-control" id="name" value="{{$name}}" placeholder="Enter Name">
            @error('name')
              <label id="name-error" class="error" for="name">{{ $message }}</label>
            @enderror
        </div>
        <div class="col-md-6 form-group">
          <label for="price">Price</label>
          <input type="price" name="price" class="form-control" id="price" value="{{$price}}" placeholder="Enter Price">
          @error('price')
            <label id="price-error" class="error" for="price">{{ $message }}</label>
          @enderror
        </div>
      </div>
  
      <div class="row">
        <div class="col-md-6 form-group">
            <label for="stock">Stock</label>
            <input type="stock" name="stock" class="form-control" id="stock" value="{{$stock}}" placeholder="Enter Stock">
          @error('stock')
            <label id="stock-error" class="error" for="stock">{{ $message }}</div>
          @enderror
        </div>
     
        <div class="col-md-6 form-group">
          <label for="video">Product Video</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" name="video" id="video" accept="video/mp4,video/x-m4v,video/*">
            <input type="hidden" class="custom-file-input" value="{{(isset($data->video) && $data->video != '' ? $data->video : '')}}" name="hiddenvideo" id="hiddenvideo">
            <label class="custom-file-label" for="video">Choose file</label>
          </div>
          {{(isset($data->video) && $data->video != '' ? $data->video : '')}}
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
    $.validator.addMethod('filesize', function(value, element, param) {
  return this.optional(element) || (element.files[0].size <= param)
}, 'File size must be less than {0} bytes');

    $('#product-form').validate({ // initialize the plugin
      ignore: ":hidden:not(#stock),.note-editable.card-block",
        rules: {
          name: {
            required: true
          },
          stock: {
            required: true,
            digits: true
          },
          price: {
            required: true,
            number: true
          },
          video: {
            extension: "ogg|ogv|avi|mpe?g|mov|wmv|flv|mp4",
            filesize: 5242880
          }

        }
    });

   
  });

</script>
@endpush