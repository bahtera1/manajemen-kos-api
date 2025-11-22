<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePenghuniRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Ambil ID penghuni dari route untuk pengecualian unique
        $penghuniId = $this->route('penghuni') ? $this->route('penghuni')->id : null;

        return [
            'no_ktp' => 'required|string|max:17|unique:penghunis,no_ktp,' . $penghuniId,
            'nama_lengkap' => 'required|string|max:150',
            'no_hp' => 'required|string|max:16',
            'pic_emergency' => 'required|string|max:150',
            'tanggal_masuk' => 'required|date',
            'email' => 'nullable|email|max:150',
            'kamar_id' => 'required|exists:kamars,id',
            'pekerjaan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ];
    }
}
