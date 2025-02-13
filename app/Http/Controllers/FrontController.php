<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\City;
use App\Models\CarStore;
use App\Models\CarService;
use Illuminate\Http\Request;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\StoreBookingPaymentRequest;
use Termwind\Components\Dd;

class FrontController extends Controller
{
    public function index()
    {
        $cities = City::all();
        $services = CarService::withCount('carStores')->get(); // Menyesuaikan dengan relasi belongsToMany
        return view('front.index', compact('cities', 'services'));
    }

    public function search(Request $request)
    {
        $cityId = $request->input('city_id');
        $serviceTypeId = $request->input('service_type');

        // Pastikan service tersedia sebelum lanjut
        $carService = CarService::find($serviceTypeId);
        if (!$carService) {
            return redirect()->back()->withErrors(['error' => 'Service type tidak ditemukan.']);
        }

        // Cari store berdasarkan service yang tersedia
        $stores = CarStore::whereHas('carServices', function ($query) use ($serviceTypeId) {
            $query->where('car_services.id', $serviceTypeId);
        })
        ->where('city_id', $cityId)
        ->get();

        $city = City::find($cityId);
        session()->put('serviceTypeId', $serviceTypeId);

        return view('front.stores', [
            'stores' => $stores,
            'carService' => $carService,
            'cityName' => $city ? $city->name : 'Unknown City',
        ]);
    }

    public function details(CarStore $carStore)
    {
        $serviceTypeId = session()->get('serviceTypeId');
        $carService = $carStore->carServices()->where('car_services.id', $serviceTypeId)->first();

        if (!$carService) {
            return redirect()->back()->withErrors(['error' => 'Service tidak tersedia untuk toko ini.']);
        }

        return view('front.details', compact('carStore', 'carService'));
    }

    public function booking(CarStore $carStore)
    {
        session()->put('carStoreId', $carStore->id);

        $serviceTypeId = session()->get('serviceTypeId');

        // **Cek apakah serviceTypeId ada sebelum query**
        if (!$serviceTypeId) {
            return redirect()->back()->withErrors(['error' => 'Service belum dipilih.']);
        }

        // Ambil service berdasarkan store dan service ID
        $service = $carStore->carServices()->where('car_services.id', $serviceTypeId)->first();

        // **Cek apakah service ditemukan sebelum lanjut**
        if (!$service) {
            return redirect()->back()->withErrors(['error' => 'Service tidak ditemukan di toko ini.']);
        }

        session()->put('serviceTypeId', $service->id);

        return view('front.booking', compact('carStore', 'service'));
    }

    public function booking_store(StoreBookingRequest $request)
    {
        session()->put('customerName', $request->input('name'));
        session()->put('customerPhoneNumber', $request->input('phone_number'));
        session()->put('customerTimeAt', $request->input('time_at'));

        session()->save();

        // dd(session()->all());

        $serviceTypeId = session()->get('serviceTypeId');
        $carStoreId = session()->get('carStoreId');

        // **Dapatkan slug dari database**
        $carStore = CarStore::find($carStoreId);
        $service = CarService::find($serviceTypeId);

        // **Pastikan data ditemukan sebelum redirect**
        if (!$carStore || !$service) {
            return back()->withErrors(['error' => 'Data toko atau layanan tidak ditemukan.']);
        }

        // **Gunakan slug untuk redirect**
        return redirect()->route('front.booking.payment', [
            'carStore' => $carStore->slug,
            'carService' => $service->slug
        ]);
    }

    public function booking_payment(CarStore $carStore, CarService $carService) {
        //  dd(session()->all());
        session()->keep(['customerName', 'customerPhoneNumber', 'customerTimeAt']);
        // Simpan kembali session agar tetap aktif
        session()->put('customerName', session('customerName'));
        session()->put('customerPhoneNumber', session('customerPhoneNumber'));
        session()->put('customerTimeAt', session('customerTimeAt'));

        $price = $carService->price;
        $bookingFee = 25000;
        $ppn = 0.11 * $price;
        $grandTotal = $price + $bookingFee + $ppn;

        session()->put('totalAmount', $grandTotal);
        return view('front.payment', compact('carStore', 'carService', 'price', 'bookingFee', 'ppn', 'grandTotal'));
    }


    public function booking_payment_store(StoreBookingPaymentRequest $request)
    {
        //    dd(session()->all());
        // // dd('Masuk ke booking_payment_store');
        // // dd($request->all());


        // Ambil data dari session
        $customerName = session()->get('customerName');
        $customerPhoneNumber = session()->get('customerPhoneNumber');
        $customerTimeAt = session()->get('customerTimeAt');
        $serviceTypeId = session()->get('serviceTypeId');
        $carStoreId = session()->get('carStoreId');
        $totalAmount = session()->get('totalAmount');

        $bookingTransactionId = null;

        DB::transaction(function () use (
            $request,
            $customerName,
            $customerPhoneNumber,
            $customerTimeAt,
            $serviceTypeId,
            $carStoreId,
            $totalAmount,
            &$bookingTransactionId,
        ) {
            $validated = $request->validated();

            if ($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }

            $validated['name'] = $customerName;
            $validated['total_amount'] = $totalAmount;
            $validated['phone_number'] = $customerPhoneNumber;
            $validated['started_at'] = Carbon::tomorrow()->format('Y-m-d');
            $validated['time_at'] = $customerTimeAt;
            $validated['car_service_id'] = $serviceTypeId;
            $validated['car_store_id'] = $carStoreId;
            $validated['is_paid'] = false;
            $validated['trx_id'] = BookingTransaction::generateUniqueTrxId();

            $newBooking = BookingTransaction::create($validated);

            $bookingTransactionId = $newBooking->id; // Simpan ID transaksi
        });

        // Pastikan bookingTransactionId tidak null sebelum redirect
        if ($bookingTransactionId) {
            return redirect()->route('front.success.booking', $bookingTransactionId);
        } else {
            dd('error', 'Failed to process booking, please try again.');
        }
    }

public function succes_booking(BookingTransaction $bookingTransaction)
    {
    return view('front.success_booking', compact('bookingTransaction'));
    }


}
