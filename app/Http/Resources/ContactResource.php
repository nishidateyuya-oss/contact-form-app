<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $genderMap = [1 => '男性', 2 => '女性', 3 => 'その他'];

        return [
            'id' => $this->id,
            'first_name' => $this->first_name, // 苗字
            'last_name' => $this->last_name,  // 下の名前
            'gender' => $this->gender,
            'gender_label' => $genderMap[$this->gender] ?? '不明',
            'email' => $this->email,
            'tel' => $this->tel,
            'address' => $this->address,
            'building' => $this->building,
            'detail' => $this->detail,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),

            // Eager Loadingされたリレーション先の整形
            'category' => $this->category ? [
                'id' => $this->category->id,
                'content' => $this->category->content,
            ] : null,

            'tags' => $this->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            }),
        ];
    }
}
