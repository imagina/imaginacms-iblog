<?php

namespace Modules\Iblog\Http\Requests;

use Imagina\Icore\Http\Request\CoreFormRequest;
use Illuminate\Contracts\Validation\Validator;

class CreatePostRequest extends CoreFormRequest
{
    public function rules(): array
        {
            return [
                'category_id' => 'required',
            ];
        }

        public function translationRules(): array
        {
            return [
                'title' => 'required|min:1',
                'summary' => 'required|min:1',
                'slug' => ['required', 'min:1', "alpha_dash:ascii"],
                'description' => 'required|min:1',
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
                'title.required' => itrans('iblog::post.messages.titleIsRequired'),
                'title.min:1' => itrans('iblog::post.messages.titleMin2'),
                'summary.required' => itrans('iblog::post.messages.summaryIsRequired'),
                'summary.min:1' => itrans('iblog::post.messages.summaryMin2'),
                'description.required' => itrans('iblog::post.messages.descriptionIsRequired'),
                'description.min:1' => itrans('iblog::post.messages.descriptionMin2'),
                // slug
                'slug.required' => itrans('iblog::post.messages.slugRequired'),
                'slug.min:1' => itrans('iblog::post.messages.slugMin2'),
            ];
        }

        public function getValidator(): Validator
        {
            return $this->getValidatorInstance();
        }

}
