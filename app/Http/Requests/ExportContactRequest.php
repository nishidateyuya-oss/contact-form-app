<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 検索・フィルタ用クエリ（空欄でも全件取得できるよう nullable）
            'keyword' => 'nullable|string|max:255',
            'gender' => 'nullable|integer|in:0,1,2,3',
            'category_id' => 'nullable|integer|exists:categories,id',
            'date' => 'nullable|date_format:Y-m-d',

            // 【条件5】 CSV出力項目に対応するバリデーション
            'id' => 'nullable|integer',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'tel' => 'nullable|string|regex:/^[0-9]{10,11}$/',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'detail' => 'nullable|string|max:255',
            'created_at' => 'nullable|date',
        ];
    }
}
