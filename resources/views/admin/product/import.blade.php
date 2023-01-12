@extends('layouts.app')

@section('content')
  <?php
    $shop_id = $shop_id;
?>
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Import Product</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form method="post" action="{{route('admin.product.import', $shop_id)}}" id="import-form"  enctype="multipart/form-data" >
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="card-body">      
      <div class="row">
        <div class="col-md-6 form-group">
        <label for="image">Import CSV</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" name="importcsv" id="importcsv">
            <input type="hidden" class="custom-file-input" value="" name="hiddenimage" id="hiddenimage">
            <label class="custom-file-label" for="image">Choose file</label>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>


@endsection
@push('script')
<script type="text/javascript">
  $(document).ready(function () {
    $('#import-form').validate({ // initialize the plugin
      ignore: ".note-editable.card-block",
        rules: {
          importcsv: {
            extension: "csv"
          }
        }
    });
  });

</script>
@endpush