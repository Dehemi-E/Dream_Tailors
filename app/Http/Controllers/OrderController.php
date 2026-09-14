<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http; 

class OrderController extends Controller
{
    public function create()
    {
        $customers = DB::table('customers')->orderBy('id', 'desc')->get();
        return view('place-order', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'garment_category' => 'required|string',
            'specific_garment' => 'required|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('design_image')) {
            $file = $request->file('design_image');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('order_designs'), $filename);
            $imagePath = '/order_designs/' . $filename;
        }

        DB::table('orders')->insert([
            'customer_id' => $request->customer_id,
            'garment_category' => $request->garment_category,
            'garment_name' => $request->specific_garment,
            'fabric_source' => $request->fabric_source,
            'fabric_name' => $request->fabric_name,
            'collar' => $request->collar,
            'chest' => $request->chest,
            'shoulder' => $request->shoulder,
            'sleeve_length' => $request->sleeve_length,
            'full_length' => $request->full_length,
            'armhole' => $request->armhole,
            'upper_waist' => $request->upper_waist,
            'lower_waist' => $request->lower_waist,
            'hip' => $request->hip,
            'thigh' => $request->thigh,
            'outseam' => $request->outseam,
            'inseam' => $request->inseam,
            'bottom_hem' => $request->bottom_hem,
            'knee' => $request->knee,
            'styling_notes' => $request->styling_notes,
            'design_image' => $imagePath,
            'status' => 'Measuring',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/order-management')->with('success', 'Order placed successfully!');
    }

    public function index()
    {
        $orders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('fabrics', 'orders.fabric_name', '=', 'fabrics.name')
            ->whereNotIn('orders.status', ['Completed', 'completed', 'COMPLETED'])
            ->select('orders.*', 'customers.name as customer_name', 'customers.tier as customer_tier', 'fabrics.image as shop_fabric_image')
            ->orderBy('orders.id', 'desc')
            ->get();
        
        $completedOrders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('fabrics', 'orders.fabric_name', '=', 'fabrics.name')
            ->whereIn('orders.status', ['Completed', 'completed', 'COMPLETED'])
            ->select('orders.*', 'customers.name as customer_name', 'customers.tier as customer_tier', 'fabrics.image as shop_fabric_image')
            ->orderBy('orders.updated_at', 'desc')
            ->get()
            ->groupBy('customer_id');

        return view('order-management', compact('orders', 'completedOrders'));
    }

    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);
        $status = $data['status'] ?? 'Processing';
        
