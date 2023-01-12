<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Excel;
use App\Imports\ProductsImport;

use App\Models\Product;
use App\Models\Shop;
use DataTables;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function listData(Request $request,$shop_id)
    {
        //
        if ($request->ajax()) {
          $shop = Product::where('shop_id',$shop_id)->select('*');
          return DataTables::of($shop)
          ->addColumn('actions', function ($row) {
              $editicon = '<a href="' . route('admin.product.edit',[$row->shop_id,$row->id]) . '" class="btn btn-primary" >
                  <i class="far fa-edit" ></i>
              </a>';
              $deleteicon = '<a href="javascript:void(0);" class="btn btn-danger" onclick="deleteProduct('.$row->id.')" data-id="' . $row->id . '"">
                  <i class="far fa-trash-alt"></i>
              </a>';
              return $action =$editicon."  ".$deleteicon;
          })
          ->rawColumns(['actions'])
          ->make();
      }
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($shop_id)
    {
      return view('admin.product.add_edit',['shop_id'=>$shop_id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $validatedData = $request->validate([
        'price' => 'numeric',
        'stock' => 'numeric',
        'name' =>  Rule::unique('products')->where(function ($query) use ($request) {
          return $query->where('name', $request->name)
             ->where('shop_id', $request->shop_id);
        })
      ]);
      $shop_id            = $request->shop_id;
      $name               = $request->name;
      $price              = $request->price;
      $stock              = $request->stock;
      $video              = $request->video;

      $video='';
      if ($request->hasFile('video')) {
          $video = $request->video->getClientOriginalExtension();
          $video = time().'.'.$video; // Add current time before video name
          $request->video->move(public_path().'/uploads/products/videos/',$video);
          $video = $video;
      }

      Product::create([
              'shop_id' => $shop_id,
              'name'    =>  $name,
              'price'   =>  $price,
              'stock'   =>  $stock,
              'video'   =>  $video,
          ]);
      return redirect()->route('admin.shop.view',$shop_id)->with(['type'=>'success','icon'=>'success','message'=>'Product Created in successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($shop_id,$id)
    {
        //
        $product = Product::where('id',$id)->first();
        
        return view('admin.product.add_edit',['data'=> $product,'shop_id'=>$shop_id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
      $validatedData = $request->validate([
        'price' => 'numeric',
        'stock' => 'numeric',
        'name' =>  Rule::unique('products')->where(function ($query) use ($request) {
          return $query->where('name', $request->name)
             ->where('shop_id', $request->shop_id);
        })->ignore($request->edit_id, 'id'),
      ]);
      $edit_id            = $request->edit_id;
      $shop_id            = $request->shop_id;
      $name               = $request->name;
      $price              = $request->price;
      $stock              = $request->stock;
      $video              = $request->video;
        $image = '';
      // dd($expiry_date);
      if($request->hasFile('video') && $request->video)
      {   
        $oldImageurl = public_path("/uploads/products/videos/{$request->hiddenvideo}"); // get previous video from folder

        if (isset($request->hiddenvideo) && $request->hiddenvideo !='' &&  file_exists($oldImageurl)) { 
            unlink($oldImageurl);
        }

        $video = time() . '.' .
          $request->video->getClientOriginalExtension();
        $request->file('video')->move(public_path().'/uploads/products/videos/', $video);
      }
      
      if(isset($video) && $video != ''){
          $video = $video;
      }else{
          $video = $request->hiddenvideo;
      }


      $data = Product::where('id',$edit_id)->update([
          'name'      =>  $name,
          'price'     =>  $price,
          'stock'     =>  $stock,
          'video'     =>  $video
      ]);

          
      if($data){
          return redirect()->route('admin.shop.view',$shop_id)->with(['type'=>'success','icon'=>'success','message'=>'Successfully Update Shop.']);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($shop_id,$id)
    {
    
        $data = Product::findOrFail($id);

        if($data->video)
        {
            if (file_exists(public_path().'/uploads/products/videos/'.$data->video))
            {
               unlink(public_path().'/uploads/products/videos/'.$data->video);
            }
        }

        $data->delete();
        return ['success' => true,'type'=>'success','title'=>'Deleted!','message'=>'Seccessfully Delete Shop'];
    }


    /** Kishan */
    public function import(Request $request, $shop_id){
        if ($request->method() === 'POST') {
            if ($request->hasFile('importcsv')){
                $path               = $request->file('importcsv')->getRealPath();
                $data               = Excel::toArray(new ProductsImport,$request->file('importcsv'));
                $headerexcel        = isset($data[0][0]) ? $data[0][0] : array();
                $headerset          = 0;
                $shop_name_arrays   = array();
                $shop_name_exls     = array();
                $arrDup             = array();
                $arrDupValue        = array();
                $records            = array();
                $headerset          = 0;
                $isValid            = true;

                if(!empty($headerexcel)) {
                    if (array_key_exists("name", $headerexcel) && array_key_exists("stock",$headerexcel) && array_key_exists("price",$headerexcel)) {
                        $headerset = 1;
                    }
                }
                if($headerset == 1){
                    if(count($data) > 0 && count($data[0]) > 0 ) {
                      $records = $this->trimArray($data[0]);
                    } else {
                        return redirect()->route('admin.shop.view',$shop_id)->with(['type'=>'error','message'=>'Excel Sheet is empty.']);
                    //   return redirect()->back()->with('excelerror', 'Excel Sheet is empty.');
                    }
                } else {
                    return redirect()->route('admin.shop.view',$shop_id)->with(['type'=>'error','message'=>'Excel Header wrong data. Please download sample file.']);
                    // return redirect()->back()->with('excelerror', 'Excel Header wrong data. Please download sample file.');
                }

                if(!empty($this->trimArray($records))) {
                    $product_data = Product::where('shop_id', $shop_id )->select('*')->get();
                    if (!empty($product_data)) {
                        foreach($product_data as $pItem) {
                            $pName                     = strtolower($pItem->name);
                            $shop_name_arrays[$pName] = $pItem->id; 
                        }
                    }

                    foreach ($records as $key => $value) {
                        if(isset($value['price']) && $value['price'] != "" ) {
                            if(is_numeric($value['price'])){
                                $arrDup[$key]['price'] = $value['price'];
                            } else {
                                if(!isset($arrDup[$key]['err_msg'])) {
                                    $arrDup[$key]['err_msg']    = "Invalid Price.";
                                }
                            }
                        } else {
                            $arrDup[$key]['price'] = '';
                            if(!isset($arrDup[$key]['err_msg'])) {
                                $arrDup[$key]['err_msg']= "Price is required";
                            } 
                        }
                        if(isset($value['stock']) && $value['stock'] != "" ) {
                            if(is_numeric($value['stock'])){
                                $arrDup[$key]['stock'] = $value['stock'];
                            } else {
                                if(!isset($arrDup[$key]['err_msg'])) {
                                    $arrDup[$key]['err_msg']    = "Invalid Stock.";
                                }
                            }
                        } else {
                            $arrDup[$key]['stock'] = '';
                            if(!isset($arrDup[$key]['err_msg'])) {
                                $arrDup[$key]['err_msg']= "Stock is required";
                            }
                        }
                        if(isset($value['name']) && $value['name'] != "" ) {
                            $arrDup[$key]['name']   = $value['name'];
                            $prdName                = strtolower($value['name']);
                            if(!in_array($prdName, $shop_name_exls)) {
                                    $shop_name_exls[]  = $prdName;
                                    if(isset($shop_name_arrays[strtolower($value['name'])])) {
                                        if(!isset($arrDup[$key]['err_msg'])) {
                                            $arrDup[$key]['err_msg'] = "Product already exists";
                                        }
                                    }
                            } else {
                                if(!isset($arrDup[$key]['err_msg'])) {
                                    $arrDup[$key]['err_msg'] = "Product already exists";
                                }
                            }
                        } else {
                            $arrDup[$key]['name']  = ''; 
                        }
                    }

                    if(!empty($arrDup)) {
                        foreach ($arrDup as $key => $value) {
                            if(isset($value['err_msg']) && $value['err_msg'] != ""){
                                $isValid = false;
                                break;
                            }
                        }
                        if ($isValid) {
                            foreach ($arrDup as $key => $value) {
                                if(!isset($value['err_msg']) || $value['err_msg'] == ""){
                                    $shop_ins_arr = array(
                                        'name'      => $value['name'],
                                        'stock'     => $value['stock'],
                                        'price'     => $value['price'],
                                        'shop_id'   => $shop_id,
                                    );
                                    Product::create($shop_ins_arr);
                                }
                            }
                        } else {
                            return redirect()->route('admin.shop.view',$shop_id)->with(['type'=>'error','message'=>'Some Records have duplicate value.']);
                            // return redirect()->back()->with('error', '');            
                        }
                    } else {
                        return redirect()->route('admin.shop.view',$shop_id)->with(['type'=>'error','message'=>'No Valid Records founds.']);
                        // return redirect()->back()->with('error', 'No Valid Records founds.');
                    }
                }
                return redirect()->route('admin.shop.view',$shop_id)->with(['type'=>'success','message'=>'Record have been updated successfully..']);
                // return redirect()->back()->with('success', 'Record have been updated successfully..');
            }
        }
        return view('admin.product.import',['shop_id'=>$shop_id]);
    }

    public function trimArray($arr) {
        $final = array();
        foreach($arr as $k => $v) {
          if(array_filter($v)) {
            foreach($v as $v_key => $val) {
              $v[$v_key] = trim($val);
            }
            $final[] = $v;
          }
        }
        return $final;
    }
    /**Kishan */
}
