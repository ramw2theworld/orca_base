<?php

namespace Modules\PaymentProvider\Repositories\Eloquent;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Modules\PaymentProvider\Models\Plan;
use Modules\PaymentProvider\Repositories\Contracts\PlanRepositoryInterface;
use Illuminate\Support\Str;

class PlanRepository implements PlanRepositoryInterface
{
    protected $model;
    public function __construct(Plan $model){
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['paymentProvider', 'currency'])->orderBy('id', 'desc')->get();
    }

    public function find($id): Plan
    {
        try{
            $plan = $this->model->with(['paymentProvider', 'currency'])->whereId($id)->first();
            if (!$plan) {
                throw new ModelNotFoundException('Plan not found');
            }
            return $plan;
        }
        catch(ModelNotFoundException $exception){
            throw new ModelNotFoundException('Plan not found');
        }
        catch (Exception $exception){
            throw new Exception('Something went wrong. Please try again!');
        }
    }

    public function create(array $data): Plan
    {
        DB::beginTransaction();
        try{
            $plan = $this->model::create([
                'name' => $data['name'],
                'plan_code' => Str::lower($data['plan_code'], '-'),
                'amount_trial' => $data['amount_trial'],
                'amount_premium' => $data['amount_premium'],
                'provider_plan_id' => $data['provider_plan_id'],
                'payment_provider_id' => $data['payment_provider_id'],
                'currency_id' => $data['currency_id'],
            ]);
            DB::commit();
            return $plan;
        }catch(Exception $ex){
            DB::rollBack();
            $errorCode = is_numeric($ex->getCode()) ? (int)$ex->getCode() : 0;
            throw new \Exception($ex->getMessage(), $errorCode, $ex);
        }
    }

    public function update(array $data, int $id): Plan
    {
        try {
            $plan = $this->model::where('id', $id)->with(['paymentProvider', 'currency'])->first();
            if (!$plan) {
                throw new ModelNotFoundException('Plan not found');
            }
            if($data['plan_code']){
                $plan_code = Str::lower($data['plan_code']);
            }else{
                $plan_code = $plan->plan_code;
            }

            $plan->name = $data['name']??$plan->name;
            $plan->plan_code = $plan_code;
            $plan->amount_trial = $data['amount_trial']??$plan->amount_trial;
            $plan->amount_premium = $data['amount_premium']??$plan->amount_premium;
            $plan->provider_plan_id = $data['provider_plan_id']??$plan->provider_plan_id;
            $plan->payment_provider_id = $data['payment_provider_id']??$plan->payment_provider_id;
            $plan->currency_id = $data['currency_id']??$plan->currency_id;

            $plan->save();
            
            return $plan;
        } catch (\Exception $ex) {
            throw new $ex($ex->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            $plan = $this->model::where('id', $id)->first();
            if (!$plan) {
                throw new ModelNotFoundException('Plan not found');
            }
            $plan->delete();
        } catch (\Exception $ex) {
            throw new $ex($ex->getMessage());
        }
        
    }
}
