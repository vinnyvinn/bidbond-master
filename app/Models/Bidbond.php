<?php

namespace App\Models;

use App\Services\GatewayService;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Bidbond extends Model
{
    protected $fillable = ['tender_no','purpose','addressee','effective_date','amount','currency','period','company_id','counter_party_id','charge',
    'template_secret','secret','reference','paid','agent_id','expiry_date','created_by','expired_at','deal_date','created_at', 'updated_at'];

    protected $appends = ['bid_template','bid_company'];
    protected $hidden = ['deleted_at']; //, 'expires_at'

    protected $dates = ['created_at', 'updated_at', 'effective_date', 'expiry_date'];

    protected $casts = [
        'effective_date' => 'datetime:Y-m-d',
        'expiry_date' => 'datetime:Y-m-d',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function secret($secret)
    {
        return self::where('secret', $secret);
    }
    public static function tender($tender)
    {
        return self::where('tender_no', $tender);
    }

    /**
     * @return BelongsTo
     */
    public function counterparty()
    {
        return $this->belongsTo(CounterParty::class, 'counter_party_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }

    public function bidbondtemplate()
    {
        return $this->belongsTo(BidBondTemplate::class);
    }

    /**
     * @param $value
     */
    public function setNumberAttribute($value)
    {
        $this->attributes['tender_no'] = (empty($value) || strlen($value) == 0) ? $value : ucwords($value);
    }

    /**
     * @param $value
     */
    public function setPurposeAttribute($value)
    {
     $this->attributes['purpose'] = (empty($value) || strlen($value) == 0) ? $value : ucwords($value);
    }

    /**
     * @param $value
     */
    public function setAddresseeAttribute($value)
    {
        $this->attributes['addressee'] = (empty($value) || strlen($value) == 0) ? $value : ucwords($value);
    }

    public function scopeDue($builder): void
    {
        $builder->where("expiry_date", "<", Carbon::today());
    }

    public function scopeActive($builder): void
    {
        $builder->whereNull("expired_at");
    }

    public function scopePaid($builder): void
    {
      $builder->where("paid", 1);
    }

    public function scopeReference($builder, $value): void
    {
        $builder->where("reference", $value);
    }
    public function scopeCurrency($builder, $value): void
    {
        $builder->where("currency", $value);
    }
    public function getBidTemplateAttribute(){
        return BidBondTemplate::where('secret',$this->template_secret)->select('secret','name')->first();
    }
    public function getBidCompanyAttribute(){
        return Company::where('id',$this->company_id)->select('name','balance','limit')->first();
    }

}
