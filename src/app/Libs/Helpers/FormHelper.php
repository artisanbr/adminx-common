<?php
/**
 * Created by PhpStorm.
 * User: renalcio
 * Date: 26/12/17
 * Time: 10:42
 */

namespace ArtisanBR\Adminx\Common\App\Libs\Helpers;


use ArtisanBR\Adminx\Common\App\Libs\Support\Str;

class FormHelper
{

    public static function prefix($input, $prefixo = ""){

        return (empty($prefixo)) ? $input : "{$prefixo}[{$input}]";
    }

    public static function getFieldName($attribute, $prefix = null){


        if(Str::contains($attribute, ['[',']'])){
            //Padrão normal

            //Remover ']'
            $attribute = Str::replace(']', '', $attribute);

            //Explodir em '['
            $attributeArray = collect(explode('[', $attribute))->filter();

        }else if(Str::contains($attribute, '.')){
            $attributeArray = collect(explode('.', $attribute))->filter();
        }else{
            return $prefix ? "{$prefix}[{$attribute}]" : $attribute;
        }

        return ($prefix ?? '') . $attributeArray->reduce(fn($carry, $item) => !$carry && !$prefix ? $item : $carry."[{$item}]");
    }

    public static function labelFromName($name = null){

        if(!$name){
            return null;
        }

        $label = $name;

        $name = str_replace('_', '.', self::GetInputId($name));
        $name_trans = "validation.attributes.".last(explode('.', $name));
        $label = self::FormLabel($label);
        $label_trans = "validation.attributes.".$label;

        if(trans($name_trans) !== $name_trans){
            $label = trans("validation.attributes.".$name_trans);
        }else if(trans($label_trans) != $label_trans) {
            $label = trans($label_trans);
        }

        return Str::ucfirst($label);
    }

    public static function FormLabel($label){
        $rgx = '/(.+\[)(.+)(\])/mi';

        $result = preg_replace($rgx, '$2', $label);

        return ($result) ? $result : $label;
    }

    public static function GetInputId($name){
        $re = '/(.+)\[(.+)\]/mi';

        if(preg_match($re, $name)){
            //attr[id]
            $name = Str::replace('[]', '', $name);
            return Str::replace(['[','-'], '_', Str::replace(']', '', $name));
        }

        return $name;
    }

    public static function listToSelectArray($list, $value_field, $text_field, $selecione = null){
        if(!empty($list)){
            $retorno = is_null($selecione) ? ["" => "Selecione..."] : (is_array($selecione) ? $selecione : []);
            foreach($list as $item){
                $value = $item->{$value_field};
                $text = $item->{$text_field};
                $retorno[$value] = $text;
            }

            return $retorno;
        }else return null;
    }
}
