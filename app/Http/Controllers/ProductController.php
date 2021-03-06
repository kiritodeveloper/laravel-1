<?php

namespace Veterinaria\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Veterinaria\Http\Requests;
use Veterinaria\Http\Controllers\Controller;
use Veterinaria\Http\Requests\ProductRequest;
use Veterinaria\Product;
use Veterinaria\ProductType;
use Veterinaria\Provider;
use Veterinaria\RecordTypeStock;
use Veterinaria\Stock;

class ProductController extends Controller
{
    public function __construct(){
        $this -> middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::Search($request->name)->paginate(10);
        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $providers = Provider::all();
        $productTypes = ProductType::all();
        return view('product.create',compact('providers','productTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Veterinaria\Http\Requests\ProductRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        Product::Create([
             'name'              => $request['name']
            ,'product_type_id'   => $request['product_type_id']
            ,'provider_id'       => $request['provider_id']
            //,'quantity'          => $request['quantity']
            ,'price'             => $request['price']
            ,'stock_alert'             => $request['stock_alert']
        ]);

        Session::flash('message', 'Producto creado correctamente');
        return Redirect::to('/product');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $providers = Provider::all();
        $productTypes = ProductType::all();

        return view('product.edit', compact('product','productTypes','providers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Veterinaria\Http\Requests\ProductRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);
        $product -> fill($request->all());
        $product -> save();

        Session::flash('message', 'Producto editado correctamente');
        return Redirect::to('/product');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Product::destroy($id);
        $product = Product::find($id);
        $product -> delete();
        Session::flash('message','Producto eliminado Correctamente');
        return Redirect::to('/product');
    }

    public function searchByName(Request $request){
        if($request->ajax()){
            $products = Product::search($request->name)->available()->orderBy('name')->get();

            return response()->json(
                $products->toArray()
            );
        }

        return null;
    }

    public function searchById(Request $request){
        if($request->ajax()){
            $product = Product::searchById($request->id)->available()->get();

            return response()->json(
                $product->toArray()
            );
        }

        return null;
    }

    public function add(Request $request){
        $recordTypeStock = RecordTypeStock::find("1");

        Stock::Create([
            'invoice_number' => $request->invoice_number
            ,'quantity' => $request->quantity
            ,'product_id' => $request->id
            ,'record_type_stock_id' => $recordTypeStock->id
        ]);

        $product = Product::find($request->id);
        $product->quantity = ($product->quantity+$request->quantity);
        $product->save();

        Session::flash('message', 'Stock agregado correctamente');
        return Redirect::to('/product');
    }
}
