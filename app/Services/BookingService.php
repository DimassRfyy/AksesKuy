<?php

namespace App\Services;

use App\Models\GroupParticipant;
use App\Models\Product;
use App\Models\ProductSubscription;
use App\Models\ProductTestimonial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class BookingService
{
    public function getBookingDetails(array $validated)
    {
        return ProductSubscription::where('booking_trx_id', $validated['booking_trx_id'])
            ->where('phone', $validated['phone'])
            ->first();
    }

    protected function calculateBookingData(Product $product, $validatedData)
    {
        $ppn = 0.12;
        $price = $product->price_per_person;
        $totalPpn = $ppn * $price;
        $totalAmount = $price + $totalPpn;

        return [
            'product_id' => $product->id,
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'duration' => $product->duration,
            'price_per_person' => $price,
            'sub_total' => $price,
            'total_ppn' => $totalPpn,
            'total_amount' => $totalAmount,
        ];
    }

    public function storeBookingInSession($product, $validatedData)
    {
        $bookingData = $this->calculateBookingData($product, $validatedData);
        Session::put('booking_data', $bookingData);
    }

    public function payment()
    {
        $booking = session('booking_data', []);

        if (empty($booking)) {
            Log::error('No booking data found in session.');
            return [];
        }

        $product = Product::find($booking['product_id']);

        return compact('booking', 'product');
    }

    public function paymentStore(array $validated)
    {
        $bookingData = Session::get('booking_data');

        if (!$bookingData) {
            Log::error('No booking data found in session.');
            return null;
        }

        return DB::transaction(function () use ($validated, $bookingData) {
            if (isset($validated['proof'])) {
                $proofPath = $validated['proof']->store('proofs', 'public');
                $validated['proof'] = $proofPath; // e.g., proofs/bukitransfer.png  
            }

            $validated = array_merge($validated, [
                'name' => $bookingData['name'],
                'email' => $bookingData['email'],
                'phone' => $bookingData['phone'],
                'duration' => $bookingData['duration'],
                'total_tax_amount' => $bookingData['total_ppn'],
                'price' => $bookingData['price_per_person'],
                'total_amount' => $bookingData['total_amount'],
                'product_id' => $bookingData['product_id'],
                'is_paid' => false,
            ]);

            $newBooking = ProductSubscription::create($validated);

            return $newBooking->id;
        });
    }

    public function getBookingDetailsWithGroupAndCapacity(array $validatedData)
    {
        $bookingDetails = ProductSubscription::with(['product'])
            ->where('booking_trx_id', $validatedData['booking_trx_id'])
            ->where('phone', $validatedData['phone'])
            ->first();

        if (!$bookingDetails) {
            return null;
        }

        $group = GroupParticipant::where('booking_trx_id', $bookingDetails->booking_trx_id)->first();
        $subscriptionGroup = null;

        if ($group) {
            $subscriptionGroup = $group->subscriptionGroup()
                ->with(['groupParticipants', 'groupMessages'])
                ->first();
        }

        $totalParticipants = 0;
        if ($subscriptionGroup && $subscriptionGroup->groupParticipants) {
            $totalParticipants = $subscriptionGroup->groupParticipants->count();
        }

        $productCapacity = $bookingDetails->product->capacity ?? 0;
        $remainingSlots = $productCapacity - $totalParticipants;

        $testimonialExists = ProductTestimonial::where('customer_booking_trx_id', $bookingDetails->booking_trx_id)->exists();

        return [
            'bookingDetails' => $bookingDetails,
            'subscriptionGroup' => $subscriptionGroup,
            'productCapacity' => $productCapacity,
            'totalParticipants' => $totalParticipants,
            'remainingSlots' => $remainingSlots,
            'testimonialExists' => $testimonialExists,
        ];
    }

    public function createTestimonial(array $validated)
    {
        $validated['is_publish'] = false;
        if (isset($validated['photo'])) {
            $photoPath = $validated['photo']->store('testimonials', 'public');
            $validated['photo'] = $photoPath; // e.g., testimonials/testimonial1.png
        } else {
            $validated['photo'] = 'assets/images/photos/photo-3.png';
        }
        return DB::transaction(function () use ($validated) {
            $testimonial = ProductTestimonial::create($validated);

            return $testimonial;
        });
    }
}
