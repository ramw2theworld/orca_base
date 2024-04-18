<?php

namespace Modules\PaymentProvider\Repositories\Eloquent;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Modules\PaymentProvider\Models\Currency;
use Modules\PaymentProvider\Repositories\Contracts\CurrencyRepositoryInterface;
use Illuminate\Support\Str;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    protected $model;
    public function __construct(Currency $model){
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->get();
        
        $query = $this->model
            ->select(
                'id', 
                'code', 
                'symbol', 
                'created_at'
            );
    
        return $query->orderBy('id', 'desc')->get();
    }

    public function find(int $id): Currency
    {
        try{
            $currency = $this->model->whereId($id)->first();
            if(!$currency){
                throw new ModelNotFoundException('Currency not found');
            }
            return $currency;
        }
        catch(ModelNotFoundException $exception){
            throw new ModelNotFoundException('Currency not found');
        }
        catch (Exception $exception){
            throw new Exception('Something went wrong. Please try again!');
        }
    }

    public function create(array $data): Currency
    {
        DB::beginTransaction();
        try{
            $currency = $this->model::create([
                'code' => Str::lower($data['code']),
                'symbol' => $data['symbol'],
            ]);
            DB::commit();
            return $currency;
        }catch(Exception $ex){
            DB::rollBack();
            $errorCode = is_numeric($ex->getCode()) ? (int)$ex->getCode() : 0;
            throw new \Exception($ex->getMessage(), $errorCode, $ex);
        }
    }

    public function update(array $data, int $id): Currency
    {
        try {
            $currency = $this->model::where('id', $id)->first();
            if (!$currency) {
                throw new ModelNotFoundException('Currency not found');
            }

            $currency->code = $data['code']??$currency->code;
            $currency->symbol = $data['symbol']??$currency->symbol;

            $currency->save();
            
            return $currency;
        } catch (\Exception $ex) {
            throw new $ex($ex->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            $currency = $this->model::where('id', $id)->first();
            if (!$currency) {
                throw new ModelNotFoundException('Currency not found');
            }

            $currency->delete();
        } 
        catch (\Exception $ex) {
            throw new $ex($ex->getMessage());
        }
    }
}
