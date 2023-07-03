<?php


namespace Adminx\Common\Libs\Helpers;


use Adminx\Common\Models\Custom\MailTo;
use Adminx\Common\Models\Users\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailHelper extends Mail
{
    /**
     * @param string|array|Collection|User|MailTo $to
     *
     * @return \Illuminate\Mail\PendingMail|void
     */
    public static function to($to)
    {
        $mailto = self::TraitTo($to);

        return parent::to($mailto);
    }

    /**
     * @param string|array|Collection|User|MailTo $to
     *
     * @return \Illuminate\Mail\PendingMail|void
     */
    public static function bcc($to)
    {
        $mailto = self::TraitTo($to);

        parent::bcc($mailto);
    }

    /**
     * @param $itens
     *
     * @return Collection
     */
    public static function MailToFromArray($itens){

        $retorno = collect();

        foreach ($itens as $item) {
            if(filter_var($item, FILTER_VALIDATE_EMAIL)) {
                $retorno->push(new MailTo($item));
            }
        }

        return $retorno;


    }

    /**
     * @param string|array|Collection|User|MailTo $to
     *
     * @return array|Collection
     */
    private static function TraitTo($to){
        $mailto = collect();

        //Tratar to
        if(is_string($to)){
            //Caso for string

            if(Str::contains($to, ",")){
                //Se tiver virgula, explodir e adicionar
                $to_list = explode(",", $to);
                $mailto = self::MailToFromArray($to_list);

            }else{
                //Se não, apenas adicionar
                $to_item = new MailTo($to);
                $mailto->push($to_item);
            }

        }elseif(is_array($to)){

            //Se for um array, verifica se é um item unico ou varios tos

            if(is_string($to[0])){
                //Se for um array de emails
                $mailto = self::MailToFromArray($to);

            }else{
                //Se for um conjunto de tos, users ou objetos
                $mailto = $to;
            }

        }else{

            //Se for um outro objeto
            $mailto = $to;
        }

        return $mailto;
    }
}
