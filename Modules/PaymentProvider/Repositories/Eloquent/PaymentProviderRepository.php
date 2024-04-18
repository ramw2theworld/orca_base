<?php

namespace Modules\PaymentProvider\Repositories\Eloquent;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Modules\PaymentProvider\Models\PaymentProvider;
use Modules\PaymentProvider\Repositories\Contracts\PaymentProviderRepositoryInterface;
use Illuminate\Support\Str;

class PaymentProviderRepository implements PaymentProviderRepositoryInterface
{
    protected $model;
    public function __construct(PaymentProvider $model){
        $this->model = $model;
    }

    public function all(): Collection
    {
        $query = $this->model
            ->select(
                'id', 
                'name', 
                'is_active', 
                'created_at'
            );
    
        return $query->orderBy('id', 'desc')->get();
    }

    public function find(int $id): PaymentProvider
    {
        try{
            return $this->model->whereId($id)->first();
        }
        catch(ModelNotFoundException $exception){
            throw new ModelNotFoundException('Provider not found');
        }
        catch (Exception $exception){
            throw new Exception('Payment Provider not found!');
        }
    }

    public function create(array $data): PaymentProvider
    {
        DB::beginTransaction();
        try{
            $payment_provider = $this->model::create([
                'name' => $data['name'],
                'is_active' => $data['is_active'],

            ]);
            DB::commit();
            return $payment_provider;
        }catch(Exception $ex){
            DB::rollBack();
            $errorCode = is_numeric($ex->getCode()) ? (int)$ex->getCode() : 0;
            throw new \Exception($ex->getMessage(), $errorCode, $ex);
        }
    }

    public function update(array $data, int $id): PaymentProvider
    {
        try {
            $provider = $this->model::where('id', $id)->first();
            if (!$provider) {
                throw new ModelNotFoundException('Provider not found');
            }

            $provider->name = $data['name']??$provider->name;
            $provider->is_active = $data['is_active']??$provider->is_active;

            $provider->save();
            
            return $provider;
        } catch (\Exception $ex) {
            throw new $ex($ex->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            $provider = $this->model::where('id', $id)->first();
            if (!$provider) {
                throw new ModelNotFoundException('Provider not found');
            }

            $provider->delete();
        } 
        catch (\Exception $ex) {
            throw new $ex($ex->getMessage());
        }
    }
}
