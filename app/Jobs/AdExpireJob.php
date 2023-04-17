<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Ads;
use App\Common\Status;
use App\Mail\AdsExpiry;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AdExpireJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $expireAds=Ads::where('delete_status', '!=', Status::DELETE)->where('status', Status::ACTIVE)->get();

        foreach($expireAds as $expiread){

            $date = $expiread->start_at;
            
           $diff = now()->diffInDays(Carbon::parse($date));
           $catExp=0;
           if($expiread->Category->expire_days!=null){
            $catExp=$expiread->Category->expire_days;
           }
           if($diff>$expiread->Category->expire_days){
            $expiread->status=Status::INACTIVE;
            $expiread->update();
           }
           if($catExp-$diff==5){
            
            Mail::to($expiread->User->email)->send(new AdsExpiry);

           }
        }
    }
}
