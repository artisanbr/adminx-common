<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Http\Responses;

//use App\Http\Resources\ApiResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Response;
use JetBrains\PhpStorm\ArrayShape;

class ApiResponse
{
    //protected $resourceClass = ApiResource::class;

    public string|Route|null $redirect_to = null;

    public array $errors;

    public function __construct(
        public $result = true,
        public $data = [],
        public $message = [],
        public $status = 200) {}

    public static function success($data = [], $message = [], $status = 200): self
    {
        return (new static(true, $data, $message, $status));
    }

    public static function error($data = [], $message = '', $errors = [], $status = 500): self
    {
        return (new static(false, $data, $message, $status))->errors($errors);
    }

    public static function from($data, $message = []): self
    {
        return (new static(true, $data, $message));
    }

    public function status($status = 200): static
    {
        $this->status = $status;

        return $this;
    }

    public function errors($errors): static
    {
        $this->errors = $errors;

        return $this;
    }

    public function message($message = 200): static
    {
        $this->message = $message;

        return $this;
    }

    public function redirectTo($redirect): static
    {
        $this->redirect_to = $redirect;

        return $this;
    }

    public function data($data = []): static
    {
        $this->data = $data;

        return $this;
    }

    #[ArrayShape(['result' => "bool", 'data' => "array", 'message' => "array", 'redirect_to' => ""])]
    public function getResponseData($encode = true): array
    {
        $responseData = [
            'result'  => $this->result,
            'data'    => $this->data,
            'message' => $this->message,
        ];

        if ($this->redirect_to) {
            $responseData['redirect_to'] = $this->redirect_to;
        }

        if (!$this->result) {
            $responseData['errors'] = $this->errors ?? ['Falha na requisição'];
        }

        if($encode){
            try{
                return mb_convert_encoding($responseData, 'UTF-8');
            }catch (\Exception $e){
                return $responseData;
            }
        }

        return $responseData;
    }

    public function toJson(): JsonResponse
    {
        return Response::json($this->getResponseData(), $this->status);
    }
}
