<?php

namespace Modules\Iblog\Http\Requests;

use Imagina\Icore\Http\Request\CoreFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Imagina\Icore\Http\Rules\DeleteFunctionRule;


class DeleteCategoryRequest extends CoreFormRequest
{
    public function rules(): array
    {
        return [
            'id' => [new DeleteFunctionRule('iblog__post_category', $this->id, 'category_id', itrans('iblog::common.messages.deleteValidation'), 'Modules\Iblog\Repositories\PostRepository', 'post_id')]
        ];
    }

    public function translationRules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [];
    }

    public function translationMessages(): array
    {
        return [];
    }

    public function getValidator(): Validator
    {
        return $this->getValidatorInstance();
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            'id' => $this->query('id'),
        ]);
    }

}
