<?php

namespace App\Http\Controllers;

use App\Requisition;
use App\Contact;
use App\Product;
use App\Variation;
use App\TaxRate;
use App\Transaction;
use App\PurchaseLine;
use App\BusinessLocation;
use App\Business;
use App\CustomerGroup;
use App\SellingPriceGroup;
use App\User;
use App\ReceiveBalanceBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Redirect;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;

class RequisitionController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
        'is_return' => 0, 'transaction_no' => ''];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('requisition.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $purchases = Requisition::join('contacts as c','c.id','=','requisitions.contact_id')
                        ->join('users as u','u.id','=','requisitions.created_by')
                        ->select('requisitions.*','c.name','u.username')
                        ->where('requisitions.business_id', $business_id)
                        ->orderby('requisitions.id','desc');
            
            // if (!empty(request()->supplier_id)) {
            //     $supplier_id = request()->supplier_id;
            //     $purchases->where('contacts.id', $supplier_id);
            // }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchases->whereDate('requisitions.transaction_date', '>=', $start)
                            ->whereDate('requisitions.transaction_date', '<=', $end);
            }
            return Datatables::of($purchases)

            ->addColumn('action',function($data){
                $button = '<a href="' . action('RequisitionController@edit', [$data->id]) . '" type="button" id="'.$data->id.'" class="btn btn-primary btn-xs"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view").'</a>';
               
                return $button;
            })
            ->rawColumns(['action'])

            ->editColumn('created_at', function ($user) 
            {
                return date('d-m-Y', strtotime($user->created_at) );
            })
            ->make(true);
            }
        return view('requisition.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('requisition.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $taxes = TaxRate::where('business_id', $business_id)
                            ->get();
        $orderStatuses = $this->productUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);
        $selling_price_groups = SellingPriceGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types();
        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);
        $banks = DB::table('transfer_balance_bank')->where('deleted_at',null)->select('bank_name','branch','account_no')->groupBy('bank_name','branch','account_no')->get();
        
        return view('requisition.create')
            ->with(compact('taxes','banks', 'orderStatuses', 'business_locations', 'currency_details', 'default_purchase_status', 'customer_groups', 'types', 'shortcuts', 'payment_line', 'payment_types', 'accounts', 'selling_price_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       

        if (!auth()->user()->can('requisition.create')) {
            abort(403, 'Unauthorized action.');
        }
    //    dd($request->mrp_price);
         try {
            $business_id = $request->session()->get('user.business_id');
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            //Check if subscribed or not
            

            $transaction_data = $request->only([ 'ref_no', 'status', 'contact_id', 'transaction_date', 'total_before_tax', 'location_id','discount_type', 'discount_amount','tax_id', 'tax_amount', 'final_total', 'additional_notes', 'exchange_rate']);

            $exchange_rate = $transaction_data['exchange_rate'];

            //Reverse exchange rate and save it.
            //$transaction_data['exchange_rate'] = $transaction_data['exchange_rate'];

            //TODO: Check for "Undefined index: total_before_tax" issue
            //Adding temporary fix by validating
            $request->validate([
                'status' => 'required',
                'contact_id' => 'required',
                'location_id' => 'required',
                'transaction_date' => 'required',
                'total_before_tax' => 'required',
                'final_total' => 'required',
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);
            $user_id = $request->session()->get('user.id');

            //Update business exchange rate.

            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['total_before_tax'], $currency_details)*$exchange_rate;

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($transaction_data['discount_type'] == 'fixed') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details)*$exchange_rate;
            } elseif ($transaction_data['discount_type'] == 'percentage') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details);
            } else {
                $transaction_data['discount_amount'] = 0;
            }

            $transaction_data['tax_amount'] = $this->productUtil->num_uf($transaction_data['tax_amount'], $currency_details)*$exchange_rate;
           
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details)*$exchange_rate;
            $all_data = $request->input('payment');

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['payment_amount'] = $all_data[0]['amount'];
            $transaction_data['payment_method'] = $all_data[0]['method'];

            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date']);

            //upload document
            if ($request->hasFile('document') && $request->file('document')->isValid()) {
                if ($request->document->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->document->getClientOriginalName();
                    $path = $request->document->storeAs('public/documents', $new_file_name);
                    $transaction_data['document'] = str_replace('public/documents/', '', $path);
                }
            }
            
            DB::beginTransaction();

            //Update reference count
            //Generate reference number
           
            $requisition = Requisition::create($transaction_data);
            
            $purchase_lines = [];
            $purchases = $request->input('purchases');
            foreach ($purchases as $purchase) {
                $new_purchase_line = [
                'product_id' => $purchase['product_id'],
                'variation_id' => $purchase['variation_id'],
                'quantity'=> $this->productUtil->num_uf($purchase['quantity'], $currency_details),
                'pp_without_discount' => $this->productUtil->num_uf($purchase['pp_without_discount'], $currency_details)*$exchange_rate,
                'discount_percent' => $this->productUtil->num_uf($purchase['discount_percent'], $currency_details),
                'purchase_price' => $this->productUtil->num_uf($purchase['purchase_price'], $currency_details)*$exchange_rate,
                'item_tax'=>$this->productUtil->num_uf($purchase['item_tax'], $currency_details)*$exchange_rate,
                'tax_id' => $purchase['purchase_line_tax_id'],
                'purchase_price_inc_tax' => $this->productUtil->num_uf($purchase['purchase_price_inc_tax'], $currency_details)*$exchange_rate,
                'reseller_price'  => $request->reseller_price,
                'mrp_price' => $request->mrp_price,
                'lot_number' => !empty($purchase['lot_number']) ? $purchase['lot_number'] : null
                ];

                if (!empty($purchase['mfg_date'])) {
                    $new_purchase_line['mfg_date'] = $this->productUtil->uf_date($purchase['mfg_date']);
                }
                if (!empty($purchase['exp_date'])) {
                    $new_purchase_line['exp_date'] =$this->productUtil->uf_date($purchase['exp_date']);
                }

                $purchase_lines[] = $new_purchase_line;

                //Edit product price

                // Update quantity only if status is "received"
               
            }
            if (!empty($purchase_lines)) {
                $requisition->details()->createMany($purchase_lines);
            }
            
            DB::commit();
            
            $output = ['success' => 1,
                            'msg' => 'Requisition Add SuccessFully'
                        ];
             } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }
        

        return redirect('requisition')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('purchase.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $purchase = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'contact',
                                    'purchase_lines',
                                    'purchase_lines.product',
                                    'purchase_lines.variations',
                                    'purchase_lines.variations.product_variation',
                                    'location',
                                    'payment_lines',
                                    'tax'
                                )
                                ->first();
        $payment_methods = $this->productUtil->payment_types();

        $purchase_taxes = [];
        if (!empty($purchase->tax)) {
            if ($purchase->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
            } else {
                $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
            }
        }

        return view('purchase.show')
                ->with(compact('taxes', 'purchase', 'payment_methods', 'purchase_taxes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
       

        //Check if the transaction can be edited or not.
    

        //Check if return exist then not allowed
        
        $payment_types = $this->productUtil->payment_types();
        $business = Business::find($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->get();
        $purchase = Requisition::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(
                        'contact',
                        'details',
                        'details.product',
                        'details.product.unit',
                        'details.variations',
                        'details.variations.product_variation',
                        'location'
                    )
                    ->first();
        $taxes = TaxRate::where('business_id', $business_id)
                            ->get();
        $orderStatuses = $this->productUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id);


        $default_purchase_status = 'received';
    

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);
        $selling_price_groups = SellingPriceGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        return view('requisition.edit')
            ->with(compact(
                'taxes',
                'purchase',
                'taxes',
                'orderStatuses',
                'business_locations',
                'business',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'selling_price_groups',
                'shortcuts',
                'payment_types'
            ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }

            $transaction_data = $request->only([ 'ref_no', 'status', 'contact_id', 'transaction_date', 'total_before_tax', 'location_id','discount_type', 'discount_amount','tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate']);

            $exchange_rate = $transaction_data['exchange_rate'];

            //Reverse exchange rate and save it.
            //$transaction_data['exchange_rate'] = $transaction_data['exchange_rate'];

            //TODO: Check for "Undefined index: total_before_tax" issue
            //Adding temporary fix by validating
            $request->validate([
                'status' => 'required',
                'contact_id' => 'required',
                'transaction_date' => 'required',
                'total_before_tax' => 'required',
                'location_id' => 'required',
                'final_total' => 'required',
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);
            $user_id = $request->input('user_id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            //Update business exchange rate.
            Business::update_business($business_id, ['p_exchange_rate' => ($transaction_data['exchange_rate'])]);

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['total_before_tax'], $currency_details)*$exchange_rate;

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($transaction_data['discount_type'] == 'fixed') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details)*$exchange_rate;
            } elseif ($transaction_data['discount_type'] == 'percentage') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details);
            } else {
                $transaction_data['discount_amount'] = 0;
            }

            $transaction_data['tax_amount'] = $this->productUtil->num_uf($transaction_data['tax_amount'], $currency_details)*$exchange_rate;
            $transaction_data['shipping_charges'] = $this->productUtil->num_uf($transaction_data['shipping_charges'], $currency_details)*$exchange_rate;
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details)*$exchange_rate;

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'purchase';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date']);

            //upload document
            if ($request->hasFile('document') && $request->file('document')->isValid()) {
                if ($request->document->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->document->getClientOriginalName();
                    $path = $request->document->storeAs('public/documents', $new_file_name);
                    $transaction_data['document'] = str_replace('public/documents/', '', $path);
                }
            }
            
            DB::beginTransaction();
            Requisition::where('id',$id)->update(['approve'=>'Approved']);
            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
            }
            $transaction = Transaction::create($transaction_data);
            
            $purchase_lines = [];
            $purchases = $request->input('purchases');
            foreach ($purchases as $purchase) {
                $new_purchase_line = [
                'product_id' => $purchase['product_id'],
                'variation_id' => $purchase['variation_id'],
                'quantity'=> $this->productUtil->num_uf($purchase['quantity'], $currency_details),
                'pp_without_discount' => $this->productUtil->num_uf($purchase['pp_without_discount'], $currency_details)*$exchange_rate,
                'discount_percent' => $this->productUtil->num_uf($purchase['discount_percent'], $currency_details),
                'purchase_price' => $this->productUtil->num_uf($purchase['purchase_price'], $currency_details)*$exchange_rate,
                'item_tax'=>$this->productUtil->num_uf($purchase['item_tax'], $currency_details)*$exchange_rate,
                'tax_id' => $purchase['tax_id'],
                'purchase_price_inc_tax' => $this->productUtil->num_uf($purchase['purchase_price_inc_tax'], $currency_details)*$exchange_rate,
                'reseller_price'  => $request->reseller_price,
                'mrp_price' => $request->mrp_price,
                'lot_number' => !empty($purchase['lot_number']) ? $purchase['lot_number'] : null
                ];

                if (!empty($purchase['mfg_date'])) {
                    $new_purchase_line['mfg_date'] = $this->productUtil->uf_date($purchase['mfg_date']);
                }
                if (!empty($purchase['exp_date'])) {
                    $new_purchase_line['exp_date'] =$this->productUtil->uf_date($purchase['exp_date']);
                }

                $purchase_lines[] = $new_purchase_line;

                //Edit product price
                if ($enable_product_editing == 1) {
                    //Default selling price is in base currency so no need to multiply with exchange rate.
                    $new_purchase_line['default_sell_price'] = $this->productUtil->num_uf($purchase['default_sell_price'], $currency_details);
                    $this->productUtil->updateProductFromPurchase($new_purchase_line);
                }

                // Update quantity only if status is "received"
                if ($transaction_data['status'] == 'received') {
                    $this->productUtil->updateProductQuantity($transaction_data['location_id'], $purchase['product_id'], $purchase['variation_id'], $purchase['quantity']);
                }
               $all_data = $request->input('payment');
               
            
                $this->transactionUtil->createOrUpdatePaymentLines($transaction, $request->input('payment'));
                $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            }
            if (!empty($purchase_lines)) {
                $transaction->purchase_lines()->createMany($purchase_lines);
            }
            
            DB::commit();
            
            $output = ['success' => 1,
                            'msg' => __('purchase.purchase_add_success')
                        ];
        
           
        return redirect('purchases')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = request()->session()->get('user.business_id');

                //Check if return exist then not allowed
                if ($this->transactionUtil->isReturnExist($id)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.return_exist')
                    ];
                    return $output;
                }
        
                $transaction = Transaction::where('id', $id)
                                ->where('business_id', $business_id)
                                ->with(['purchase_lines'])
                                ->first();
                $delete_purchase_lines = $transaction->purchase_lines;
                DB::beginTransaction();

                $transaction_status = $transaction->status;
                if ($transaction_status != 'received') {
                    $transaction->delete();
                } else {
                    //Delete purchase lines first
                    $delete_purchase_line_ids = [];
                    foreach ($delete_purchase_lines as $purchase_line) {
                        $delete_purchase_line_ids[] = $purchase_line->id;
                        $this->productUtil->decreaseProductQuantity(
                            $purchase_line->product_id,
                            $purchase_line->variation_id,
                            $transaction->location_id,
                            $purchase_line->quantity
                        );
                    }
                    PurchaseLine::where('transaction_id', $transaction->id)
                                ->whereIn('id', $delete_purchase_line_ids)
                                ->delete();

                    //Update mapping of purchase & Sell.
                    $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($transaction_status, $transaction, $delete_purchase_lines);
                }

                    //Delete Transaction
                    $transaction->delete();

                DB::commit();

                $output = ['success' => true,
                            'msg' => __('lang_v1.purchase_delete_success')
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => $e->getMessage()
                        ];
        }

        return $output;
    }
    
    /**
     * Retrieves supliers list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = Contact::where('business_id', $business_id);

            $selected_contacts = User::isSelectedContacts($user_id);
            if ($selected_contacts) {
                $query->join('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')
                ->where('uca.user_id', $user_id);
            }
            $suppliers = $query->where(function ($query) use ($term) {
                            $query->where('name', 'like', '%' . $term .'%')
                                ->orWhere('supplier_business_name', 'like', '%' . $term .'%')
                                ->orWhere('contacts.contact_id', 'like', '%' . $term .'%');
            })
                        ->select('contacts.id', 'name as text', 'supplier_business_name as business_name')
                        ->onlySuppliers()
                        ->get();
            return json_encode($suppliers);
        }
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
        if (request()->ajax()) {
            $term = request()->term;

            $check_enable_stock = true;
            if (isset(request()->check_enable_stock)) {
                $check_enable_stock = filter_var(request()->check_enable_stock, FILTER_VALIDATE_BOOLEAN);
            }

            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $q = Product::leftJoin(
                'variations',
                'products.id',
                '=',
                'variations.product_id'
            )
                ->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term .'%');
                    $query->orWhere('sku', 'like', '%' . $term .'%');
                    $query->orWhere('sub_sku', 'like', '%' . $term .'%');
                })
                ->where('business_id', $business_id)
                ->whereNull('variations.deleted_at')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    // 'products.sku as sku',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku'
                )
                ->groupBy('variation_id');

            if ($check_enable_stock) {
                $q->where('enable_stock', 1);
            }
            $products = $q->get();
                
            $products_array = [];
            foreach ($products as $product) 
            {
                $products_array[$product->product_id]['name'] = $product->name;
                $products_array[$product->product_id]['sku'] = $product->sub_sku;
                $products_array[$product->product_id]['type'] = $product->type;
                $products_array[$product->product_id]['variations'][]
                = [
                        'variation_id' => $product->variation_id,
                        'variation_name' => $product->variation,
                        'sub_sku' => $product->sub_sku
                        ];
            }

            $result = [];
            $i = 1;
            $no_of_records = $products->count();
            if (!empty($products_array)) {
                foreach ($products_array as $key => $value) {
                    if ($no_of_records > 1 && $value['type'] != 'single') {
                        $result[] = [ 'id' => $i,
                                    'text' => $value['name'] . ' - ' . $value['sku'],
                                    'variation_id' => 0,
                                    'product_id' => $key
                                ];
                    }
                    $name = $value['name'];
                    foreach ($value['variations'] as $variation) {
                        $text = $name;
                        if ($value['type'] == 'variable') {
                            $text = $text . ' (' . $variation['variation_name'] . ')';
                        }
                        $i++;
                        $result[] = [ 'id' => $i,
                                            'text' => $text . ' - ' . $variation['sub_sku'],
                                            'product_id' => $key ,
                                            'variation_id' => $variation['variation_id'],
                                        ];
                    }
                    $i++;
                }
            }
            
            return json_encode($result);
        }
    }
    
    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseEntryRow(Request $request)
    {
        if (request()->ajax()) 
        {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = request()->session()->get('user.business_id');

            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            if (!empty($product_id)) 
            {
                $row_count = $request->input('row_count');
                
                $product = Product::where('id', $product_id)
                                    ->with(['unit'])
                                    ->first();

                $query = Variation::where('product_id', $product_id)
                                        ->with(['product_variation']);
                                       
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations =  $query->get();
                //dd($variation_id);
                $taxes = TaxRate::where('business_id', $business_id)
                            ->get();
                
                $vld =DB::table('variation_location_details')->select('qty_available')->where('variation_id', $variation_id)->where('product_id', $product_id)->get();
                //dd($vld);
               
                #-----------------------------------
                $price_groups = SellingPriceGroup::where('business_id', $business_id)->pluck('name', 'id');
                $allowed_group_prices = [];
                foreach ($price_groups as $key => $value) 
                {
                    if (auth()->user()->can('selling_price_group.' . $key)) 
                    {
                        $allowed_group_prices[$key] = $value;
                    }
                }
        
                $group_price_details = [];

                foreach ($product->variations as $variation) {
                    foreach ($variation->group_prices as $group_price) 
                    {
                        $group_price_details[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
                    }
                }
                #----------------------------------

                return view('purchase.partials.purchase_entry_row')
                    ->with(compact(
                        'product',
                        'variations',
                        'row_count',
                        'variation_id',
                        'taxes',
                        'currency_details',
                        'hide_tax',
                        'vld',
                        'allowed_group_prices',
                        'group_price_details'
                    ));
            }
        }
    }
    
    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkRefNumber(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $contact_id = $request->input('contact_id');
        $ref_no = $request->input('ref_no');
        $purchase_id = $request->input('purchase_id');

        $count = 0;
        if (!empty($contact_id) && !empty($ref_no)) {
            //check in transactions table
            $query = Transaction::where('business_id', $business_id)
                            ->where('ref_no', $ref_no)
                            ->where('contact_id', $contact_id);
            if (!empty($purchase_id)) {
                $query->where('id', '!=', $purchase_id);
            }
            $count = $query->count();
        }
        if ($count == 0) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $taxes = TaxRate::where('business_id', $business_id)
                                ->pluck('name', 'id');
            $purchase = Transaction::where('business_id', $business_id)
                                    ->where('id', $id)
                                    ->with(
                                        'contact',
                                        'purchase_lines',
                                        'purchase_lines.product',
                                        'purchase_lines.variations',
                                        'purchase_lines.variations.product_variation',
                                        'location',
                                        'payment_lines'
                                    )
                                    ->first();
            $payment_methods = $this->productUtil->payment_types();

            $output = ['success' => 1, 'receipt' => []];
            $output['receipt']['html_content'] = view('purchase.partials.show_details', compact('taxes', 'purchase', 'payment_methods'))->render();
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;
    }
}
