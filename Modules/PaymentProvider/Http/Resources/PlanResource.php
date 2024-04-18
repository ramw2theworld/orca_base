<?php

namespace Modules\PaymentProvider\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\PaymentProvider\Models\PaymentProvider;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'plan_code' => $this->plan_code,
            'amount_trial' => $this->amount_trial,
            'amount_premium' => $this->amount_premium,
            'provider_plan_id' => $this->provider_plan_id,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'paymentProvider' => new PaymentProviderResource($this->whenLoaded('paymentProvider')),

        ];
    }
}
