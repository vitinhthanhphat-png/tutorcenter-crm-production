<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cashbook extends Model
{
    use BelongsToTenant;

    protected $table = 'cashbook';

    protected $fillable = [
        'tenant_id', 'branch_id', 'type', 'category', 'description',
        'amount', 'transaction_date', 'reference', 'recorded_by', 'note',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'integer',
    ];

    public static function categories(): array
    {
        return [
            'income'  => ['Học phí', 'Bán sách/tài liệu', 'Phí đăng ký', 'Thu khác'],
            'expense' => ['Lương', 'Điện nước', 'Thuê mặt bằng', 'Văn phòng phẩm', 'Sửa chữa', 'Marketing', 'Chi khác'],
        ];
    }

    public function tenant(): BelongsTo   { return $this->belongsTo(Tenant::class); }
    public function branch(): BelongsTo   { return $this->belongsTo(Branch::class); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
