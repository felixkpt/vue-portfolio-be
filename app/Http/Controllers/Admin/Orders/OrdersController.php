<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Order;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class OrdersController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return order's index view
     */
    public function index() {
        return view($this->folder.'order', []);
    }

    /**
     * store order
     */
    public function storeOrder() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('orders', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Order '.$action.' successfully']);
    }

    /**
     * return order values
     */
    public function listOrders() {
        $orders = Order::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('orders', 'status')) return $orders->where('status', 1)->get();
            else return $orders->get();
        }
        
        return SearchRepo::of($orders)
            ->addColumn('action', function($order) {
                $str = '';
                $json = json_encode($order);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'order_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/orders/delete').'\',\''.$order->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle order status
     */
    public function toggleOrderStatus($order_id)
    {
        $order = Order::findOrFail($order_id);        
        $state = $order->status == 1 ? 'Deactivated' : 'Activated';
        $order->status = $order->status == 1 ? 0 : 1;
        $order->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Order #'.$order->id.' has been '.$state]);
    }
    
    /**
     * delete order
     */
    public function destroyOrder($order_id)
    {
        $order = Order::findOrFail($order_id);
        $order->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Order deleted successfully']);
    }

}
