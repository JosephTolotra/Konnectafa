<?php
namespace App\TimeController;

use Carbon\Carbon;

class TimeController
{
    public function transformTime(Carbon $datetime){
        $now=Carbon::now();
        
        $datetime->locale('fr_FR');

        //differnce refa 2 jours
        if($datetime->diffInDays($now,false)<2){
            //difference de deux heurs 
               if($datetime->diffInHours($now,false)<2){
                $date=$datetime->diffForHumans();                
                }
                else{
                    $date=$datetime->calendar();

                }          
        } 
        else{
            $date=$datetime->isoFormat('ddd LT');
        }
  
        return $date;
    }
}