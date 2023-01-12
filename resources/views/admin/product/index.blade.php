
<?php
  $downloadPath =  asset('default/product.csv'); 
?>
<div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Product List</h3>
        <div class="card-tools">
          <a href="{{route('admin.product.create',$id)}}" class="btn btn-primary" tag="button">Add New Product<i class="fa fa-product" aria-hidden="true"></i></a>
          <!-- <a class="btn btn-primary">Download Sample CSV <i class="fa fa-image" aria-hidden="true"></i></a> -->
          <a href="{{URL::to('/public')}}/default/product.csv" download="" class="btn btn-primary" target="_blank">
              <i class="fa fa-download"></i> Sample CSV
          </a>
          <a href="{{route('admin.product.import',$id)}}" class="btn btn-primary" tag="button">Import CSV <i class="fa fa-image" aria-hidden="true"></i></a>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <div class="d-flex justify-content-center mb-15">
        </div>
        <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4 table-responsive">
            <table id="product_list" class="display Main-Table-View table table-hover" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>price</th>
                    <th>stock</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>

@push('script')
@if(session()->has('message'))
  <script type="text/javascript">
    $(document).ready(function () {
      var typeError = "{{ session()->get('type') }}";
      if(typeError === 'success') {
        toastr.success("{{ session()->get('message') }}");
      } else {
        toastr.error("{{ session()->get('message') }}");
      }
    });
  </script>
@endif
<script type="text/javascript">
  var shop_id = "<?php echo $id ?>";
  var shopUrl = "{{url('admin/shop/')}}";

  $(document).ready(function () {
    initProductsTable();  
  });
  function initProductsTable() {
    if($(document).find('#product_list').length > 0) {
      var product_list_tbl = $('#product_list').DataTable({
        dom: 'Blfrtip',
        "destroy"   : true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "pageLength": 10,
        "order": [[ 0, "desc" ]],
        buttons: [{
              extend: 'csv',
              text: "Export CSV",
              title: 'Product',
              exportOptions: {
                columns: [ 0, 1, 2, 3 ]
              },
            }],
       
      "ajax":{
              "url"     : shopUrl+"/"+shop_id+"/products/listdata",
              "dataType": "json",
              "type"    : "GET",
              "data"    :{ _token: "{{csrf_token()}}"}
          },
        "columns": [
          { "data": "id" },
            { "data": "name" },
            { "data": "price" },
            { "data": "stock" },                
            { "data": "actions" }
        ],
        columnDefs : [
          { targets: 0, visible: false, searchable: false },
          { targets: 4, orderable : false, className: "text-center", width: "25%" },
        ],
        responsive:true
      });
    }  
  }

  function deleteProduct(id)
  {
    Swal.fire({
      text: "are You sure whant to delete?",
      icon: 'warning',
      showCancelButton: true,
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'OK'
    }).then((result) => {
      if (result.value) {
        //send request to server
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
           type:'DELETE',
            url: shopUrl+"/"+shop_id+"/product/delete/"+id,
            dataType:'json',
            data: {
              _token:'{{ csrf_token() }}', 
              id:id
             },
            beforeSend: function(){
            //
            },
            success:function(data){
              Swal.fire(
                data['title'],
                data['message'],
                data['type']
              )
              $('#product_list').DataTable().ajax.reload();
            },
            complete:function(data){
             //
            },
        });
      }
    })
  }


</script>
@endpush