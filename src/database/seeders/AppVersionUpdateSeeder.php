<?php

namespace Database\Seeders;

use App\Models\Tenant\Sales\Cash\CashRegister;
use App\Models\Tenant\Sales\Cash\CashRegisterLog;
use App\Repositories\Core\Status\StatusRepository;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Database\Seeders\UpdateSeeder\RolePermissionUpdateSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class AppVersionUpdateSeeder extends Seeder
{
    use TruncateTable, DisableForeignKeys;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        activity()->withoutLogs(function () {
            $this->call(RolePermissionUpdateSeeder::class);

            $counter_open_status_id = resolve(StatusRepository::class)->counterOpen();
            $cash_counter_open_status_id = resolve(StatusRepository::class)->cash_counterOpen();
            CashRegister::query()->where('status_id', $counter_open_status_id)->update(['status_id' => $cash_counter_open_status_id]);
            CashRegisterLog::query()->where('status_id', $counter_open_status_id)->update(['status_id' => $cash_counter_open_status_id]);

            $counter_close_status_id = resolve(StatusRepository::class)->counterClose();
            $cash_counter_close_status_id = resolve(StatusRepository::class)->cash_counterClose();
            CashRegister::query()->where('status_id', $counter_close_status_id)->update(['status_id' => $cash_counter_close_status_id]);
            CashRegisterLog::query()->where('status_id', $counter_close_status_id)->update(['status_id' => $cash_counter_close_status_id]);
        });

        Model::reguard();
    }
}
