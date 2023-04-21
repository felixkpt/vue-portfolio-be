<?php

namespace App\Http\Controllers;

use App\Models\Core\Customer;
use App\Models\Core\Ticket;
use App\Repositories\ModelSaverRepository;
use App\Models\Core\TicketUpdate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // protected $folder = "";

    // public function __construct()
    // {
    //     $class = get_class($this);

    //     $class = str_replace('App\\Http\\Controllers\\', "", $class);

    //     $arr = explode('\\', $class);
    //     unset($arr[count($arr) - 1]);
    //     $folder = implode('.', $arr) . '.';
    //     $this->folder = 'core.' . strtolower($folder);
    // }

    function saveModel($data)
    {
        $model_saver = new ModelSaverRepository();
        $model = $model_saver->saveModel($data);
        return $model;
    }

    function autoSaveModel($data)
    {
        $model_saver = new ModelSaverRepository();
        $model = $model_saver->saveModel($data);
        return $model;
    }


    public function bytesToHuman($bytes)
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function cartName()
    {
        return request()->user()->id . '_cart';
    }

    public function invoiceCartName()
    {
        return request()->user()->id . '_invoice_cart';
    }

    public function quotationCartName()
    {
        return request()->user()->id . '_quotation_cart';
    }

    /**
     * empty cart and revert stock quantity
     * values
     */
    public function emptyCart($cart_name, $default_branch)
    {
        $cart_data = Session::get($cart_name);
        //        foreach ($cart_data as $cart) {
        //            $product = Product::whereId($cart['product_id'])->first();
        //            if ($default_branch->main == 0)
        //                $model = AssignProduct::where([
        //                    ['product_id', $cart['product_id']],
        //                    ['to_branch', $default_branch->id]
        //                ])->first();
        //            else
        //                $model = $product;
        ////            $model->quantity = $model->quantity + $cart['quantity'];
        ////            $model->save();
        //        }
        //        Session::forget($cart_name);
    }

    public function searchContact($term)
    {

        if (empty($term)) {
            return Customer::latest()
                ->with('user')
                ->with('dealer');
        }

        return Customer::search($term, null, true, true)
            ->with('user')
            ->with('dealer');
    }
    public function searchCustomer($term)
    {

        if (empty($term)) {
            return Customer::latest()
                ->with('user')
                ->with('dealer');
        }

        return Customer::search($term, null, true, true)
            ->with('user')
            ->with('dealer');
    }


    /**
     * search through array for a mathching value
     * then return the index
     */
    function searchMultDimArray($array_data, $index, $value)
    {

        foreach (@$array_data as $key => $data) {

            // return the matched array key if found
            if (@$data[$index] === $value)
                return $key;
        }
        return -1;
    }


    protected function saveTicketUpdates($ticket, $data)
    {
        $ticket_update = new TicketUpdate();
        $ticket_update->ticket_id = $ticket->id;
        $ticket_update->source_id = $ticket->source_id;
        $ticket_update->issue_category_id = $ticket->issue_category_id;
        $ticket_update->disposition_id = $ticket->disposition_id;
        $ticket_update->update_comments = isset($data['ticket_comments']) ? $data['ticket_comments'] : null;
        $ticket_update->ticket_status_id = 1;
        $ticket_update->user_id = auth()->id();
        $ticket_update->save();

        return $ticket_update;
    }


    public function sendTicketMail($ticket_id, $is_initial, $is_acknowledgement = 0)
    {
        $auth_user_email = null;
        $user = auth()->user();
    }

    public function getContactByIdOrPhone($phone = null, $id = null)
    {
        $customer = Customer::leftjoin('organization_types', 'customers.organization_type_id', 'organization_types.id')
            ->leftjoin('users', 'customers.user_id', 'users.id')
            ->leftjoin('line_businesses', 'customers.line_business_id', 'line_businesses.id')
            ->leftjoin('order_frequencies', 'customers.order_frequency_id', 'order_frequencies.id')
            ->leftjoin('order_days', 'customers.order_day_id', 'order_days.id');

        if ($phone) {
            $customer = $customer->where('customers.phone', 'LIKE', '%' . $phone . '%');

            if ($customer->count() > 1) {


                $customer = $customer->select(
                    'customers.id',
                    'users.name as created_by',
                    'organization_types.name as organization_type',
                    "order_days.name as order_day",
                    'customers.name as account_name',
                    'customers.account_number',
                    'customers.contact_person',
                    'customers.location',
                    'customers.assigned_to',
                    'customers.phone',
                    'customers.alternate_phone',
                    'customers.email',
                    'line_businesses.name as line_business',
                    'order_frequencies.name as order_frequency',
                    'customers.created_at',
                    'order_days.name as order_frequency'
                )->get();

                return $customer;
            }
        }
        if ($id) {
            $customer = $customer->where('customers.id', $id);
        }
        $customer = $customer->select(
            'customers.id',
            'users.name as created_by',
            'organization_types.name as organization_type',
            "order_days.name as order_day",
            'customers.name as account_name',
            'customers.account_number',
            'customers.contact_person',
            'customers.location',
            'customers.assigned_to',
            'customers.phone',
            'customers.alternate_phone',
            'customers.email',
            'line_businesses.name as line_business',
            'order_frequencies.name as order_frequency',
            'customers.created_at',
            'order_days.name as order_frequency'
        )->first();

        return $customer;
    }


    public function getTicketDetailsById($ticket_id)
    {
        return Ticket::leftjoin('customers', 'tickets.customer_id', 'customers.id')
            ->leftjoin('issue_categories', 'tickets.issue_category_id', 'issue_categories.id')
            ->leftjoin('sources', 'tickets.source_id', 'sources.id')
            ->leftjoin('ticket_dispositions', 'tickets.disposition_id', 'ticket_dispositions.id')
            ->leftjoin('ticket_statuses', 'tickets.ticket_status_id', 'ticket_statuses.id')
            ->leftjoin('users', 'tickets.assigned_to', 'users.id')
            ->where([
                ['tickets.id', $ticket_id],
            ])->select(
                'tickets.*',
                'customers.name as contact',
                'customers.phone',
                'issue_categories.name as issue_category',
                'sources.name as issue_source',
                'ticket_dispositions.name as disposition',
                'ticket_statuses.name as status',
                'users.id as assigned_to_id',
                'users.name as assigned_to'
            )->first();
    }

    public function validatePhone($phone)
    {
        $len = strlen($phone);

        $begin = substr($phone, 0, 1);
        if ($begin == "+") {
            if ($len == 11 || $len == 10 || $len == 12 | $len == 13) {
                return true;
            } else {
                return false;
            }
        } elseif ($len == 9 || $len == 10 || $len == 11 || $len == 12) {
            return true;
        } else {
            return false;
        }
    }

    public static function getFileType($mimeType)
    {

        $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];

        if ($mimeType == "application/pdf") {
            $file = "pdf";
        } elseif ($mimeType == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
            $file = "office";
        } elseif ($mimeType == "text/plain") {
            $file = "text";
        } elseif ($mimeType == "application/octet-stream") {
            $file = "office";
        } elseif ($mimeType == "application/msword") {
            $file = "office";
        } elseif (!in_array($mimeType, $allowedMimeTypes)) {
            $file = "image";
        } else {
            $file = "image";
        }
        return $file;
    }

    /**
     * get the latest uploaded
     * debt collection list id
     */
    public function getLatestDebtCollectionListId()
    {
        $latest_debt_list = DB::table('debt_collection_lists')->orderBy('debt_list_id', 'DESC')->select('debt_list_id')->first();
        return @$latest_debt_list->debt_list_id;
    }
}
