<?php

namespace Modules\Iblog\Http\Requests;

use Imagina\Icore\Http\Request\CoreFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Imagina\Icore\Http\Rules\UniqueSlugRule;


class CreateCategoryRequest extends CoreFormRequest
{
    public function rules(): array
        {
            return [];
        }

        public function translationRules(): array
        {
            return [
                'title' => 'required|min:1',
                'slug' => ['required', new UniqueSlugRule('iblog__category_translations', null, null,
                    itrans('iblog::category.messages.sameSlug', ['slug' => $this->input(app()->getLocale() . '.slug')])), 'min:1', "alpha_dash:ascii"],
                'description' => 'min:1',
            ];
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
            return [
                'title.required' => itrans('iblog::category.messages.titleIsRequired'),
                'title.min:1' => itrans('iblog::category.messages.titleMin2'),
                // slug
                'slug.required' => itrans('iblog::category.messages.slugIsRequired'),
                'slug.min:1' => itrans('iblog::category.messages.slugMin2'),

                // description
                'description.required' => itrans('iblog::category.messages.descriptionIsRequired'),
                'description.min:1' => itrans('iblog::category.messages.descriptionMin2'),
            ];
        }

        public function getValidator(): Validator
        {
            return $this->getValidatorInstance();
        }

}
