<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class everyMinute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description minuteUpdate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $now_date = \Carbon\Carbon::today()->toDateString();
        
        $users = DB::table('users')
                            ->whereDate('sendmail', '<', date('Y-m-d'))
                            ->orWhereNull('sendmail')
                            ->where('is_register','=',1)
                            ->skip(0)->take(5)
                            ->orderBy('id','desc')
                            ->get();

        foreach ($users as $key => $value) {

            sendEmail('thongbaothithu', array('name'=>ucwords($value->name), 'to_email' => $value->email));

            DB::table('users')->where('id', '=', $value->id)->update([
                    'sendmail' => $now_date
            ]);
        }


        // DB::table('test')->delete();
        // echo "delete table test";
    }
}
