<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use DataTables;
use Excel;
use App\Imports\ShopsImport;
use Facade\FlareClient\Stacktrace\File;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.shop.index');
    }

    public function listData(Request $request)
    {
        //
        if ($request->ajax()) {
          $shop = Shop::select('*');
          return DataTables::of($shop)
          ->editColumn('image', function ($shop) {
            $file = $shop->image;
            if($file == ''){
                return asset('uploads/default')."/shop-placeholder.jpg";
            }else if(!file_exists( public_path().'/uploads/shops/images/'.$file )){
              return asset('uploads/default')."/shop-placeholder.jpg";
            }else{
                return asset('uploads/shops/images')."/".$file;
            }
            
       })
          ->addColumn('actions', function ($row) {
                $viewicon = '<a href="' . route('admin.shop.view', $row->id) . '" class="btn btn-primary" >
                <i class="far fa-eye" ></i>
            </a>';
              $editicon = '<a href="' . route('admin.shop.edit', $row->id) . '" class="btn btn-primary" >
                  <i class="far fa-edit" ></i>
              </a>';
              $deleteicon = '<a href="javascript:void(0);" class="btn btn-danger" onclick="deleteShop('.$row->id.')" data-id="' . $row->id . '"">
                  <i class="far fa-trash-alt"></i>
              </a>';
              return $action =$editicon."  ".$deleteicon." ".$viewicon;
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
    public function create()
    {
      return view('admin.shop.add_edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'email' => 'unique:shops,email',
        ]);

      $name               = $request->name;
      $email              = $request->email;
      $address            = $request->address;

      $image='';
      if ($request->hasFile('image')) {
          $image = $request->image->getClientOriginalExtension();
          $image = time().'.'.$image; // Add current time before image name
          $request->image->move(public_path().'/uploads/shops/images/',$image);
          $image = $image;
      }

      Shop::create([
              'name'    =>  $name,
              'email'   =>  $email,
              'address' =>  $address,
              'image'   =>  $image,
          ]);
      return redirect()->route('admin.shop.list')->with(['type'=>'success','icon'=>'success','message'=>'Shop Created in successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $shop = Shop::where('id',$id)->first();
      return view('admin.shop.view',['data'=> $shop]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        // dd($data)
        $shop = Shop::where('id',$id)->first();
        
        return view('admin.shop.add_edit',['data'=> $shop]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shop $shop)
    {
         $validatedData = $request->validate([
              'email' =>  'required|email|unique:shops,email,'.$request->edit_id,
          ]);
         
          $edit_id           = $request->edit_id;
         $name              = $request->name;
         $email             = $request->email;
         $address           = $request->address;
         $image = '';
         // dd($expiry_date);
         if($request->hasFile('image') && $request->image)
         {   
           $oldImageurl = public_path("/uploads/shops/images/{$request->hiddenimage}"); // get previous image from folder
 
           if (isset($request->hiddenimage) && $request->hiddenimage !='' &&  file_exists($oldImageurl)) { 
               unlink($oldImageurl);
           }
 
           $image = time() . '.' .
             $request->image->getClientOriginalExtension();
           $request->file('image')->move(public_path().'/uploads/shops/images/', $image);
         }
         
         if(isset($image) && $image != ''){
             $image = $image;
         }else{
             $image = $request->hiddenimage;
         }
 
 
         $data = Shop::where('id',$edit_id)->update([
             'name'       =>  $name,
             'address'    =>  $address,
             'email'      =>  $email,
             'image'      =>  $image
         ]);
 
            
         if($data){
             return redirect()->route('admin.shop.list')->with(['type'=>'success','icon'=>'success','message'=>'Successfully Update Shop.']);
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
       
        $data = Shop::findOrFail($id);
        if($data->id){
          $products = Product::where('shop_id',$id)->get();
          if(!empty($products)){
            foreach ($products as $key => $product) {
                if($product->video)
                {
                    if (file_exists(public_path().'/uploads/products/videos/'.$product->video))
                    {
                      unlink(public_path().'/uploads/products/videos/'.$product->video);
                    }
                }
                Product::where('id',$product->id)->delete();
            }
          }
        
        }
        if($data->image)
        {
            if (file_exists(public_path().'/uploads/shops/images/'.$data->image))
            {
               unlink(public_path().'/uploads/shops/images/'.$data->image);
            }
        }

        $data->delete();
        return ['success' => true,'type'=>'success','title'=>'Deleted!','message'=>'Seccessfully Delete Shop'];
    }


    public function import(Request $request){
        if ($request->method() === 'POST') {
            if ($request->hasFile('importcsv')){
                $path = $request->file('importcsv')->getRealPath();
                $data         = Excel::toArray(new ShopsImport,$request->file('importcsv'));
                $headerexcel  = isset($data[0][0]) ? $data[0][0] : array();
                $headerset    = 0;
                $shop_email_arrays   = array();
                $shop_email_exls= array();
                $arrDup           = array();
                $arrDupValue      = array();
                $records          = array();
                $headerset        = 0;
                $isValid          = true;
                if(!empty($headerexcel)) {
                    if (array_key_exists("name", $headerexcel) && array_key_exists("address",$headerexcel) && array_key_exists("email",$headerexcel)) {
                        $headerset = 1;
                    }
                }
                if($headerset == 1){
                    if(count($data) > 0 && count($data[0]) > 0 ) {
                      $records = $this->trimArray($data[0]);
                    } else {
                        return redirect()->route('admin.shop.list')->with(['type'=>'error','message'=>'Excel Sheet is empty.']);
                    //   return redirect()->back()->with('excelerror', 'Excel Sheet is empty.');
                    }
                } else {
                    return redirect()->route('admin.shop.list')->with(['type'=>'error','message'=>'Excel Header wrong data. Please download sample file.']);
                    // return redirect()->back()->with('excelerror', 'Excel Header wrong data. Please download sample file.');
                }

                if(!empty($this->trimArray($records))) {
                    $shop_data = Shop::all();
                    if (!empty($shop_data)) {
                        foreach($shop_data as $pItem) {
                            $pEmail                     = strtolower($pItem->email);
                            $shop_email_arrays[$pEmail] = $pItem->id; 
                        }
                    }

                    foreach ($records as $key => $value) {
                        if(isset($value['name']) && $value['name'] != "" ) {
                            $arrDup[$key]['name'] = $value['name'];
                        } else {
                            $arrDup[$key]['name'] = '';
                            if(!isset($arrDup[$key]['err_msg'])) {
                                $arrDup[$key]['err_msg']= "Shop Name is required";
                            }
                        }
                        if(isset($value['email']) && $value['email'] != "" ) {
                            $arrDup[$key]['email']  = $value['email'];
                            $shpEmil = strtolower($value['email']);
                            if(!in_array($shpEmil, $shop_email_exls)) {
                                    $shop_email_exls[]  = $shpEmil;
                                    if(isset($shop_email_arrays[strtolower($value['email'])])) {
                                        if(!isset($arrDup[$key]['err_msg'])) {
                                            $arrDup[$key]['err_msg'] = "Shop Email already exists";
                                        }
                                    }
                            } else {
                                if(!isset($arrDup[$key]['err_msg'])) {
                                    $arrDup[$key]['err_msg'] = "Shop Email already exists";
                                }
                            }
                        } else {
                            $arrDup[$key]['email']  = ''; 
                        }
            
                        if(isset($value['address']) && $value['address'] != "" ) {
                            $arrDup[$key]['address']  = $value['address'];
                        } else {
                            $arrDup[$key]['address']  = ''; 
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
                                        'address'   => $value['address'],
                                        'email'     => $value['email'],
                                    );
                                    Shop::create($shop_ins_arr);
                                }
                            }
                        } else {
                            return redirect()->route('admin.shop.list')->with(['type'=>'error','message'=>'Some Records have duplicate value.']);
                            // return redirect()->back()->with('error', 'Some Records have duplicate value.');
                        }
                    } else {
                        return redirect()->route('admin.shop.list')->with(['type'=>'error','message'=>'No Valid Records founds.']);
                        // return redirect()->back()->with('error', 'No Valid Records founds.');
                    }
                }
                return redirect()->route('admin.shop.list')->with(['type'=>'success','message'=>'Record have been updated successfully..']);
                // return redirect()->back()->with('success', 'Record have been updated successfully..');
            }
        }
        return view('admin.shop.import');
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
    
}