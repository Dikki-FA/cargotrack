<?php

namespace App\Http\Requests;

use App\Models\Shipment;
//use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Shipment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_phone' => ['required', 'string', 'max:20'],
            'sender_address' => ['required', 'string', 'max:255'],
            'origin_city' => ['required', 'string', 'max:255'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:20'],
            'receiver_address' => ['required', 'string', 'max:255'],
            'destination_city' => ['required', 'string', 'max:255'],
            'weight_kg' => ['required', 'numeric', 'min:0.1', 'max:999999.99'],
            'item_type' => ['required', 'string', 'max:255'],
            'assigned_courier_id' => ['nullable', 'integer', 'exists:couriers,id'],
        ];
    }
}