        DB::table('orders')->where('id', $id)->update([
            'status' => $status,
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        
        if ($order) {
            $checkStatus = strtolower($status); 
            
            if ($checkStatus == 'processing') {
                $this->sendPushNotification($order->customer_id, 'Order Update', 'Your order is starting to process!', $order->id, 'false');
            } elseif ($checkStatus == 'ready') {
                $this->sendPushNotification($order->customer_id, 'Order Finished', 'Your order is ready to collect!', $order->id, 'false');
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DB::table('orders')->where('id', $id)->delete();
        return redirect('/order-management')->with('success', 'Order deleted!');
    }

    public function getCustomerMeasurements($id)
    {
        $latestOrder = DB::table('orders')
            ->where('customer_id', $id)
            ->orderBy('id', 'desc')
            ->first([
                'collar', 'chest', 'shoulder', 'sleeve_length', 'full_length', 
                'armhole', 'upper_waist', 'lower_waist', 'hip', 'thigh', 
                'outseam', 'inseam', 'bottom_hem', 'knee'
            ]);

        if ($latestOrder) {
            return response()->json($latestOrder);
        }

        return response()->json(null);
    }

    // ---------------- FABRIC MANAGEMENT ---------------- //

    public function getFabrics() {
        $count = DB::table('fabrics')->count();
        
        if ($count === 0) {
            $defaultFabrics = [
                ['name' => 'Premium Linen', 'image' => 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=200&h=200&fit=crop'],
                ['name' => 'Egyptian Cotton', 'image' => 'https://images.unsplash.com/photo-1598033129183-c4f50d1a4b0e?w=200&h=200&fit=crop'],
                ['name' => 'Wool Blend', 'image' => 'https://images.unsplash.com/photo-1603251579431-8041402bdeda?w=200&h=200&fit=crop'],
                ['name' => 'Raw Silk', 'image' => 'https://images.unsplash.com/photo-1589310243389-96a5483213a8?w=200&h=200&fit=crop']
            ];
            foreach($defaultFabrics as $df) {
                DB::table('fabrics')->insert([
                    'name' => $df['name'],
                    'image' => $df['image'],
                    'in_stock' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $fabrics = DB::table('fabrics')->orderBy('id', 'desc')->get();
        return response()->json($fabrics);
    }

    public function storeFabric(Request $request) {
        $imagePath = '';
        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('fabrics_images'), $filename);
            $imagePath = '/fabrics_images/' . $filename;
        }
        
        $id = DB::table('fabrics')->insertGetId([
            'name' => $request->name,
            'image' => $imagePath,
            'in_stock' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true, 'id' => $id, 'image' => $imagePath]);
    }

    public function updateFabricImage(Request $request, $id) {
        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('fabrics_images'), $filename);
            $imagePath = '/fabrics_images/' . $filename;
            
            DB::table('fabrics')->where('id', $id)->update([
                'image' => $imagePath,
                'updated_at' => now()
            ]);
            return response()->json(['success' => true, 'image' => $imagePath]);
        }
        return response()->json(['success' => false]);
    }

    public function toggleFabricStock($id) {
        $fabric = DB::table('fabrics')->where('id', $id)->first();
        if($fabric){
            DB::table('fabrics')->where('id', $id)->update(['in_stock' => !$fabric->in_stock]);
        }
        return response()->json(['success' => true]);
    }

    public function deleteFabric($id) {
        DB::table('fabrics')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // ========================================== //
    // 💬 MESSAGING & SUGGESTIONS SYSTEM
    // ========================================== //

    public function getUnreadCount($user_type, $user_id = 0) {
        if ($user_type === 'admin') {
            $count = DB::table('order_messages')->where('sender', 'customer')->where('is_read', false)->count();
        } else {
            $count = DB::table('order_messages')->where('sender', 'admin')->where('customer_id', $user_id)->where('is_read', false)->count();
        }
        return response()->json(['count' => $count]);
    }

    public function getAdminChats()
    {
        $chats = \Illuminate\Support\Facades\DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('order_messages', 'orders.id', '=', 'order_messages.order_id')
            ->select(
                'orders.id as order_id',
                'orders.garment_name',
                'customers.id as customer_id',
                'customers.name as customer_name'
            )
            // 🟢 මෙතන තිබ්බ whereNotIn ('Completed', 'finished' අයින් කරන) එක සම්පූර්ණයෙන්ම අයින් කළා. 
            // දැන් ඕනෑම State එකකදී Chat එක Admin ට පේනවා.
            ->distinct()
            ->get();

        foreach ($chats as $chat) {
            $chat->unread_count = \Illuminate\Support\Facades\DB::table('order_messages')
                ->where('order_id', $chat->order_id)
                ->where('sender', 'customer')
                ->where('is_read', 0)
                ->count();
        }

        return response()->json($chats);
    }

    public function getOrderMessages($order_id) {
        $messages = DB::table('order_messages')
            ->where('order_id', $order_id)
            ->orderBy('created_at', 'asc')
            ->get();
        return response()->json($messages);
    }

    public function sendMessage(Request $request) {
        $imagePath = null;
        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('message_images'), $filename); 
            $imagePath = '/message_images/' . $filename;
        }

        DB::table('order_messages')->insert([
            'order_id' => $request->order_id,
            'customer_id' => $request->customer_id,
            'sender' => $request->sender,
            'message' => $request->message ?? '',
            'image' => $imagePath,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($request->sender === 'admin') {
            $order = DB::table('orders')->where('id', $request->order_id)->first();
            if ($order) {
                $this->sendPushNotification(
                    $order->customer_id, 
                    'New Message from Master Tailor', 
                    'Admin has replied to your order message.', 
                    $request->order_id,
                    'true'
                );
            }
        }

        return response()->json(['success' => true]);
    }

    public function markMessagesAsRead(Request $request) {
        DB::table('order_messages')
            ->where('order_id', $request->order_id)
            ->where('sender', $request->reader_type === 'customer' ? 'admin' : 'customer')
            ->update(['is_read' => true, 'updated_at' => now()]);
            
        return response()->json(['success' => true]);
    }

    private function sendPushNotification($customerId, $title, $body, $orderId = '', $isMessage = 'false')
    {
        $customer = DB::table('customers')->where('id', $customerId)->first();
        
        if ($customer && $customer->fcm_token) {
            try {
                $path = storage_path('app/firebase-credentials.json');
                
                if (!file_exists($path)) {
                    \Illuminate\Support\Facades\Log::error('Firebase credentials file missing.');
                    return;
                }

                $client = new \Google_Client();
                $client->setAuthConfig($path);
                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                $token = $client->fetchAccessTokenWithAssertion()['access_token'];

                $response = Http::withToken($token)->post('https://fcm.googleapis.com/v1/projects/tailors-559ab/messages:send', [
                    'message' => [
                        'token' => $customer->fcm_token,
                        'notification' => [
                            'title' => (string)$title,
                            'body' => (string)$body,
                        ],
                        'data' => [
                            'order_id' => (string)$orderId,
                            'is_message' => (string)$isMessage
                        ],
                        'webpush' => [
                            'fcm_options' => [
                                'link' => url('/customer/dashboard?open_chat=' . $orderId)
                            ]
                        ]
                    ]
                ]);
                
                if (!$response->successful()) {
                    \Illuminate\Support\Facades\Log::error('FCM API Error: ' . $response->body());
                }

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('FCM Send Error: ' . $e->getMessage());
            }
        } else {
            \Illuminate\Support\Facades\Log::error('FCM Token not found for customer ID: ' . $customerId);
        }
    }
}