<?php

namespace App\Console\Commands;

use App\Models\RefreshToken;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredRefreshTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa các refresh token đã hết hạn khỏi csdl';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedCount = RefreshToken::where('expires_at', '<', Carbon::now()->delete());  
        $this->info("Đã xóa {$deletedCount} refresh token hết hạn.");
    }
}
