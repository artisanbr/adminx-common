<?php
/**
 * Created by PhpStorm.
 * User: renalcio
 * Date: 07/10/16
 * Time: 14:54
 */

namespace Adminx\Common\Libs\Helpers;


use Adminx\Common\Models\Dia;
use Carbon\Carbon;
use Illuminate\Support\Debug\Dumper;
use Illuminate\Support\Str;

class DateTimeHelper
{
    /**
     * @param      $value
     * @param null $from_format
     * @param null $to_format
     *
     * @return Carbon|null
     */
    public static function DBTrait($value, $from_format = null, $to_format = null){

        if(!empty($value) && !Str::contains($value,"null")) {

            if(empty($from_format)){
                $from_format = self::getFromFormat($value);
            }

            if(empty($to_format)) {
                $to_format = self::getToFormat($value);
            }

            if (Str::contains($value, '/')) {
                return Carbon::createFromFormat($from_format, $value);
            }
            else if (Str::contains($value, '-')) {
                return Carbon::parse($value);
            }
            else if(is_int($value)) {
                return Carbon::createFromTimestamp($value);
            }
            else if(is_object($value) && get_class($value) == Carbon::class) {
                return $value;
            }
            else {
                return Carbon::parse($value);
            }
        }else{
            return NULL;
        }

    }

    public static function isFeriado($value){
        $value = self::DBTrait($value);

        $mes = intval($value->format("m"));
        $dia = intval($value->format("d"));

        return isset(config("pagamentos.faturas.feriados")[$mes]) && config("pagamentos.faturas.feriados")[$mes]->contains($dia);
    }

    private static function getFromFormat($value){

        if(Str::contains($value, "/")){
            return Str::contains($value, ":") ? "d/m/Y H:i:s" : "d/m/Y";
        }

        return null;
    }

    private static function getToFormat($value){

        if(Str::contains($value, "/")){
            return Str::contains($value, ":") ? "Y-m-d H:i:s" : "Y-m-d";
        }

        return null;
    }

    public static function dateBrToUs($date)
    {
        return Carbon::createFromFormat("d/m/Y", $date)->format('Y-m-d');
    }

    public static function checkDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }


    public static function DiaSemana($id){
        return config("agenda.dias_semana")[$id];
    }

    public static function GetDiaSemana($data){
        $num_semana = date("w", strtotime($data));

        return self::DiaSemana($num_semana);
    }

    public static function GetDiaSemanaId($data){
        return date("w", strtotime($data));
    }
}
