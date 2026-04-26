<?php

namespace Modules\Admin\DTO;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class AdminDto
{
    public string $name;
    public string $email;
    public string $password;
    public ?UploadedFile $image;
    public int $is_active;

    public function __construct(Request $request)
    {
        $this->name = $request->input('name');
        $this->email = $request->input('email');
        $this->password = Hash::make($request->input('password'));
        $this->image = $request->file('image');
        $this->is_active = $request->has('is_active') ? 1 : 0;
    }

    public function dataFromRequest(): array
    {
        $data = json_decode(json_encode($this), true);

        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
